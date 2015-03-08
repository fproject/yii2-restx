<?php
///////////////////////////////////////////////////////////////////////////////
//
// Licensed Source Code - Property of f-project.net
//
// © Copyright f-project.net 2015. All Rights Reserved.
//
///////////////////////////////////////////////////////////////////////////////

namespace fproject\rest;

use fproject\components\DbHelper;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\rest\Action;

/**
 * BatchSaveAction implements the API endpoint for batch-saving (inserting or updating) models.
 *
 * @author Bui Sy Nguyen <nguyenbs@f-project.net>
 */
class BatchSaveAction extends Action
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;


    /**
     * Saves or updates a model according to the primary key values.
     * @return \yii\db\ActiveRecordInterface the model being updated
     */
    public function run()
    {
        $modelArr = Yii::$app->getRequest()->getBodyParams();
        $models = [];
        foreach($modelArr as $ma)
        {
            /* @var $model ActiveRecord */
            $model = new $this->modelClass([
                'scenario' => $this->scenario,
            ]);
            $model->setAttributes($ma, false);
            $models[] = $model;
        }

        return DbHelper::batchSave($models);
    }
}
