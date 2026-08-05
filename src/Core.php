<?php

namespace DocsifyDocs;

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Core {

    public function run(): void {
        $file_server = new FileServer();
        $file_server->run();

        if ( is_admin() ) {
            ( new Migration() )->run();

            // A plugin updated over the filesystem never runs the activation hook.
            FileServer::maybeFlush();

            $admin = new Admin();
            $admin->run();
        }

        $template = new Template();
        $template->run();
    }
}
