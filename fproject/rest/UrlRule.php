<?php
///////////////////////////////////////////////////////////////////////////////
//
// © Copyright f-project.net 2010-present.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
///////////////////////////////////////////////////////////////////////////////

namespace fproject\rest;

use Yii;

/**
 * UrlRule is provided to simplify the creation of URL rules for RESTful API support.
 *
 * The simplest usage of UrlRule is to declare a rule like the following in the application configuration,
 *
 * ```php
 * [
 *     'class' => 'fproject\rest\UrlRule',
 *     'controller' => 'user',
 * ]
 * ```
 *
 * The above code will create a whole set of URL rules supporting the following RESTful API endpoints:
 *
 * - `'PUT,PATCH users/<id>' => 'user/update'`: update a user
 * - `'DELETE users/<id>' => 'user/delete'`: delete a user
 * - `'GET users/remove/<id>' => 'user/delete'`: delete a user
 * - `'POST users/batch-remove' => 'user/batch-remove'`: batch-remove an array of users
 * - `'GET,HEAD users/<id>' => 'user/view'`: return the details/overview/options of a user
 * - `'POST users' => 'user/create'`: create a new user
 * - `'POST users/save' => 'user/save'`: save a user
 * - `'POST users/batch-save' => 'user/batch-save'`: save a user
 * - `'GET,HEAD users' => 'user/index'`: return a list/overview/options of users
 * - `'users/<id>' => 'user/options'`: process all unhandled verbs of a user
 * - `'users' => 'user/options'`: process all unhandled verbs of user collection
 *
 * You may configure [[only]] and/or [[except]] to disable some of the above rules.
 * You may configure [[patterns]] to completely redefine your own list of rules.
 * You may configure [[controller]] with multiple controller IDs to generate rules for all these controllers.
 * For example, the following code will disable the `delete` rule and generate rules for both `user` and `post` controllers:
 *
 * ```php
 * [
 *     'class' => 'fproject\rest\UrlRule',
 *     'controller' => ['user', 'post'],
 *     'except' => ['delete'],
 * ]
 * ```
 *
 * The property [[controller]] is required and should represent one or multiple controller IDs.
 * Each controller ID should be prefixed with the module ID if the controller is within a module.
 * The controller ID used in the pattern will be automatically pluralized (e.g. `user` becomes `users`
 * as shown in the above examples).
 *
 * @author Bui Sy Nguyen <nguyenbs@f-project.net>
 */
class UrlRule extends \yii\rest\UrlRule
{
    /** @inheritdoc */
    public $tokens = [
        '{id}' => '<id:\\d[\\d,]*|{("\\w+"\\s*:\\s*"{0,1}\\d+"{0,1}\\s*,{0,1})+\\}|>',
    ];

    /** @inheritdoc */
    public $patterns = [
        'PUT,PATCH {id}' => 'update',
        'DELETE {id}' => 'delete',
        'GET,HEAD {id}' => 'view',
        'POST' => 'create',
        'POST save' => 'save',
        'POST batch-save' => 'batch-save',
        'GET remove/{id}' => 'delete',
        'POST batch-remove' => 'batch-remove',
        'GET,HEAD' => 'index',
        '{id}' => 'options',
        '' => 'options',
    ];
}
