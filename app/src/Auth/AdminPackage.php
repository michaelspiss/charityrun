<?php

namespace App\Auth;

use Solution10\Auth\Package;

/**
 * Class AdminPackage
 * The Administrator has full control over the system and thus all permissions.
 * @package App\Auth
 */
class AdminPackage extends Package {

    public function init() {
        $this
            ->precedence(10)
            ->permissions([
                'addDonor' => true,
                'addRunner' => true,
                'addClass' => true,
                'viewManageHelp' => true,
                'editClass' => true,
                'editRunner' => true,
                'editDonor' => true,
                'viewEditHelp' => true,
                'rollback' => true,
                'editSettings' => true,
                'addRounds' => true,
	            'removeAssistantEditRights' => true
        ]);
    }
}