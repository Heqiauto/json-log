JSON Log
========
[![Latest Stable Version](https://poser.pugx.org/heqiauto/json-log/v/stable.svg)](https://packagist.org/packages/heqiauto/json-log)
[![Total Downloads](https://poser.pugx.org/heqiauto/json-log/downloads.svg)](https://packagist.org/packages/heqiauto/json-log) 
[![Latest Unstable Version](https://poser.pugx.org/heqiauto/json-log/v/unstable.svg)](https://packagist.org/packages/heqiauto/json-log)
[![License](https://poser.pugx.org/heqiauto/json-log/license.svg)](https://packagist.org/packages/heqiauto/json-log)
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)

JSON file log for Yii2.

Requirements
-------------
+ PHP 5.5 or later.

Installation
--------------

To install the plugin, follow these instructions.

1. Open your terminal and go to your Yii2 project:

        cd /path/to/project

2. Then tell Composer to load the library:

        composer require heqiauto/json-log

3. Config your application components:

```php
[
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => \heqiauto\jsonlog\FileTarget::class,
                    'levels' => ['error', 'warning', 'info'], // Without TRACE and PROFILE on production environment
                ],
            ],
        ],
    ],
]
```

License
-------
The JSON Log is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
