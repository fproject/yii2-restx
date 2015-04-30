<?php
///////////////////////////////////////////////////////////////////////////////
//
// Licensed Source Code - Property of f-project.net
//
// © Copyright f-project.net 2015. All Rights Reserved.
//
///////////////////////////////////////////////////////////////////////////////
/**
 * Class fproject\rest\ActiveController extends yii\rest\ActiveController with
 * additional REST endpoints and methods in order to work with F-Project framework Flex clients.
 * It also provides fproject\rest\UrlRule to config URL rule for active controllers to accept extended routes.
 *
 * By default, the following actions are supported:
 *
 * - `index`: list of models. Support query criteria, pagination and sorting.
 * - `view`: return the details of a model
 * - `create`: create a new model
 * - `update`: update an existing model
 * - `remove`: delete an existing model
 * - `options`: return the allowed HTTP methods
 * - `save`: save (update or insert) a model
 * - `batch-save`: batch-save (update or insert) models
 * - `batch-remove`: batch-delete existing models
 *
 * You may disable some of these actions by overriding {{actions()}} and unsetting the corresponding actions.
 *
 * To add a new action, either override {{actions()}} by appending a new action class or write a new action method.
 * Make sure you also override {{verbs()}} to properly declare what HTTP methods are allowed by the new action.
 *
 * You should usually override {{checkAccess()}} to check whether the current user has the privilege to perform
 * the specified action against the specified model.
 *
 * @author Bui Sy Nguyen <nguyenbs@f-project.net>
 */
namespace fproject\rest;


use yii\base\Model;

class ActiveController extends \yii\rest\ActiveController
{
    /**
     * @inheritdoc
     */
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

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
        $actions = array_merge(parent::actions(),[
            'save' => [
                'class' => 'fproject\rest\SaveAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->saveScenario,
            ],
            'batch-save' => [
                'class' => 'fproject\rest\BatchSaveAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->batchSaveScenario,
            ],
            'batch-remove' => [
                'class' => 'fproject\rest\BatchRemoveAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ]);

        //Override 'index' action
        $actions['index'] = [
            'class' => 'fproject\rest\IndexAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess']
        ];

        return $actions;
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        $verbs = array_merge(parent::verbs(),[
            'save' => ['POST'],
            'batch-save' => ['POST'],
        ]);

        //Override 'delete' verbs
        $verbs['delete'] = ['GET','DELETE'];

        return $verbs;
    }
}