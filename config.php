<?php

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'DOCSIFYDOCS_VERSION', '3.0.0' );
define( 'DOCSIFYDOCS_NAME', 'Docsify Docs' );
define( 'DOCSIFYDOCS_DIR', plugin_dir_path( __FILE__ ) );
define( 'DOCSIFYDOCS_URL', plugin_dir_url( __FILE__ ) );

/** Default values — overridden by admin settings stored in wp_options. */
define( 'DOCSIFYDOCS_DEFAULT_THEME_COLOR', '#2674D9' );
define( 'DOCSIFYDOCS_DEFAULT_IS_RESTRICTED', true );
define( 'DOCSIFYDOCS_DEFAULT_ALLOWED_ROLES', [ 'administrator' ] );
