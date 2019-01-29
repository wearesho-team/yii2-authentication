# Yii2 Authentication
[![Build Status](https://travis-ci.org/wearesho-team/yii2-authentication.svg?branch=master)](https://travis-ci.org/wearesho-team/yii2-authentication)
[![codecov](https://codecov.io/gh/wearesho-team/yii2-authentication/branch/master/graph/badge.svg)](https://codecov.io/gh/wearesho-team/yii2-authentication)

Simple Yii2 library to authenticate API users and generate
authorization tokens using [wearesho-team/yii2-authorization](https://github.com/wearesho-team/yii2-authorization)

## Installation
Using [composer](https://packagist.org)
```bash
composer require wearesho-team/yii2-authentication:^1.0
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

### Two factor authorization

You can use [Controller](./src/TwoFactor/Controller.php) that provides two factor authentication.
You need to pass Token\Repository and TokenGeneratorInterface in addition to base controller settings

```php
<?php

use Wearesho\Yii2\Authentication;
use Wearesho\Yii2\Authorization;
use Wearesho\Yii2\Token;

return [
    'controllerMap' => [
        'auth' => [
            'class' => Authentication\TwoFactor\Controller::class, 
            'identityClass' => YourIdentityClass::class,
            'repository' => Authorization\Repository::class,
            'tokenRepository' => Token\Repository::class,
            'tokenGenerator' => YourImplementedTokenGenerator::class,
        ],
    ],
];
```  

After confirmation token is being created, an [event](./src/TwoFactor/Events/Create.php)
EVENT_AFTER_CREATE will be triggered in [LoginForm](./src/TwoFactor/LoginForm.php).
You can add listeners to this event to implement custom logic of token delivery.

```php
<?php

// bootstrap.php

\yii\base\Event::on(\Wearesho\Yii2\Authentication\TwoFactor\LoginForm::EVENT_AFTER_CREATE, function ($event) {
    $tokenValue = $event->getValue();
    // custom logic
});
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

- Response 205 - Current token is being deleted and new one is created

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

#### Two Factor Controller

##### POST

This action is used to check passed credentials and generate confirmation token for two factor authentication

- Body params
```json
{
  "LoginForm": {
    "login": "login value",
    "password": "password value"
  }
}
```

- Response 202 - When first factor is completed.
You will receive hash, that should be passed into the second step request

```json
{
  "hash": "hash to identify created token in second step"
}
```

- Response 400 - When something went wrong

##### PATCH

This action is used to confirm authentication with token value

- Body params
```json
{
  "ConfirmForm": {
    "hash": "hash value that has been returned in first stage",
    "value": "filled token value"
  }
}
```

+ Response 202 - When authentication is completed 

```json
{
  "id": "returned user id, integer value",
  "access": "access token",
  "refresh": "refresh token"
}
```

+ Response 400 - Required params are missing or invalid

+ Response 409 - When hash and token were correct, but token owner was not found by system

## License
[MIT](./LICENSE)
