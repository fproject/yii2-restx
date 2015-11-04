<?php

namespace tests\codeception\unit\models\base;

use Yii;

/**
 * This is the model class for table "user_department_assignment".
 *
 * @property integer $userId
 * @property integer $departmentId
 *
 * @property User $user
 * @property Department $department
 */
class UserDepartmentAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_department_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userId' => 'User ID',
            'departmentId' => 'Department Id',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(User::className(), ['id' => 'departmentId']);
    }

    public $_isInserting;
}
