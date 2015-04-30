yii2-restx
===========

Extended Yii2 Framework RESTful for F-Project server sites 


What is it?
-----------

#### ActiveController

Yii2-restx provides provides a class fproject\rest\ActiveController which extends yii\rest\ActiveController with
additional REST endpoints and methods in order to work with F-Project framework Flex clients.

By default, the following actions are supported:

- `index`: list of models. Support query criteria, pagination and sorting.
- `view`: return the details of a model
- `create`: create a new model
- `update`: update an existing model
- `delete`: delete an existing model
- `options`: return the allowed HTTP methods
- `save`: save (update or insert) a model
- `batch-save`: batch-save (update or insert) models
- `batch-remove`: batch-delete existing models

You may disable some of these actions by overriding {{actions()}} and unsetting the corresponding actions.

To add a new action, either override {{actions()}} by appending a new action class or write a new action method.
Make sure you also override {{verbs()}} to properly declare what HTTP methods are allowed by the new action.

You should usually override {{checkAccess()}} to check whether the current user has the privilege to perform
the specified action against the specified model.

#### UrlRule

Yii2-RESTx also provides fproject\rest\UrlRule to config URL rule for active controllers to accept extended routes.

Class UrlRule is provided to simplify the creation of URL rules for RESTful API support.

The simplest usage of UrlRule is to declare a rule like the following in the application configuration,

```php
[
    'class' => 'fproject\rest\UrlRule',
    'controller' => 'user',
]
```

The above code will create a whole set of URL rules supporting the following RESTful API endpoints:

- `'PUT,PATCH users/<id>' => 'user/update'`: update a user
- `'DELETE users/<id>' => 'user/delete'`: delete a user
- `'GET users/remove/<id>' => 'user/delete'`: delete a user
- `'GET,HEAD users/<id>' => 'user/view'`: return the details/overview/options of a user
- `'POST users' => 'user/create'`: create a new user
- `'POST users/save' => 'user/save'`: save a user
- `'POST users/batch-save' => 'user/batchSave'`: save a user
- `'GET,HEAD users' => 'user/index'`: return a list/overview/options of users
- `'users/<id>' => 'user/options'`: process all unhandled verbs of a user
- `'users' => 'user/options'`: process all unhandled verbs of user collection

You may configure [[only]] and/or [[except]] to disable some of the above rules.
You may configure [[patterns]] to completely redefine your own list of rules.
You may configure [[controller]] with multiple controller IDs to generate rules for all these controllers.
For example, the following code will disable the `delete` rule and generate rules for both `user` and `post` controllers:

```php
[
    'class' => 'fproject\rest\UrlRule',
    'controller' => ['user', 'post'],
    'except' => ['delete'],
]
```

The property [[controller]] is required and should represent one or multiple controller IDs.
Each controller ID should be prefixed with the module ID if the controller is within a module.
The controller ID used in the pattern will be automatically pluralized (e.g. `user` becomes `users`
as shown in the above examples).

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

    composer.phar require fproject/yii2-restx:"*"

Usage
-----
- In your Yii configuration file, use _fproject\rest\UrlRule_ instead of _yii\rest\UrlRule_
 ```
 'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'rules' => [
                ['class' => 'fproject\rest\UrlRule', 'controller' => 'user'],
            ],
        ]
 ```

- Let your controller extends _fproject\rest\ActiveController_ instead of _yii\rest\ActiveController_
 ```
 class UserController extends \fproject\rest\ActiveController{
    public $modelClass = 'app\models\User';
 }
 ```
 
 
Links
-----

- [GitHub](https://github.com/fproject/yii2-restx)
- [Packagist](https://packagist.org/packages/fproject/yii2-restx)
