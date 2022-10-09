<?php

use common\rbac\Migration;
use common\models\User;
use yii\rbac\Role;

class m160520_094101_roles extends Migration {

    public function up() {
        $this->auth->remove($this->auth->getRole(User::ROLE_USER));
        $this->auth->remove($this->auth->getRole(User::ROLE_MANAGER));
        $this->auth->remove($this->auth->getRole(User::ROLE_ADMINISTRATOR));

        $user_with_docs = new Role([
            'name'        => User::ROLE_USER_WITH_DOCS,
            'description' => 'User with passport or other doc scan',
        ]);
        $this->auth->add($user_with_docs);

        $user_with_phone = $this->auth->createRole(User::ROLE_USER_WITH_PHONE);
        $this->auth->add($user_with_phone);
        $this->auth->addChild($user_with_docs, $user_with_phone);

        $user = $this->auth->createRole(User::ROLE_USER);
        $this->auth->add($user);
        $this->auth->addChild($user_with_phone, $user);
        
        $manager = $this->auth->createRole(User::ROLE_MANAGER);
        $this->auth->add($manager);
        $this->auth->addChild($manager, $user_with_docs);

        $admin = $this->auth->createRole(User::ROLE_ADMINISTRATOR);
        $this->auth->add($admin);
        $this->auth->addChild($admin, $manager);

        //Назначение прав существующим пользователям
        $this->auth->assign($admin, 1);
        $this->auth->assign($manager, 2);
        $this->auth->assign($user, 3);
    }

    public function down() {
        $this->auth->remove($this->auth->getRole(User::ROLE_USER_WITH_DOCS));
        $this->auth->remove($this->auth->getRole(User::ROLE_USER_WITH_PHONE));
        $this->auth->remove($this->auth->getRole(User::ROLE_USER));
        $this->auth->remove($this->auth->getRole(User::ROLE_MANAGER));
        $this->auth->remove($this->auth->getRole(User::ROLE_ADMINISTRATOR));


        $user = $this->auth->createRole(User::ROLE_USER);
        $this->auth->add($user);

        $manager = $this->auth->createRole(User::ROLE_MANAGER);
        $this->auth->add($manager);
        $this->auth->addChild($manager, $user);

        $admin = $this->auth->createRole(User::ROLE_ADMINISTRATOR);
        $this->auth->add($admin);
        $this->auth->addChild($admin, $manager);

        $this->auth->assign($admin, 1);
        $this->auth->assign($manager, 2);
        $this->auth->assign($user, 3);
    }

}
