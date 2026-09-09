<?php

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'DOCSIFYDOCS_VERSION', '3.4.3' );
define( 'DOCSIFYDOCS_NAME', 'Docsify Docs' );
define( 'DOCSIFYDOCS_DIR', plugin_dir_path( __FILE__ ) );
define( 'DOCSIFYDOCS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Default values, used until the admin settings are saved to wp_options.
 *
 * Guarded so wp-config.php can set them first, which is how a project keeps its
 * own defaults under version control instead of only in the database.
 */
if ( ! defined( 'DOCSIFYDOCS_DEFAULT_THEME_COLOR' ) ) {
    define( 'DOCSIFYDOCS_DEFAULT_THEME_COLOR', '#2674D9' );
}

if ( ! defined( 'DOCSIFYDOCS_DEFAULT_IS_RESTRICTED' ) ) {
    define( 'DOCSIFYDOCS_DEFAULT_IS_RESTRICTED', true );
}

if ( ! defined( 'DOCSIFYDOCS_DEFAULT_ALLOWED_ROLES' ) ) {
    define( 'DOCSIFYDOCS_DEFAULT_ALLOWED_ROLES', [ 'administrator' ] );
}

if ( ! defined( 'DOCSIFYDOCS_DEFAULT_PROTECT_FILES' ) ) {
    define( 'DOCSIFYDOCS_DEFAULT_PROTECT_FILES', true );
}
