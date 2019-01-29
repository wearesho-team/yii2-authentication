# Two factor authentication

You can use [Controller](./src/TwoFactor/Controller.php) that provides two factor authentication.
You need to pass Token\Repository and TokenGeneratorInterface in addition to base controller settings

## Configuration

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

## API

Two-factor controller API extends base controller API with override of `POST` method and additional `PATCH` method.

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
