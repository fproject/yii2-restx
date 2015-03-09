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
        if(is_array($urlParams) && isset($urlParams['criteria']))
        {
            $criteria = Json::decode($urlParams['criteria']);
            if(is_array($criteria) && isset($criteria['condition']))
            {
                $params = isset($criteria['params']) ? $criteria['params'] : [];

                /** @var ActiveQuery $query */
                $query = Yii::createObject(ActiveQuery::className(), [$this->modelClass]);
                $query->where($criteria['condition'], $params);
                $dp->query = $query;
            }
        }

        return $dp;
    }
}