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

use fproject\rest\BatchSaveAction;
use tests\codeception\unit\models\base\UserDepartmentAssignment;
use tests\codeception\unit\models\User;
use fproject\rest\BatchRemoveAction;
use Yii;
use yii\codeception\TestCase;
use \Codeception\Specify;

class BatchSaveActionTest extends TestCase
{
	use Specify;

    public function testBatchSaveForSinglePrimaryKey001()
    {
        $users = [];

        $u = new User();
        $u->username = "Jack 001";

        $users[] = $u;

        $u = new User();
        $u->username = "Jack 002";

        $users[] = $u;

        Yii::$app->request->setBodyParams($users);

        $this->specify('Remove a AR with single primary key', function () {
            $action = new BatchSaveAction("batch-save", null, ['modelClass'=>'tests\codeception\unit\models\User']);
            $ret = $action->run();

            expect("Number of inserted records should be 2: ", $ret->insertCount)->equals(2);
            $lastID =$ret->lastId;
            expect("LastID must > 0: ", $lastID)->greaterThan(0);
            $u = User::findOne(['id'=>$lastID]);
            expect("Checking second record: ", $u->username)->equals('Jack 002');
            $u = User::findOne(['id'=>$lastID - 1]);
            expect("Checking first record: ", $u->username)->equals('Jack 001');
        });
    }

    public function testBatchSaveForCompositePrimaryKey001()
    {
        $departs = [];

        $depart = new UserDepartmentAssignment();
        $depart->userId = 1;
        $depart->departmentId = 2;

        $departs[] = $depart;

        $depart = new UserDepartmentAssignment();
        $depart->userId = 3;
        $depart->departmentId = 5;

        $departs[] = $depart;

        Yii::$app->request->setBodyParams($departs);

        $this->specify('Remove a AR with single primary key', function () {
            $action = new BatchSaveAction("batch-save", null, ['modelClass'=>'tests\codeception\unit\models\base\UserDepartmentAssignment']);
            $ret = $action->run();
            expect("Number of inserted records should be 2: ", $ret->insertCount)->equals(2);
            expect("LastID must > 0: ", $ret->lastId)->greaterThan(0);
        });
    }
}
