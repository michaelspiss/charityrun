<?php

namespace App\Auth;

use Solution10\Auth\Package;

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
                'editSettings' => true
        ]);
    }

    public function name() {
        return 'AdminPackage';
    }
}