<?php

namespace DocsifyDocs;

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Carries data over from the pre-rename "WP Docsify" releases.
 */
class Migration {

    private const FLAG         = 'docsify_docs_renamed';
    private const FLAT_FLAG    = 'docsify_docs_flat_docs';
    private const OLD_OPTION   = 'wp_docsify_options';
    private const OLD_DIR      = 'wp-docsify';
    private const OLD_TEMPLATE = 'template-wp-docsify.php';
    private const NEW_TEMPLATE = 'template-docsify-docs.php';
    private const OLD_LOCALES  = [ 'pt_BR', 'en_US' ];

    public function run(): void {
        $this->runRename();
        $this->flattenLocaleDir();
    }

    private function runRename(): void {
        if ( get_option( self::FLAG ) ) {
            return;
        }

        $this->migrateOption();
        $this->migrateUploadsDir();
        $this->migratePageTemplates();

        update_option( self::FLAG, DOCSIFYDOCS_VERSION );
    }

    private function migrateOption(): void {
        $old = get_option( self::OLD_OPTION );

        if ( $old === false || ! is_array( $old ) || get_option( 'docsify_docs_options' ) !== false ) {
            return;
        }

        add_option( 'docsify_docs_options', $this->normalizeOption( $old ) );
        delete_option( self::OLD_OPTION );
    }

    /**
     * Legacy values were written before the settings sanitizer existed.
     *
     * @param array<string,mixed> $old
     * @return array<string,mixed>
     */
    private function normalizeOption( array $old ): array {
        $roles = is_array( $old['allowed_roles'] ?? null ) ? $old['allowed_roles'] : [];

        return [
            'is_restricted' => ! empty( $old['is_restricted'] ),
            'allowed_roles' => array_values( array_filter( array_map( 'sanitize_key', $roles ) ) ),
            'logo_id'       => absint( $old['logo_id'] ?? 0 ),
            'theme_color'   => sanitize_hex_color( $old['theme_color'] ?? '' ) ?: DOCSIFYDOCS_DEFAULT_THEME_COLOR,
            'repo_url'      => esc_url_raw( $old['repo_url'] ?? '' ),
        ];
    }

    /**
     * Docs used to live in a per-locale subfolder; they are pt_BR only now.
     */
    private function flattenLocaleDir(): void {
        if ( get_option( self::FLAT_FLAG ) ) {
            return;
        }

        $uploads  = wp_upload_dir();
        $docs_dir = trailingslashit( $uploads['basedir'] ) . 'docsify-docs';

        if ( ! function_exists( 'WP_Filesystem' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        global $wp_filesystem;
        if ( ! WP_Filesystem() ) {
            return;
        }

        foreach ( self::OLD_LOCALES as $locale ) {
            $locale_dir = $docs_dir . '/' . $locale;

            // Only the first locale found wins: the root must not end up with mixed content.
            if ( ! is_dir( $locale_dir ) || file_exists( $docs_dir . '/README.md' ) ) {
                continue;
            }

            foreach ( (array) $wp_filesystem->dirlist( $locale_dir ) as $item ) {
                $wp_filesystem->move( $locale_dir . '/' . $item['name'], $docs_dir . '/' . $item['name'] );
            }

            $wp_filesystem->rmdir( $locale_dir, true );
        }

        update_option( self::FLAT_FLAG, DOCSIFYDOCS_VERSION );
    }

    private function migrateUploadsDir(): void {
        $uploads = wp_upload_dir();
        $old_dir = trailingslashit( $uploads['basedir'] ) . self::OLD_DIR;
        $new_dir = trailingslashit( $uploads['basedir'] ) . 'docsify-docs';

        if ( ! is_dir( $old_dir ) || is_dir( $new_dir ) ) {
            return;
        }

        if ( ! function_exists( 'WP_Filesystem' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        global $wp_filesystem;
        if ( WP_Filesystem() ) {
            $wp_filesystem->move( $old_dir, $new_dir );
        }
    }

    private function migratePageTemplates(): void {
        $pages = get_posts(
            [
                'post_type'   => 'page',
                'post_status' => 'any',
                'numberposts' => -1,
                'fields'      => 'ids',
                // phpcs:ignore WordPress.DB.SlowDBQuery -- one-off migration, guarded by an option flag
                'meta_key'    => '_wp_page_template',
                // phpcs:ignore WordPress.DB.SlowDBQuery -- one-off migration, guarded by an option flag
                'meta_value'  => self::OLD_TEMPLATE,
            ]
        );

        foreach ( $pages as $page_id ) {
            update_post_meta( $page_id, '_wp_page_template', self::NEW_TEMPLATE );
        }
    }
}
