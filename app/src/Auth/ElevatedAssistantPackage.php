<?php

namespace App\Auth;

use Solution10\Auth\Package;

/**
 * Class ElevatedAssistantPackage
 * This set of permission is granted to the Assistant during setup phase.
 * The assistant will have less permissions once setup phase is over.
 * @see \App\Auth\AssistantPackage
 * @package App\Auth
 */
class ElevatedAssistantPackage extends Package {

    public function init() {
        $this
            ->precedence(7)
            ->permissions([
                'addDonor' => true,
                'addRunner' => true,
                'addClass' => true,
                'viewManageHelp' => true,
                'editClass' => true,
                'editRunner' => true,
                'editDonor' => true,
                'viewEditHelp' => true,
                'rollback' => false,
                'editSettings' => false,
                'addRounds' => true
        ]);
    }
}