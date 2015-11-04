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
use Yii;
use yii\codeception\TestCase;

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
}
