<?php

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
    		$controller = new ActiveController('user', Yii::$app);
    		expect("controller id should be 'user'", $controller->id == 'user' )->true();
    	});
    }
}
