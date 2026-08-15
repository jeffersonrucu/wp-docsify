<?php

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'DOCSIFYDOCS_VERSION', '3.2.0' );
define( 'DOCSIFYDOCS_NAME', 'Docsify Docs' );
define( 'DOCSIFYDOCS_DIR', plugin_dir_path( __FILE__ ) );
define( 'DOCSIFYDOCS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Default values, used until the admin settings are saved to wp_options.
 *
 * Guarded so wp-config.php can set them first, which is how a project keeps its
 * own defaults under version control instead of only in the database.
 */
defined( 'DOCSIFYDOCS_DEFAULT_THEME_COLOR' ) or define( 'DOCSIFYDOCS_DEFAULT_THEME_COLOR', '#2674D9' );
defined( 'DOCSIFYDOCS_DEFAULT_IS_RESTRICTED' ) or define( 'DOCSIFYDOCS_DEFAULT_IS_RESTRICTED', true );
defined( 'DOCSIFYDOCS_DEFAULT_ALLOWED_ROLES' ) or define( 'DOCSIFYDOCS_DEFAULT_ALLOWED_ROLES', [ 'administrator' ] );
defined( 'DOCSIFYDOCS_DEFAULT_PROTECT_FILES' ) or define( 'DOCSIFYDOCS_DEFAULT_PROTECT_FILES', true );
