<?php

namespace DocsifyDocs;

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Activator {

    public static function activate(): void {
        // Must run first: it carries over legacy data the defaults below would mask.
        ( new Migration() )->run();

        self::setDefaultOptions();
        self::copySampleDocs();
        self::registerRoutes();

        ( new Hardening() )->sync();
    }

    private static function setDefaultOptions(): void {
        if ( false === get_option( 'docsify_docs_options' ) ) {
            add_option( 'docsify_docs_options', [
                'is_restricted' => DOCSIFYDOCS_DEFAULT_IS_RESTRICTED,
                'allowed_roles' => DOCSIFYDOCS_DEFAULT_ALLOWED_ROLES,
                'protect_files' => DOCSIFYDOCS_DEFAULT_PROTECT_FILES,
                'logo_id'       => 0,
                'theme_color'   => DOCSIFYDOCS_DEFAULT_THEME_COLOR,
                'repo_url'      => '',
            ] );
        }
    }

    /**
     * The endpoint that serves the files is a rewrite rule, and rules only reach
     * the database when they are flushed.
     */
    private static function registerRoutes(): void {
        ( new FileServer() )->addRewriteRule();
        FileServer::flush();
    }

    private static function copySampleDocs(): void {
        $dest_dir = Docs::dir();

        if ( file_exists( $dest_dir ) ) {
            return;
        }

        self::copyDir( DOCSIFYDOCS_DIR . 'src/docs', $dest_dir );
    }

    private static function copyDir( string $src, string $dest ): void {
        wp_mkdir_p( $dest );

        $items = scandir( $src );
        if ( ! $items ) {
            return;
        }

        foreach ( $items as $item ) {
            if ( $item === '.' || $item === '..' ) {
                continue;
            }

            $s = $src . '/' . $item;
            $d = $dest . '/' . $item;

            if ( is_dir( $s ) ) {
                self::copyDir( $s, $d );
            } else {
                copy( $s, $d );
            }
        }
    }
}
