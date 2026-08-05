<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

delete_option( 'docsify_docs_options' );
delete_option( 'docsify_docs_renamed' );
delete_option( 'docsify_docs_flat_docs' );
delete_option( 'docsify_docs_page_id' );
delete_option( 'docsify_docs_rewrite' );

// Left behind when a pre-rename install had already been configured.
delete_option( 'wp_docsify_options' );

/**
 * Without the plugin there is nothing left to serve the documentation, so the
 * deny rule would only make the remaining files unreachable.
 */
$docsifydocs_docs_dir = defined( 'DOCSIFYDOCS_DOCS_DIR' ) && DOCSIFYDOCS_DOCS_DIR
    ? DOCSIFYDOCS_DOCS_DIR
    : trailingslashit( wp_upload_dir()['basedir'] ) . 'docsify-docs';

$docsifydocs_htaccess = trailingslashit( $docsifydocs_docs_dir ) . '.htaccess';

if ( ! function_exists( 'WP_Filesystem' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}

global $wp_filesystem;

if ( WP_Filesystem() && $wp_filesystem->exists( $docsifydocs_htaccess ) ) {
    $docsifydocs_contents = $wp_filesystem->get_contents( $docsifydocs_htaccess );

    if ( is_string( $docsifydocs_contents ) && strpos( $docsifydocs_contents, '# BEGIN Docsify Docs' ) === 0 ) {
        $wp_filesystem->delete( $docsifydocs_htaccess );
    }
}
