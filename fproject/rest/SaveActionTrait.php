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
use yii\db\ActiveRecord;

/**
 * The SaveActionTrait trait represents the common method set for SaveAction and BatchSaveAction
 *
 * @author Bui Sy Nguyen <nguyenbs@f-project.net>
 */

trait SaveActionTrait {
    /**
     * Get parameter 'fields' from request.
     * This can be an JSON-encoded array or a list of fields separated by ','
     * @return array|mixed|null
     */
    public function getSavingFieldsFromRequest()
    {
        $urlParams = \Yii::$app->getRequest()->queryParams;
        if(is_array($urlParams) && isset($urlParams['fields']))
        {
            $attributes = $urlParams['fields'];
            if(is_string($attributes))
            {
                $attributes = json_decode($attributes, true);
                if(!isset($attributes))
                    $attributes = explode(',', $attributes);
            }
            if(!is_array($attributes))
                $attributes = null;
        }
        else
            $attributes = null;
        return $attributes;
    }

    /**
     * Populate model data
     * @param ActiveRecord $model the model to populate
     * @param array $modelData
     * @return bool true if success
     */
    public function loadModel($model, $modelData)
    {
        $model->trigger('beforeLoad');
        if(empty($modelData) || !is_array($modelData))
            return false;

        $attributes = $model->attributes();

        $b = false;
        foreach($attributes as $attrName)
        {
            if(array_key_exists($attrName, $modelData))
            {
                $model->setAttribute($attrName, $modelData[$attrName]);
                $b = true;
            }
        }
        $model->trigger('afterLoad');
        return $b;
    }
}