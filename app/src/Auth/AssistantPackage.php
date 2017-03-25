<?php

namespace App\Auth;

use Solution10\Auth\Package;

/**
 * Class AssistantPackage
 * This set of permissions is granted to the Assistant.
 * The assistant has elevated permissions during the setup phase.
 * @see \App\Auth\ElevatedAssistantPackage
 * @package App\Auth
 */
class AssistantPackage extends Package {

    public function init() {
        $this
            ->precedence(5)
            ->permissions([
                'addDonor' => false,
                'addRunner' => false,
                'addClass' => false,
                'viewManageHelp' => true,
                'editClass' => false,
                'editRunner' => false,
                'editDonor' => false,
                'viewEditHelp' => false,
                'rollback' => false,
                'editSettings' => false
        ]);
    }

    public function name() {
        return 'AssistantPackage';
    }
}