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
class BatchRemoveAction extends Action
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;


    /**
     * Delete a list of models according to the primary key values.
     * @return int the number of deleted models
     */
    public function run()
    {
        $ids = Yii::$app->getRequest()->getBodyParams();
        /* @var $modelCls ActiveRecord */
        $modelCls = new $this->modelClass;

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $modelCls);
        }

        $pks = $modelCls::primaryKey();
        $cnt = count($pks);
        if($cnt > 1 || $cnt == 0)
        {
            return DbHelper::batchDelete($modelCls::tableName(), $ids);
        }
        else
        {
            $condition = [];
            $condition[$pks[0]] = $ids;
            return $modelCls::deleteAll($condition);
        }
    }
}
