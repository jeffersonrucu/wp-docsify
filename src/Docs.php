<?php

namespace DocsifyDocs;

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Resolves where the documentation files live and how the browser reaches them.
 */
class Docs {

    public const DIR_NAME = 'docsify-docs';

    /**
     * Absolute path to the directory holding the Markdown files.
     *
     * Defaults to the uploads directory so the docs survive plugin updates.
     * Define DOCSIFYDOCS_DOCS_DIR in wp-config.php to keep them elsewhere, for
     * instance inside a folder that is versioned with the project.
     */
    public static function dir(): string {
        if ( defined( 'DOCSIFYDOCS_DOCS_DIR' ) && DOCSIFYDOCS_DOCS_DIR ) {
            $dir = DOCSIFYDOCS_DOCS_DIR;
        } else {
            $uploads = wp_upload_dir();
            $dir     = trailingslashit( $uploads['basedir'] ) . self::DIR_NAME;
        }

        return untrailingslashit( (string) apply_filters( 'docsify_docs_dir', $dir ) );
    }

    /**
     * URL docsify prepends to every file it fetches.
     */
    public static function basePath(): string {
        $base_path = self::isProtected() ? FileServer::url() : self::directUrl();

        return untrailingslashit( (string) apply_filters( 'docsify_docs_base_path', $base_path ) );
    }

    /**
     * Whether the files are served by WordPress instead of straight off disk.
     *
     * Rewrite rules are the only way to keep the paths docsify expects, so plain
     * permalinks fall back to direct access rather than serving broken links.
     */
    public static function isProtected(): bool {
        $options = get_option( 'docsify_docs_options', [] );
        $enabled = isset( $options['protect_files'] ) ? $options['protect_files'] : DOCSIFYDOCS_DEFAULT_PROTECT_FILES;

        return (bool) $enabled && self::hasPrettyPermalinks();
    }

    public static function hasPrettyPermalinks(): bool {
        return (bool) get_option( 'permalink_structure' );
    }

    /**
     * A directory outside the WordPress root has no URL of its own, so it can
     * only be reached through the endpoint or through the filter.
     */
    private static function directUrl(): string {
        $dir  = self::dir();
        $root = untrailingslashit( str_replace( '\\', '/', ABSPATH ) );
        $path = str_replace( '\\', '/', $dir );

        if ( strpos( $path, $root . '/' ) === 0 ) {
            return site_url( substr( $path, strlen( $root ) ) );
        }

        $uploads = wp_upload_dir();

        return trailingslashit( $uploads['baseurl'] ) . self::DIR_NAME;
    }
}
