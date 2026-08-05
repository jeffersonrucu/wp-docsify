<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

delete_option( 'docsify_docs_options' );
delete_option( 'docsify_docs_renamed' );
delete_option( 'docsify_docs_flat_docs' );
delete_option( 'docsify_docs_page_id' );

// Left behind when a pre-rename install had already been configured.
delete_option( 'wp_docsify_options' );
