<?php
///////////////////////////////////////////////////////////////////////////////
//
// Licensed Source Code - Property of f-project.net
//
// © Copyright f-project.net 2015. All Rights Reserved.
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