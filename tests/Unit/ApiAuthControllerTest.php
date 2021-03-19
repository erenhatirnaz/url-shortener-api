<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;

class ApiAuthControllerTest extends TestCase
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

    public function testRegisterReturnErrorMessageIfGivenDataIsNotValid()
    {
        $this->json('post', 'api/register', ['email' => "", 'password' => ""])
             ->assertExactJson([
                 "errors" => [
                    "The email field is required.",
                    "The password field is required."
                 ],
                 "code" => 422
             ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->json('post', 'api/register', ['email' => "foobar"])
             ->assertExactJson([
                 "errors" => [
                    "The email must be a valid email address.",
                    "The password field is required."
                 ],
                 "code" => 422
             ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->json('post', 'api/register', ['email' => "foo@bar.com"])
             ->assertExactJson([
                 "errors" => [
                    "The password field is required."
                 ],
                 "code" => 422
             ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->json('post', 'api/register', ['email' => "foo@bar.com", "password" => "abc"])
             ->assertExactJson([
                 "errors" => [
                    "The password must be at least 6 characters."
                 ],
                 "code" => 422
             ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUserReturnAuthenticatedUserInformation()
    {
        $user = User::factory()->create();
        $user->createToken('Laravel Personal Access Client');
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
