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
    use SaveActionTrait;

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

        $attributes = $this->getSavingFieldsFromRequest();
        if(is_null($attributes))
            $attributes = [];

        return DbHelper::batchSave($models, $attributes);
    }
}
