<?php

use yii\db\Schema;
use yii\db\Migration;
use fproject\components\DbHelper;

class m150212_091806_create_tables_and_data extends Migration
{
    public function up()
    {
        $this->createTable('user_profile', [
            'id' => 'pk',
            'email' => Schema::TYPE_STRING,
            'phone' => Schema::TYPE_STRING ,
        ]);

        $this->createTable('department', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING,
        ]);

        $this->createTable('user', [
            'id' => 'pk',
            'profileId' => "int(11)",
            'departmentId' => "int(11)",
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'password' => Schema::TYPE_STRING . ' NOT NULL',
            'authKey' => Schema::TYPE_STRING,
            'accessToken' => Schema::TYPE_STRING,
        ]);

        $this->createTable('user_department_assignment', [
            'userId' => "int(11)",
            'departmentId' => "int(11)",
        ]);

        $this->addForeignKey('fk_user_profile','user','profileId','user_profile','id');

        $this->addForeignKey('fk_user_department','user','departmentId','department','id');

        $this->addForeignKey('fk_department_assignment_user','user_department_assignment','userId','user','id');

        $this->addForeignKey('fk_department_assignment_department','user_department_assignment','departmentId','department','id');

        $data=[];

        for($i=0; $i<100;$i++)
        {
            $data[] = ['name' => 'Department No.'.$i];
        }

        DbHelper::insertMultiple('department', $data);

        $this->insert('user_profile', [
            'id'=>1,
            'email' => 'admin@fproject.net',
            'phone' => '0123456789',
        ]);

        $this->insert('user_profile', [
            'id'=>2,
            'email' => 'demo@fproject.net',
            'phone' => '9876543210',
        ]);

        $data=[];

        for($i=0; $i<1000;$i++)
        {
            $data[] = [
                'email' => "user_$i@fproject.net",
                'phone' => '0123456789'];
        }

        DbHelper::insertMultiple('user_profile', $data);


        $this->insert('user', [
            'profileId'=>1,
            'username' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ]);
        $this->insert('user', [
            'profileId'=>2,
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ]);

        $data=[];
        for($i=0; $i<500;$i++)
        {
            $data[] = ['profileId'=>$i+3,
                'departmentId'=> 1+ ($i % 100),
                'username' => 'demo_no_'.$i,
                'password' => 'demo_no_'.$i,
                'authKey' => "test_$i _key",
                'accessToken' => 'test_$i _token',];
        }

        DbHelper::insertMultiple('user', $data);
    }

    public function down()
    {
        echo "m150212_091806_create_user_table cannot be reverted.\n";

        return false;
    }
}
