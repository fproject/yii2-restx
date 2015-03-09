yii2-restx
===========

Extended RESTful for Yii2 Framework


What is it?
-----------

Yii2-restx provides provides a class fproject\rest\ActiveController which extends yii\rest\ActiveController with additional REST endpoints and methods
It also provides fproject\rest\UrlRule to config URL rule for active controllers to accepts extended routes.

By default, the following actions are supported:

- `index`: list of models. Support query criteria, pagination and sorting.
- `view`: return the details of a model
- `create`: create a new model
- `update`: update an existing model
- `delete`: delete an existing model
- `options`: return the allowed HTTP methods
- `save`: save (update or insert) a model
- `batchSave`: batch-save (update or insert) models
- `batchDelete`: batch-delete existing models
You may disable some of these actions by overriding [[actions()]] and unsetting the corresponding actions.

To add a new action, either override [[actions()]] by appending a new action class or write a new action method.
Make sure you also override [[verbs()]] to properly declare what HTTP methods are allowed by the new action.

You should usually override [[checkAccess()]] to check whether the current user has the privilege to perform
the specified action against the specified model.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

    composer.phar require fproject/yii2-restx:"*"

Usage
-----

 ```
 [
     'class' => 'fproject\rest\UrlRule',
     'controller' => 'user',
 ]
 ```
 
Links
-----

- [GitHub](https://github.com/fproject/yii2-restx)
- [Packagist](https://packagist.org/packages/fproject/yii2-restx)
