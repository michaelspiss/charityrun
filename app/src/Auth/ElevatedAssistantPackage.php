<?php

namespace App\Auth;

use Solution10\Auth\Package;

class ElevatedAssistantPackage extends Package {

    public function init() {
        $this
            ->precedence(5)
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
                'editSettings' => false
        ]);
    }

    public function name() {
        return 'ElevatedAssistantPackage';
    }
}