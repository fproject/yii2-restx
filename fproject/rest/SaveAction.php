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

namespace fproject\rest;

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
     * @throws \yii\base\InvalidConfigException
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

        if(!$this->loadModel($model, $data))
        {
            throw new ServerErrorHttpException('Failed to save the model: invalid data');
        }

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        if(array_key_exists("_isInserting", $data) && $model->hasProperty("_isInserting"))
        {
            $isNew = (bool)$bodyData["_isInserting"];
        }
        else
        {
            $keys = $model->getPrimaryKey(true);
            $isNew = false;
            foreach($keys as $name=>$value)
            {
                if(empty($bodyData[$name]))
                {
                    $isNew = true;
                    break;
                }
            }
        }

        if($isNew)
        {
            $model->setOldAttributes(null);
        }
        else
        {
            if(!isset($keys))
                $keys = $model->getPrimaryKey(true);
            $model->setOldAttributes($keys);
        }

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        if ($model->save(true, $attributes) === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to save the object for unknown reason.');
        }

        return $model->getPrimaryKey();
    }
}
