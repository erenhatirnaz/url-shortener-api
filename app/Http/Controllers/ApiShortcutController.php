<?php

namespace App\Http\Controllers;

use App\Models\Shortcut;
use Illuminate\Http\Request;
use App\Http\Resources\ShortcutCollection;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response([
            'message' => "Not implemented!",
            'code' => Response::HTTP_NOT_IMPLEMENTED
        ], Response::HTTP_NOT_IMPLEMENTED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shortcut  $shortcut
     * @return \Illuminate\Http\Response
     */
    public function show(Shortcut $shortcut)
    {
        return response([
            'message' => "Not implemented!",
            'code' => Response::HTTP_NOT_IMPLEMENTED
        ], Response::HTTP_NOT_IMPLEMENTED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shortcut  $shortcut
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shortcut $shortcut)
    {
        return response([
            'message' => "Not implemented!",
            'code' => Response::HTTP_NOT_IMPLEMENTED
        ], Response::HTTP_NOT_IMPLEMENTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shortcut  $shortcut
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shortcut $shortcut)
    {
        return response([
            'message' => "Not implemented!",
            'code' => Response::HTTP_NOT_IMPLEMENTED
        ], Response::HTTP_NOT_IMPLEMENTED);
    }
}
