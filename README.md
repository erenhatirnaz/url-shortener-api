# URL Shortener API
> A basic level URL Shortener API written in PHP/Laravel

Users can login/register and create shortcuts for the url. There's no user
interface, it's just RESTful API. API provides Personal Access Tokens to users.

## Usage
### Requirements
* PHP 7.3 or higher
* Composer

### Install dependencies
To install PHP dependencies:
```console
$ composer install
```

### Configuration
First of all, rename `.env.example` file to `.env` and fill the variables and
edit the file according to you.

Next, re-generate the application key:
```console
$ php artisan key:generate
```

Next, migrate the database:
```console
$ php artisan migrate
```

Next, insert a client to generate personal access token:
```console
$ php artisan passport:client --personal
```

### Starting up server
To serve the application:
```console
$ php artisan serve
```
and now the application runs on **localhost:8000**.

## Resources
### [POST] `/api/register`
* ***Authentication Required:*** No.
* **Required fields:** `email` and `password`.

Accepts `email` and `password` and creates a new user with given information If
the given email address isn't in the database. Finally, creates and returns a
Personal Access Token belonging to the user.

**Example request (curl):**
```console
$ curl --request POST \
  --url http://localhost:8000/api/register \
  --header 'Content-Type: application/json' \
  --data '{"email": "foo@bar.com", "password": "password123"}'
```

*Response:*
```json
{
  "accessToken": "..."
}
```

### [POST] `/api/login`
* ***Authentication Required:*** No.
* **Required fields:** `email` and `password`.

Accepts `email` and `password`. If the given credentials are true, creates and
returns a new Personal Access Token belonging to the user. If the given
credentials are not correct, returns an error message.

**Example request (curl):**
```console
$ curl --request POST \
  --url http://localhost:8000/api/login \
  --header 'Content-Type: application/json' \
  --data '{"email": "foo@bar.com", "password": "password123"}'
```

*Response:*
```json
{
  "accessToken": "..."
}
```

### [GET] `api/user`
* **Authentication Required:** Yes.

Returns the authenticated user's information (except `password` and `remember_token`).

**Example request (curl):**
```console
$ curl --request GET \
  --url http://localhost:8000/api/user \
  --header 'Authorization: Bearer [INSERT_ACCESS_TOKEN_HERE]'
```

*Response*:
```json
{
  "id": 1,
  "email": "foo@bar.com",
  "email_verified_at": null,
  "created_at": "2021-03-19T13:50:45.000000Z",
  "updated_at": "2021-03-19T13:50:45.000000Z"
}
```

### [GET] `/api/shortcuts`
* **Authentication Required:** Yes.

Returns a list of Shortcut resource representations created by authenticated
user. The list includes paging information.

**Example requrest (curl):**
```console
$ curl --request GET \
  --url http://localhost:8000/api/shortcuts \
  --header 'Authorization: Bearer [INSERT_ACCESS_TOKEN_HERE]'
```

*Response:*
```json
{
  "data": [
    {
      "id": 1,
      "shortcut": "IyN6C",
      "url": "http://www.douglas.com/veritatis-doloribus-consequatur-labore-fuga-quia-fugiat-quas-libero",
      "created_at": "2021-03-19T21:50:55.000000Z",
      "updated_at": "2021-03-19T21:50:55.000000Z"
    },
    ...
  ],
  "links": {
    "first": "http://localhost:8000/api/shortcuts?page=1",
    "last": "http://localhost:8000/api/shortcuts?page=2",
    "prev": null,
    "next": "http://localhost:8000/api/shortcuts?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://localhost:8000/api/shortcuts?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": "http://localhost:8000/api/shortcuts?page=2",
        "label": "2",
        "active": false
      },
      {
        "url": "http://localhost:8000/api/shortcuts?page=2",
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "path": "http://localhost:8000/api/shortcuts",
    "per_page": 15,
    "to": 15,
    "total": 20
  }
}
```

### [POST] `/api/shortcuts`
* **Authentication Required:** Yes.
* **Required field:** `url`
* ***Optional field:*** `shortcut`. If it's not given, a random 5-character
  string will be generated. **It's not case-sensitive**.

Accepts `url` and optional `shortcut` and creates a Shortcut resourse. Returns
the information of the created Shortcut. Some keywords have been reserved for
security reasons not to be used as shortcuts. You can customize reserved
keywords in the `config/shortcuts.php` file.

**Example requrest (curl):**
```console
$ curl --request POST \
  --url http://localhost:8000/api/shortcuts \
  --header 'Authorization: Bearer [INSERT_ACCESS_TOKEN_HERE]' \
  --header 'Content-Type: application/json' \
  --data '{"url": "http://duckduckgo.com", "shortcut": "ddg"}'
```

*Response:*
```json
{
  "shortcut": "ddg",
  "url": "http:\/\/duckduckgo.com",
  "updated_at": "2021-03-20T21:37:03.000000Z",
  "created_at": "2021-03-20T21:37:03.000000Z",
  "id": 21
}
```

### [GET] `/api/shortcuts/{shortcut}`
* **Authentication Required:** Yes.
* **Required fields:** `shortcut`

Returns the informations of the Shortcut resource by the `shortcut`.

**Example request (curl):**
```console
$ curl --request GET \
  --url http://localhost:8000/api/shortcuts/ddg \
  --header 'Authorization: Bearer [INSERT_ACCESS_TOKEN_HERE]'
```

*Response:*
```json
{
  "id": 21,
  "shortcut": "ddg",
  "url": "http:\/\/duckduckgo.com",
  "created_at": "2021-03-20T21:37:03.000000Z",
  "updated_at": "2021-03-21T11:29:57.000000Z"
}
```

### [PUT] `/api/shortcuts/{shortcut}`
* **Authentication Required:** Yes.
* **Required fields:** `shortcut`, `url`

Accepts `shortcut` and `url` and updates the `url` of the `shortcut`. **Changing
the shortcut value is not allowed.**

**Example request (curl):**
```console
$ curl --request PUT \
  --url http://localhost:8000/api/shortcuts/ddg \
  --header 'Authorization: Bearer [INSERT_ACCESS_TOKEN_HERE]' \
  --data '{"url": "http://duckduckgo.com/deneme"}'
```

*Response:*
```json
{
  "id": 21,
  "shortcut": "ddg",
  "url": "http:\/\/duckduckgo.com\/deneme",
  "created_at": "2021-03-20T21:37:03.000000Z",
  "updated_at": "2021-03-21T11:30:31.000000Z"
}
```

### [DELETE] `/api/shortcuts/{shortcut}`
* **Authentication Required:** Yes.
* **Required fields:** `shortcut`

Deletes Shortcut resource by the given `shortcut`.

**Example request(curl):**
```console
$ curl --request DELETE \
  --url http://localhost:8000/api/shortcuts/ddg \
  --header 'Authorization: Bearer [INSERT_ACCESS_TOKEN_HERE]'
```

*Response:*
```json
{
  "deleted": true,
  "shortcut": "ddg"
}
```

## License

> url-shortener-api
> Copyright (C) 2021  Eren Hatirnaz

> This program is free software: you can redistribute it and/or modify
> it under the terms of the GNU General Public License as published by
> the Free Software Foundation, either version 3 of the License, or
> (at your option) any later version.

> This program is distributed in the hope that it will be useful,
> but WITHOUT ANY WARRANTY; without even the implied warranty of
> MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
> GNU General Public License for more details.

> You should have received a copy of the GNU General Public License
> along with this program.  If not, see <https://www.gnu.org/licenses/>