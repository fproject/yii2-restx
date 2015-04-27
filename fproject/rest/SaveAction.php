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
use yii\web\ServerErrorHttpException;

/**
 * SaveAction implements the API endpoint for saving (inserting or updating) a model.
 *
 * @author Bui Sy Nguyen <nguyenbs@f-project.net>
 */
class SaveAction extends Action
{
    use SaveActionTrait;
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;


    /**
     * Saves or updates a model according to the primary key values.
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when inserting/updating the model
     */
    public function run()
    {
        /* @var $model ActiveRecord */
        $model = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);

        $bodyData = Yii::$app->getRequest()->getBodyParams();

        $attributes = $this->getSavingFieldsFromRequest();

        if(isset($attributes))
        {
            $data = [];
            foreach($attributes as $name)
            {
                $data[$name] = $bodyData[$name];
            }
        }
        else
            $data = $bodyData;
        $model->load($data, '');

        $keys = $model->getPrimaryKey(true);

        $isNew = false;
        foreach($keys as $name=>$value)
        {
            if(isset($bodyData[$name]))
            {
                $model->$name = $bodyData[$name];
                $keys[$name] = $bodyData[$name];
            }
            else
                $isNew = true;
        }

        if($isNew)
            $model->setOldAttributes(null);
        else
            $model->setOldAttributes($keys);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        if ($model->save(true, $attributes) === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to save the object for unknown reason.');
        }

        return $model->getPrimaryKey();
    }
}
