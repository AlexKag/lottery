<?php

use yii\db\Schema;
use common\rbac\Migration;

class m161021_090001_init_permissions extends Migration
{
    const LEADER_REF_PAYEMNT = 'getLeaderRefPayment';

    public function up()
    {
        $leaderRole = $this->auth->getRole(\common\models\User::ROLE_USER_LEADER);
        $getLeaderRefPayment = $this->auth->createPermission(static::LEADER_REF_PAYEMNT);
        $this->auth->add($getLeaderRefPayment);
        $this->auth->addChild($leaderRole, $getLeaderRefPayment);
    }

    public function down()
    {
        $this->auth->remove($this->auth->getPermission(static::LEADER_REF_PAYEMNT));
    }

}
