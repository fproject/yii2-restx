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
use yii\db\ActiveRecordInterface;
use yii\web\NotFoundHttpException;

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
     * @var bool Use secure searching condition. This will prevent client request directly
     * use WHERE condition as the request param to filter searching result.
     *
     * This will help us prevent SQL injection vulnerability.
     */
    public $useSecureSearch = true;

    /**
     * @var array The condition map contains pre-defined SQL conditions and expand (relation) definition for client query
     * For example:
     * [
     *    'findByUser_condition'=>'name LIKE :userName',
     *    'findByUser_expand_resource'=>['select'=>false,'condition'=>'resource.userId=:userId'],
     * ]
     * The conditionMap also can be created by inline function.
     *
     * This field is ignored if the $useSecureSearch is set to false.
     */
    public $conditionMap = [];

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

        //Config 'findModel' callback for actions
        foreach($actions as $key=>$action)
        {
            $actions[$key]['findModel'] = [$this, 'findModel'];
        }

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

    /**
     * Returns the data model based on the primary key given.
     * If the data model is not found, a 404 HTTP exception will be raised.
     * @param string|array $id the ID of the model to be loaded. If the model has a composite primary key,
     * the ID must be a string of the primary key values separated by commas.
     * The order of the primary key values should follow that returned by the `primaryKey()` method
     * of the model.
     * @return ActiveRecordInterface the model found
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        if(is_string($id) && !preg_match('/^\d[\d,]*$/', $id))
        {
            $id = json_decode($id, true);
        }
        /* @var $modelClass ActiveRecordInterface */
        $modelClass = $this->modelClass;
        $keys = $modelClass::primaryKey();
        if (count($keys) > 1) {
            if(is_array($id))
            {
                $model = $modelClass::findOne($id);
            }
            else
            {
                $values = explode(',', $id);
                if (count($keys) === count($values)) {
                    $model = $modelClass::findOne(array_combine($keys, $values));
                }
            }
        } elseif ($id !== null) {
            $model = $modelClass::findOne($id);
        }

        if (isset($model)) {
            return $model;
        } else {
            if(is_array($id) || is_object($id))
                $id = json_encode($id);
            throw new NotFoundHttpException("Object not found: $id");
        }
    }
}