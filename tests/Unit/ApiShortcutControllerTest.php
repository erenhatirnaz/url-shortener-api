<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Shortcut;
use Illuminate\Http\Response;

class ApiShortcutControllerTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }

    public function testIndexShouldReturnAListOfShortcutCreatedByAuthenticatedUser()
    {
        Shortcut::factory()->count(20)->create(['user_id' => $this->user->id]);

        $response = $this->json('get', 'api/shortcuts');
        $response->assertJsonStructure([
            'data' => [
                [
                    "id", "shortcut", "url", "created_at", "updated_at"
                ]
            ],
            'links' => ["first", "last", "prev", "next"],
            'meta' => [ "current_page", "from", "last_page", 'links' => [["url", "label", "active"]], "path",
                        "per_page", "to", "total"],
        ])->assertStatus(Response::HTTP_OK);
    }
}
