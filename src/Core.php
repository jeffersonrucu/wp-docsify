<?php

namespace DocsifyDocs;

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Core {

    public function run(): void {
        if ( is_admin() ) {
            ( new Migration() )->run();

            $admin = new Admin();
            $admin->run();
        }

        $template = new Template();
        $template->run();
    }
}
