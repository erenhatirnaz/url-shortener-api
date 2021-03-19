# URL Shortener API
> A basic level URL Shortener API written in PHP/Laravel

Users can login/register and create shortcuts for the url. There's no user
interface, it's just RESTful API. API provides Personal Access Tokens to users.

## Resources
### [POST] `/api/register`
* ***Authentication Required:*** No.
* **Required fields:** `email` and `password`.

Accepts `email` and `password` and creates a new user with given information If
the given email address isn't in the database. Finally, creates and returns a
Personal Access Token belonging to the user.

### [POST] `/api/login`
* ***Authentication Required:*** No.
* **Required fields:** `email` and `password`.

Accepts `email` and `password`. If the given credentials are true, creates and
returns a new Personal Access Token belonging to the user. If the given
credentials are not correct, returns an error message.

### TODO [POST] `/api/logout`
* **Authentication Required:** Yes.

Deletes the current Personal Access Token from the database of the authenticated
user.

### TODO [GET] `/api/shortcuts`
* **Authentication Required:** Yes.

Returns a list of Shortcut resource representations created by authenticated
user. The list includes paging information.

### TODO [POST] `/api/shortcuts`
* **Authentication Required:** Yes.
* **Required field:** `url`
* ***Optional field:*** `shortcut`. If it's not given, a random 5-character
  string will be generated. **It's not case-sensitive**.

Accepts `url` and optional `shortcut` and creates a Shortcut resourse. Returns
the information of the created Shortcut. Some keywords have been blocked for
security reasons not to be used as shortcuts.

**Blocked keywords**
```txt
api
login
register
forgot
forgot-password
about
admin
index
homepage
```
### TODO [GET] `/api/shortcuts/{shortcut}`
* **Authentication Required:** Yes.
* **Required fields:** `shortcut`

Returns the informations of the Shortcut resource by the `shortcut`.

### TODO [PUT] `/api/shortcuts/{shortcut}`
* **Authentication Required:** Yes.
* **Required fields:** `shortcut`, `url`

Accepts `shortcut` and `url` and updates the `url` of the `shortcut`.

### TODO [DELETE] `/api/shortcuts/{shortcut}`
* **Authentication Required:** Yes.
* **Required fields:** `shortcut`

Deletes Shortcut resource by the given `shortcut`.