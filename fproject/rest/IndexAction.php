<?php
///////////////////////////////////////////////////////////////////////////////
//
// © Copyright f-project.net 2010-present. All Rights Reserved.
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
use yii\db\ActiveQuery;
use yii\helpers\Json;

/**
 * BatchSaveAction implements the API endpoint for viewing (listing) model(s).
 *
 * @author Bui Sy Nguyen <nguyenbs@f-project.net>
 */
class IndexAction extends \yii\rest\IndexAction{
    protected function prepareDataProvider()
    {
        $dp = parent::prepareDataProvider();
        $urlParams = Yii::$app->getRequest()->queryParams;
        if(is_array($urlParams))
        {
            if(isset($urlParams['criteria']))
            {
                $criteria = Json::decode($urlParams['criteria']);
                if(is_array($criteria) && isset($criteria['condition']))
                {
                    $params = isset($criteria['params']) && is_array($criteria['params']) ? $criteria['params'] : [];

                    /** @var ActiveQuery $query */
                    $query = Yii::createObject(ActiveQuery::className(), [$this->modelClass]);
                    $query->where($criteria['condition'], $params);
                    $dp->query = $query;
                    if(isset($criteria['pagination']) && is_array($criteria['pagination']))
                    {
                        $pagination = $criteria['pagination'];
                        if(!isset($pagination['per-page']) && isset($pagination['perPage']))
                            $pagination['per-page'] = $pagination['perPage'];
                        $dp->setPagination([
                            'params'=> array_merge($urlParams, $pagination)
                        ]);
                    }
                    if(isset($criteria['sort']))
                    {
                        $dp->setSort([
                            'params'=> array_merge($urlParams, ['sort' => $criteria['sort']])
                        ]);
                        $sortEnabled = true;
                    }
                }
            }
            if(isset($sortEnabled) || isset($urlParams['sort']))
            {
                $dp->getSort()->enableMultiSort = true;
            }
        }

        return $dp;
    }
}