<?php

use common\rbac\Migration;
use common\models\User;
use yii\rbac\Role;

class m161021_090000_roles extends Migration {

    public function up() {
//        $user_leader = $this->auth->createRole(User::ROLE_USER_LEADER);
        $user = $this->auth->getRole(User::ROLE_USER);
        $user_leader = new Role([
            'name'        => User::ROLE_USER_LEADER,
            'description' => 'Глава сообщества',
        ]);
        $this->auth->add($user_leader);
        $this->auth->addChild($user_leader, $user);
    }

    public function down() {
        $this->auth->remove($this->auth->getRole(User::ROLE_USER_LEADER));
    }

}
