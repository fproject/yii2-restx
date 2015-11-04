<?php
namespace tests\codeception\unit\fixtures;

use yii\test\ActiveFixture;

class ItemFixture extends ActiveFixture
{
    public $modelClass = 'tests\codeception\unit\models\User';
    public $dataFile = '@tests/codeception/unit/fixtures/data/items.php';
}