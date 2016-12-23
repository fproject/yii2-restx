<?php
///////////////////////////////////////////////////////////////////////////////
//
// � Copyright f-project.net 2010-present.
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
namespace tests\unit\rest;

use fproject\rest\ActiveController;
use tests\codeception\unit\models\base\UserDepartmentAssignment;
use Yii;
use yii\codeception\TestCase;
use yii\rest\DeleteAction;

class ActiveControllerTest extends TestCase
{
	use \Codeception\Specify;

    public function testActions001()
    {
    	$this->specify('check ActiveControllerTest\'s actions', function () {
    		$controller = new ActiveController('user', Yii::$app,
                [
                    'modelClass' => 'tests\unit\models\User'
                ]);
    		expect("controller id should be 'user'", $controller->id == 'user' )->true();
    	});
    }

    public function testRemoveForCompositePrimaryKey()
    {
        $this->specify('Remove a AR with composite primary key', function () {
            /*UserDepartmentAssignment::deleteAll(['userId' => 300,'departmentId'=>99]);
            $depart = new UserDepartmentAssignment();
            $depart->userId = 300;
            $depart->departmentId = 99;
            $depart->save(false);*/

            $controller = new ActiveController('user-department-assignments', Yii::$app,
                [
                    'modelClass' => 'tests\codeception\unit\models\base\UserDepartmentAssignment'
                ]);

            $action = new DeleteAction("remove", null, ['modelClass'=>'tests\codeception\unit\models\base\UserDepartmentAssignment']);
            $action->controller = $controller;
            $action->runWithParams(['id'=>'{"userId": 300,"departmentId" : 99}']);

            $model = UserDepartmentAssignment::findOne(['userId' => 300,'departmentId'=>99]);
            expect("The result of findOne() after deleting should be null: ", $model)->null();
        },['throws' => ['yii\web\NotFoundHttpException'] ]);
    }
}
