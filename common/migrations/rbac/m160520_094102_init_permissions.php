<?php

use yii\db\Schema;
use common\rbac\Migration;

class m160520_094102_init_permissions extends Migration
{

    public function up()
    {
        $managerRole = $this->auth->getRole(\common\models\User::ROLE_MANAGER);

        $loginToBackend = $this->auth->getPermission('loginToBackend');
//        $this->auth->add($loginToBackend);
        $this->auth->addChild($managerRole, $loginToBackend);
    }

    public function down()
    {
        $this->auth->remove($this->auth->getPermission('loginToBackend'));
    }

}
