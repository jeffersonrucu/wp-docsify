<?php
/**
 * Declares the plugin constants for static analysis; config.php itself cannot be
 * loaded here because it exits when WordPress is not bootstrapped.
 */

define( 'WPINC', 'wp-includes' );
define( 'DOCSIFYDOCS_VERSION', '0.0.0' );
define( 'DOCSIFYDOCS_NAME', 'Docsify Docs' );
define( 'DOCSIFYDOCS_DIR', __DIR__ . '/' );
define( 'DOCSIFYDOCS_URL', 'https://example.com/' );
define( 'DOCSIFYDOCS_DEFAULT_THEME_COLOR', '#2674D9' );
define( 'DOCSIFYDOCS_DEFAULT_IS_RESTRICTED', true );
define( 'DOCSIFYDOCS_DEFAULT_ALLOWED_ROLES', [ 'administrator' ] );
define( 'DOCSIFYDOCS_DEFAULT_PROTECT_FILES', true );
