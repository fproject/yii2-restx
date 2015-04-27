<?php
///////////////////////////////////////////////////////////////////////////////
//
// Licensed Source Code - Property of f-project.net
//
// © Copyright f-project.net 2015. All Rights Reserved.
//
///////////////////////////////////////////////////////////////////////////////
namespace fproject\rest;

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
}