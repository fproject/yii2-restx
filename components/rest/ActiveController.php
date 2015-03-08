<?php
///////////////////////////////////////////////////////////////////////////////
//
// Licensed Source Code - Property of f-project.net
//
// © Copyright f-project.net 2015. All Rights Reserved.
//
///////////////////////////////////////////////////////////////////////////////
/**
 * ActiveController implements a common set of actions for supporting RESTful access to ActiveRecord.
 *
 * The class of the ActiveRecord should be specified via [[modelClass]], which must implement [[\yii\db\ActiveRecordInterface]].
 * By default, the following actions are supported:
 *
 * - `index`: list of models
 * - `view`: return the details of a model
 * - `create`: create a new model
 * - `update`: update an existing model
 * - `delete`: delete an existing model
 * - `options`: return the allowed HTTP methods
 * - `save`: save (update or insert) a model
 * - `batchSave`: batch-save (update or insert) models
 * - `batchDelete`: batch-delete existing models
 * You may disable some of these actions by overriding [[actions()]] and unsetting the corresponding actions.
 *
 * To add a new action, either override [[actions()]] by appending a new action class or write a new action method.
 * Make sure you also override [[verbs()]] to properly declare what HTTP methods are allowed by the new action.
 *
 * You should usually override [[checkAccess()]] to check whether the current user has the privilege to perform
 * the specified action against the specified model.
 *
 * @author Bui Sy Nguyen <nguyenbs@f-project.net>
 */
namespace app\components\rest;


use yii\base\Model;

class ActiveController extends \yii\rest\ActiveController
{
    /**
     * @var string the scenario used for saving a model.
     * @see \yii\base\Model::scenarios()
     */
    public $saveScenario = Model::SCENARIO_DEFAULT;

    /**
     * @var string the scenario used for batch-saving models.
     * @see \yii\base\Model::scenarios()
     */
    public $batchSaveScenario = Model::SCENARIO_DEFAULT;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(),[
            'save' => [
                'class' => 'app\components\rest\SaveAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->saveScenario,
            ],
            'batch-save' => [
                'class' => 'app\components\rest\BatchSaveAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->batchSaveScenario,
            ],
            'batch-remove' => [
                'class' => 'app\components\rest\BatchRemoveAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return array_merge(parent::verbs(),[
            'save' => ['POST'],
            'batch-save' => ['POST'],
        ]);
    }
}