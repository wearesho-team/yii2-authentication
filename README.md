# Yii2 Authentication
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

return [
    'controllerMap' => [
        'auth' => [
            'class' => Authentication\Controller::class, 
            'identityClass' => YourIdentityClass::class,
        ],
    ],
];
```
### HTTP Routes

TODO: Write HTTP routes docs

## License
[MIT](./LICENSE)
