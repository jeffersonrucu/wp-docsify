<?php
/* Template Name: Docsify Docs - Access Denied */

if ( ! defined( 'WPINC' ) ) {
    die;
}

wp_enqueue_style( 'docsify-docs', DOCSIFYDOCS_URL . 'src/assets/style.css', [], DOCSIFYDOCS_VERSION );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?php echo esc_url( get_site_icon_url() ); ?>">
    <title><?php echo esc_html( sprintf( '%s - %s', __( 'Access Denied', 'docsify-docs' ), get_bloginfo( 'name' ) ) ); ?></title>
    <?php wp_head(); ?>
</head>
<body class="docsify-docs__body">
    <main class="docsify-docs__access-denied-container">
        <div class="docsify-docs__lock-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
            </svg>
        </div>

        <h1 class="docsify-docs__access-denied-title">
            <?php esc_html_e( 'Access Denied', 'docsify-docs' ); ?>
        </h1>

        <p class="docsify-docs__access-denied-message">
            <?php esc_html_e( 'Sorry, you do not have permission to access this documentation.', 'docsify-docs' ); ?>
        </p>

        <div class="docsify-docs__access-denied-details">
            <p><?php esc_html_e( 'Your account may not have the necessary permissions.', 'docsify-docs' ); ?></p>
        </div>

        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="docsify-docs__back-button">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
            </svg>
            <?php esc_html_e( 'Back to Home', 'docsify-docs' ); ?>
        </a>
    </main>
    <?php wp_footer(); ?>
</body>
</html>
