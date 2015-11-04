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
use yii\db\ActiveQuery;
use yii\helpers\Json;

/**
 * IndexAction implements the API endpoint for viewing (listing) model(s).
 *
 * @property ActiveController $controller
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
                $params = $this->getParams($criteria);

                if(isset($this->controller) && $this->controller->useSecureSearch)
                {
                    $conditionKeys = $this->getConditionKeys($criteria);
                    if(isset($conditionKeys))
                    {
                        $c = $this->getConditionMapItem($conditionKeys, $params);
                        if(isset($c))
                        {
                            /** @var ActiveQuery $query */
                            if($c instanceof ActiveQuery)
                            {
                                $query = $c;
                            }
                            else
                            {
                                $query = Yii::createObject(ActiveQuery::className(), [$this->controller->modelClass]);
                                if(isset($c['condition']) || isset($c['with']) || isset($c['expand']))
                                {
                                    if(isset($c['condition']))
                                        $query->where($c['condition'], $params);
                                    if(isset($c['with']))
                                        $with = $c['with'];
                                    if(isset($c['expand']))
                                        $with = $c['expand'];

                                    if(isset($with))
                                        $query->with($with);
                                }
                                else
                                {
                                    $query->where($c, $params);
                                }
                            }
                        }
                    }
                }
                elseif(is_array($criteria) && isset($criteria['condition']))
                {
                    /** @var ActiveQuery $query */
                    $query = Yii::createObject(ActiveQuery::className(), [$this->modelClass]);
                    $query->where($criteria['condition'], $params);
                }

                if(is_array($criteria))
                {
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
                        $sort = true;
                    }
                }
            }

            if(isset($query))
                $dp->query = $query;

            if(isset($sort) || isset($urlParams['sort']))
                $dp->getSort()->enableMultiSort = true;
        }

        return $dp;
    }

    protected function getParams($source)
    {
        if(isset($source) && is_array($source))
        {
            $source = $this->convertSource($source);
            if(isset($source['params']))
            {
                $params = $source['params'];
                if(is_object($params))
                    $params = (array)$params;
                return $params;
            }
        }
        return null;
    }

    protected function getConditionKeys($source)
    {
        if(isset($source) && is_array($source))
        {
            $source = $this->convertSource($source);
            if(isset($source['condition']))
            {
                $c = $source['condition'];
                $keys = [$c];
                if(substr($c, -9) !== 'Condition')
                    $keys[] = $c.'Condition';
                return $keys;
            }
        }
        return null;
    }

    protected function getConditionMapItem($keys, $params)
    {
        if(!is_array($keys))
            $keys = [$keys];
        foreach($keys as $key)
        {
            if(strlen($key) > 0 && $key[0] == '@')
                $methodName = substr($key, 1);
            if(isset($methodName) && method_exists($this->controller, $methodName))
                return $this->controller->{$methodName}($params);
            elseif(isset($this->controller->conditionMap[$key]))
                return $this->controller->conditionMap[$key];
        }
        return null;
    }

    protected function convertSource($source)
    {
        if(count($source) == 1)
        {
            $first = reset($source);
            if(is_object($first) || (is_array($first) && !is_string(key($source))))
                $source = (array)$first;
        }
        return $source;
    }
}