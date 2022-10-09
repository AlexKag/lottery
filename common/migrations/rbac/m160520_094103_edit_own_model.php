<?php

use common\rbac\Migration;
use common\rbac\rule\OwnModelRule;

class m160520_094103_edit_own_model extends Migration
{
    public function up()
    {
        $rule = $this->auth->getRule('editOwnModel');
//        $this->auth->add($rule);

        $role = $this->auth->getRole(\common\models\User::ROLE_USER);

        $editOwnModelPermission = $this->auth->getPermission('editOwnModel');
//        $editOwnModelPermission->ruleName = $rule->name;

//        $this->auth->add($editOwnModelPermission);
        $this->auth->addChild($role, $editOwnModelPermission);
    }

    public function down()
    {
        $permission = $this->auth->getPermission('editOwnModel');
        $rule = $this->auth->getRule('ownModelRule');

        $this->auth->remove($permission);
        $this->auth->remove($rule);
    }
}
