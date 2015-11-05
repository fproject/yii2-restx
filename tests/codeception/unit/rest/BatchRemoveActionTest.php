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
namespace tests\unit\rest;

use tests\codeception\unit\models\base\UserDepartmentAssignment;
use tests\codeception\unit\models\User;
use fproject\rest\BatchRemoveAction;
use Yii;
use yii\codeception\TestCase;
use \Codeception\Specify;

class BatchRemoveActionTest extends TestCase
{
	use Specify;

    public function testBatchRemoveForSinglePrimaryKey()
    {
        $ids = [];
        for($i=0; $i < 3; $i++)
        {
            $user = new User();
            $user->username = "User testBatchRemoveForSinglePrimaryKey $i";
            $user->password = "Password testBatchRemoveForSinglePrimaryKey $i";
            $user->save(false);
            $ids[] = $user->id;
        }

        Yii::$app->request->setBodyParams($ids);

    	$this->specify('Remove a AR with single primary key', function () {
    		$action = new BatchRemoveAction("batch-remove", null, ['modelClass'=>'tests\codeception\unit\models\User']);
            $n = $action->run();
    		expect("Number of deleted records should be 3: ", $n)->equals(3);
    	});
    }

    public function testBatchRemoveForCompositePrimaryKey()
    {
        $ids = [];
        for($i=0; $i < 2; $i++)
        {
            $depart = new UserDepartmentAssignment();
            $depart->userId = $i;
            $depart->departmentId = $i * 2;
            $depart->save(false);
            $ids[] = ['userId' => $depart->userId,'departmentId'=>$depart->departmentId];
        }

        Yii::$app->request->setBodyParams($ids);

        $this->specify('Remove a AR with single primary key', function () {
            $action = new BatchRemoveAction("batch-remove", null, ['modelClass'=>'tests\codeception\unit\models\base\UserDepartmentAssignment']);
            $n = $action->run();
            expect("Number of deleted records should be 2: ", $n)->equals(2);
        });
    }
}
