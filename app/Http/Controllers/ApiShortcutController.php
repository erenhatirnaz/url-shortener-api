<?php

namespace App\Http\Controllers;

use App\Models\Shortcut;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\ShortcutCollection;
use App\Http\Requests\StoreShortcutRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ApiShortcutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return new ShortcutCollection($request->user()->shortcuts()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreShortcutRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreShortcutRequest $request)
    {
        $validated = $request->validated();

        $shortcut = new Shortcut();
        $shortcut->user()->associate($request->user());
        $shortcut->shortcut = ($validated["shortcut"]) ?? Str::substr(sha1(time()), 6, 6);
        $shortcut->url = $validated["url"];
        $shortcut->save();

        return response($shortcut, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  string $shortcut
     * @return \Illuminate\Http\Response
     */
    public function show($shortcut)
    {
        try {
            $shortcut = Shortcut::where('shortcut', $shortcut)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound();
        }

        return $shortcut;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreShortcutRequest $request
     * @param  string $shortcut
     * @return \Illuminate\Http\Response
     */
    public function update(StoreShortcutRequest $request, $shortcut)
    {
        try {
            $shortcut = Shortcut::where('shortcut', $shortcut)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound();
        }

        if (! $request->user()->can('update', $shortcut)) {
            return $this->respondActionUnauthorized();
        }

        $validated = $request->validated();
        $shortcut->url = $validated["url"];
        $shortcut->save();

        return $shortcut;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string  $shortcut
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $shortcut)
    {
        try {
            $shortcut = Shortcut::where('shortcut', $shortcut)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound();
        }

        if (! $request->user()->can('delete', $shortcut)) {
            return $this->respondActionUnauthorized();
        }

        $shortcut->delete();

        return ['deleted' => true, 'shortcut' => $shortcut->shortcut];
    }

    private function respondNotFound()
    {
        return response([
            'message' => "The resource has not been found!",
            'code' => Response::HTTP_NOT_FOUND,
        ], Response::HTTP_NOT_FOUND);
    }

    private function respondActionUnauthorized()
    {
        return response([
            'message' => "This action is unauthorized! This shortcut does not belong to you.",
            'code' => Response::HTTP_FORBIDDEN,
        ], Response::HTTP_FORBIDDEN);
    }
}
