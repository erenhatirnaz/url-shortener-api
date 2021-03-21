<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;

class AuthApiTest extends TestCase
{
    public function testRegisterReturnPersonalAccessToken()
    {
        $credentials = [
            'email' => "foo@bar.com",
            'password' => "password123",
        ];

        $this->json('post', 'api/register', $credentials)
             ->assertStatus(Response::HTTP_OK)
             ->assertJsonStructure(['accessToken']);

        $this->assertDatabaseHas('users', [
            'email' => $credentials['email'],
        ]);
    }

    public function testRegisterAndLoginReturnErrorMessagesIfRequestIsNotValid()
    {
        $resources = ['api/register', 'api/login'];

        foreach ($resources as $resource) {
            $this->json('post', $resource, ['email' => "", 'password' => ""])
             ->assertExactJson([
                 "errors" => [
                    "The email field is required.",
                    "The password field is required."
                 ],
                 "code" => 422
             ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            $this->json('post', $resource, ['email' => "foobar"])
                ->assertExactJson([
                    "errors" => [
                        "The email must be a valid email address.",
                        "The password field is required."
                    ],
                    "code" => 422
                ])
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            $this->json('post', $resource, ['email' => "foo@bar.com"])
                ->assertExactJson([
                    "errors" => [
                        "The password field is required."
                    ],
                    "code" => 422
                ])
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            $this->json('post', $resource, ['email' => "foo@bar.com", "password" => "abc"])
                ->assertExactJson([
                    "errors" => [
                        "The password must be at least 6 characters."
                    ],
                    "code" => 422
                ])
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function testLoginReturnPersonalAccessTokenIfTheGivenCredentialsIsTrue()
    {
        $user = User::factory()->create();
        $credentials = [
            'email' => $user->email,
            'password' => "password"
        ];

        $response = $this->json('post', 'api/login', $credentials);

        $response->assertJsonStructure(['accessToken'])
                 ->assertStatus(Response::HTTP_OK);
    }

    public function testLoginReturnUserNotFoundErrorIfTheGivenEmailDoesntExist()
    {
        $this->json('post', 'api/login', ["email" => "bar@baz.com", "password" => "password123"])
             ->assertExactJson(['message' => "User not found!", 'code' => Response::HTTP_NOT_FOUND])
             ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testLoginReturnPasswordMismatchErrorIfTheGivenPasswordIsntCorrect()
    {
        $user = User::factory()->create(['email' => "foo@bar.com"]);

        $this->json('post', 'api/login', ["email" => "foo@bar.com", "password" => "123456"])
             ->assertExactJson(['message' => "Password mismatch!", 'code' => Response::HTTP_UNAUTHORIZED])
             ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
    public function testUserReturnAuthenticatedUserInformation()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $this->json('get', 'api/user')
             ->assertJson(function ($json) use ($user) {
                 $json->where('id', $user->id)
                      ->where('email', $user->email);
             });
    }

    public function testUserReturnUnauthenticatedErrorIfUserNotAuthenticated()
    {
        $this->json('get', 'api/user')
             ->assertExactJson(['message' => "Unauthenticated."]);
    }
}
