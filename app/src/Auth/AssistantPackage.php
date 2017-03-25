<?php

namespace App\Auth;

use Solution10\Auth\Package;

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