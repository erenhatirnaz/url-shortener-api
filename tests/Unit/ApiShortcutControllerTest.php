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

    public function testStoreShouldCreateShortcutSuccessfully()
    {
        $shortcut = [
            'url' => "https://duckduckgo.com",
            'shortcut' => "ddg",
        ];

        $response = $this->json('post', 'api/shortcuts', $shortcut);

        $response->assertJsonFragment($shortcut)
                 ->assertJsonStructure(['id', 'shortcut', 'url', 'created_at', 'updated_at'])
                 ->assertStatus(Response::HTTP_CREATED);
    }

    public function testStoreShouldGenerateRandomShortcutIfItsNotGiven()
    {
        $shortcut = ['url' => "https://duckduckgo.com"];

        $response = $this->json('post', 'api/shortcuts', $shortcut);

        $response->assertJsonFragment($shortcut)
                 ->assertJsonStructure(['id', 'shortcut', 'url', 'created_at', 'updated_at'])
                 ->assertStatus(Response::HTTP_CREATED);
    }

    public function testStoreShouldReturnErrorIfTheGivenRequestDataIsNotValid()
    {
        $this->json('post', 'api/shortcuts', ["url" => ""])
             ->assertExactJson([
                 'message' => "The given data was invalid.",
                 'errors' => ["url" => ["The url field is required."]],
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->json('post', 'api/shortcuts', ["url" => "https://duckduckgo.com", 'shortcut' => ""])
             ->assertExactJson([
                 'message' => "The given data was invalid.",
                 'errors'  => ["shortcut" => ["The shortcut must be a string.", "The shortcut must only contain letters and numbers."]]
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->json('post', 'api/shortcuts', ["url" => "https://duckduckgo.com", 'shortcut' => "admin"])
             ->assertExactJson([
                 'message' => "The given data was invalid.",
                 'errors'  => ["shortcut" => ["The selected shortcut is invalid."]]
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testShowShoulReturnShortcutInformation()
    {
        $shortcut = Shortcut::factory()->create();

        $response = $this->json('get', "api/shortcuts/" . $shortcut->shortcut);

        $response->assertExactJson($shortcut->toArray())
                 ->assertStatus(Response::HTTP_OK);
    }

    public function testShowShouldReturnNotFoundErrorIfGivenShortcutDoesntExist()
    {
        $this->json('get', "api/shortcuts/foobar")
             ->assertExactJson(['message' => "The resource has not been found!", 'code' => Response::HTTP_NOT_FOUND])
             ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateShouldChangeTheUrlOfTheGivenShortcut()
    {
        $shortcut = Shortcut::factory()->create(['user_id' => $this->user->id]);
        $fields = ['url' => "http://duckduckgo.com"];

        $response = $this->json('put', "api/shortcuts/" . $shortcut->shortcut, $fields);

        $response->assertJsonFragment($fields)
                 ->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateShouldReturnActionUnauthorizedErrorIfTheGivenShortcutDoesntBelongToTheUser()
    {
        $shortcut = Shortcut::factory()->create();
        $fields = ['url' => "http://foobarbaz.com"];

        $response = $this->json('put', "api/shortcuts/" . $shortcut->shortcut, $fields);

        $response->assertExactJson([
            'message' => "This action is unauthorized! This shortcut does not belong to you.",
            'code' => Response::HTTP_FORBIDDEN,
        ])->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
