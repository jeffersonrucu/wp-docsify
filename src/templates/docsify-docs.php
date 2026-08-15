<?php
/* Template Name: Docsify Docs */

if ( ! defined( 'WPINC' ) ) {
    die;
}

$docsifydocs_options     = get_option( 'docsify_docs_options', [] );
$docsifydocs_theme_color = $docsifydocs_options['theme_color'] ?? DOCSIFYDOCS_DEFAULT_THEME_COLOR;
$docsifydocs_repo_url    = $docsifydocs_options['repo_url'] ?? '';

$docsifydocs_logo_id = absint( $docsifydocs_options['logo_id'] ?? 0 );
$docsifydocs_logo    = $docsifydocs_logo_id ? wp_get_attachment_url( $docsifydocs_logo_id ) : '';

$docsifydocs_docs_url = \DocsifyDocs\Docs::basePath();

$docsifydocs_vendor = DOCSIFYDOCS_URL . 'src/assets/vendor/';

// Styles — docsify 5 splits the theme into a core stylesheet plus optional add-ons.
wp_enqueue_style( 'docsify-core', $docsifydocs_vendor . 'docsify/themes/core.min.css', [], DOCSIFYDOCS_VERSION );
wp_enqueue_style( 'docsify-vue', $docsifydocs_vendor . 'docsify/themes/vue.css', [ 'docsify-core' ], DOCSIFYDOCS_VERSION );
wp_enqueue_style( 'docsify-docs', DOCSIFYDOCS_URL . 'src/assets/style.css', [ 'docsify-vue' ], DOCSIFYDOCS_VERSION );

// Config inline script — must run before docsify.js
$docsifydocs_config = [
    'name'                => sprintf(
        /* translators: %s: site name */
        '%s - %s',
        __( 'Documentation', 'docsify-docs' ),
        get_bloginfo( 'name' )
    ),
    'logo'                => $docsifydocs_logo ?: '_media/logo.svg',
    'repo'                => $docsifydocs_repo_url,
    'loadSidebar'         => true,
    'loadNavbar'          => true,
    'basePath'            => $docsifydocs_docs_url,
    'auto2top'            => false,
    'themeColor'          => $docsifydocs_theme_color,
    'routerMode'          => 'hash',
    'sidebarDisplayLevel' => 0,
    'copyCode'            => [
        'buttonText'  => __( 'Copy', 'docsify-docs' ),
        'errorText'   => __( 'Error', 'docsify-docs' ),
        'successText' => __( 'Copied', 'docsify-docs' ),
    ],
    'pagination'          => [
        'previousText' => __( 'Previous', 'docsify-docs' ),
        'nextText'     => __( 'Next', 'docsify-docs' ),
    ],
    'search'              => [
        'placeholder' => __( 'Search...', 'docsify-docs' ),
        'noData'      => __( 'No results found', 'docsify-docs' ),
    ],
    'mermaidConfig'       => [ 'querySelector' => '.mermaid' ],
    'mermaidZoom'         => [
        'minimumScale' => 1,
        'maximumScale' => 5,
        'zoomPannel'   => true,
    ],
];

wp_register_script( 'docsify', $docsifydocs_vendor . 'docsify/docsify.min.js', [], DOCSIFYDOCS_VERSION, true );
wp_add_inline_script( 'docsify', 'window.$docsify = ' . wp_json_encode( $docsifydocs_config ) . ';', 'before' );
wp_enqueue_script( 'docsify' );

wp_enqueue_script( 'docsify-pagination', $docsifydocs_vendor . 'docsify-pagination/docsify-pagination.min.js', [ 'docsify' ], DOCSIFYDOCS_VERSION, true );
wp_enqueue_script( 'docsify-search', $docsifydocs_vendor . 'docsify/plugins/search.min.js', [ 'docsify' ], DOCSIFYDOCS_VERSION, true );
wp_enqueue_script( 'docsify-copy-code', $docsifydocs_vendor . 'docsify-copy-code/docsify-copy-code.min.js', [ 'docsify' ], DOCSIFYDOCS_VERSION, true );
wp_enqueue_script( 'docsify-sidebar-collapse', $docsifydocs_vendor . 'docsify-sidebar-collapse/docsify-sidebar-collapse.min.js', [ 'docsify' ], DOCSIFYDOCS_VERSION, true );

wp_enqueue_script( 'docsify-swagger-ui', $docsifydocs_vendor . 'docsify-swagger-ui/docsify-swagger-ui.js', [ 'docsify' ], DOCSIFYDOCS_VERSION, true );
wp_add_inline_script(
    'docsify-swagger-ui',
    'window.docsifySwaggerUi = ' . wp_json_encode( [
        'basePath'  => trailingslashit( $docsifydocs_docs_url ),
        'bundleUrl' => $docsifydocs_vendor . 'swagger-ui/swagger-ui-bundle.js',
        'styleUrl'  => $docsifydocs_vendor . 'swagger-ui/swagger-ui.css',
        'themeUrl'  => DOCSIFYDOCS_URL . 'src/assets/swagger-ui.css',
        'accent'    => $docsifydocs_theme_color,
    ] ) . ';',
    'before'
);

// The UMD build exposes window.mermaid; docsify-mermaid then triggers mermaid.run().
wp_enqueue_script( 'mermaid', $docsifydocs_vendor . 'mermaid/mermaid.min.js', [], DOCSIFYDOCS_VERSION, true );
wp_add_inline_script( 'mermaid', 'mermaid.initialize({ startOnLoad: false });', 'after' );

wp_enqueue_script( 'd3', $docsifydocs_vendor . 'd3/d3.min.js', [], DOCSIFYDOCS_VERSION, true );
wp_enqueue_script( 'docsify-mermaid', $docsifydocs_vendor . 'docsify-mermaid/docsify-mermaid.js', [ 'docsify', 'mermaid' ], DOCSIFYDOCS_VERSION, true );
wp_enqueue_script( 'docsify-mermaid-zoom', $docsifydocs_vendor . 'docsify-mermaid-zoom/docsify-mermaid-zoom.js', [ 'docsify-mermaid', 'd3' ], DOCSIFYDOCS_VERSION, true );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?php echo esc_url( get_site_icon_url() ); ?>">
    <title><?php echo esc_html( sprintf( '%s - %s', __( 'Documentation', 'docsify-docs' ), get_bloginfo( 'name' ) ) ); ?></title>
    <?php wp_head(); ?>
</head>
<body>
    <main id="app"></main>
    <?php wp_footer(); ?>
</body>
</html>
