# Yii2 Authentication
[![Test & Lint](https://github.com/wearesho-team/yii2-authentication/actions/workflows/php.yml/badge.svg?branch=master)](https://github.com/wearesho-team/yii2-authentication/actions/workflows/php.yml)
[![codecov](https://codecov.io/gh/wearesho-team/yii2-authentication/branch/master/graph/badge.svg)](https://codecov.io/gh/wearesho-team/yii2-authentication)

Simple Yii2 library to authenticate API users and generate
authorization tokens using [wearesho-team/yii2-authorization](https://github.com/wearesho-team/yii2-authorization)

Compatibility: tested on PHP 8.4, PHP 8.5

## Installation
Using [composer](https://packagist.org)
```bash
composer require wearesho-team/yii2-authentication:^1.4
```

## Usage
First, you need to implement [IdentityInterface](./src/IdentityInterface.php).
Then, you can use [Controller](./src/Controller.php) in your applications.

### Configuration

```php
<?php

// config.php

use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Authorization;

return [
    'controllerMap' => [
        'auth' => [
            'class' => Authentication\Controller::class, 
            'identityClass' => YourIdentityClass::class,
            'repository' => Authorization\Repository::class,
        ],
    ],
];
```

### HTTP Routes

There is only one action declared in controllers: index. It can be called using different HTTP methods.
There is a description for each action in controllers below.

#### Base controller

##### POST

Tries to login with passed credentials

- Body params
```json
{
  "LoginForm": {
    "login": "login value",
    "password": "password value"
  }
}
``` 
- Response 202 - When credentials are correct and access token is created
```json
{
  "id": "returned user id, integer value",
  "access": "access token",
  "refresh": "refresh token"
}
```

- Response 400 - When you passed invalid login/password or one of this attributes is empty
```json
{
  "errors": [
    {
      "attribute": "login",
      "details": "Login is required"
    },
    {
      "attribute": "password",
      "details": "password is invalid."
    }
  ]
}
```

##### DELETE

Action for logout

- Query params

```
?refresh=*refresh token value*
```

- Response 205 - When token is successfully deleted
```json
[]
```

- Response 400 - When passed token is invalid or empty
```json
{
  "errors": [
    {
      "attribute": "refresh",
      "details": "Refresh is required"
    }
  ]
}
```

##### PUT

This action interprets token refreshment.
Current access token will be deleted, new one will be created and returned.

- Query params:

```
?refresh=*refresh token value*
```

- Response 201 - Current token is being deleted and new one is created

```json
{
  "id": "returned user id, integer value",
  "access": "access token",
  "refresh": "refresh token"
}
```

- Response 400 - When passed token is invalid or empty
```json
{
  "errors": [
    {
      "attribute": "refresh",
      "details": "Refresh is required"
    }
  ]
}
```

## Two Factor Authentication
There is also and [implementation of two-factor authentication](./docs/TWO-FACTOR-AUTHENTICATION.md) in this library.

## License
[MIT](./LICENSE)
