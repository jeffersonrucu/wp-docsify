<?php

namespace DocsifyDocs;

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Template {

    /** Styles the documentation page is allowed to load. */
    private const KEPT_STYLES = [ 'docsify-core', 'docsify-vue', 'docsify-docs' ];

    public function run(): void {
        $this->filters();
    }

    private function filters(): void {
        add_filter( 'page_template', [ $this, 'renderTemplate' ] );
        add_filter( 'theme_page_templates', [ $this, 'includeTemplate' ], 10, 4 );

        // Registered here, not in renderTemplate: WordPress decides about the
        // admin bar on template_redirect, before the page template is resolved.
        add_filter( 'show_admin_bar', [ $this, 'hideAdminBar' ] );
    }

    /**
     * The documentation page drops the theme styles, and the admin bar is one of
     * their casualties: its markup still prints from wp_footer, unstyled, as a
     * long bare list over the documentation. It also has nowhere to sit, since
     * docsify pins its own layout to the top of the viewport.
     *
     * @param mixed $show
     * @return mixed
     */
    public function hideAdminBar( $show ) {
        return $this->isDocsPage() ? false : $show;
    }

    private function isDocsPage(): bool {
        return get_page_template_slug() === 'template-docsify-docs.php';
    }

    /**
     * @param array<string,string> $post_templates
     * @param mixed                $wp_theme
     * @param mixed                $post
     * @param mixed                $post_type
     * @return array<string,string>
     */
    public function includeTemplate( $post_templates, $wp_theme, $post, $post_type ): array {
        $post_templates['template-docsify-docs.php'] = 'Docsify Docs';
        return $post_templates;
    }

    public function renderTemplate( string $page_template ): string {
        if ( ! $this->isDocsPage() ) {
            return $page_template;
        }

        add_action( 'wp_enqueue_scripts', [ $this, 'isolateStyles' ], PHP_INT_MAX );

        $this->optOutOfOptimization();

        $access = Access::check();

        if ( $access === Access::LOGIN ) {
            wp_safe_redirect( wp_login_url( get_permalink() ) );
            exit;
        }

        if ( $access === Access::DENIED ) {
            return DOCSIFYDOCS_DIR . 'src/templates/access-denied.php';
        }

        return DOCSIFYDOCS_DIR . 'src/templates/docsify-docs.php';
    }

    /**
     * Keeps caching and optimization plugins away from the documentation page.
     *
     * Docsify has to run at load: it reads window.$docsify, then fetches and
     * renders the Markdown. Deferring or reordering those scripts, which is what
     * "delay JavaScript execution" does by rewriting every script tag to a type
     * the browser will not run, leaves the page blank. Concatenating them breaks
     * the plugin order docsify depends on, and caching the result would serve a
     * restricted page to whoever asks for it next.
     *
     * These constants are the convention WP Rocket, W3 Total Cache, LiteSpeed
     * Cache and others check before touching a response.
     */
    private function optOutOfOptimization(): void {
        foreach ( [ 'DONOTCACHEPAGE', 'DONOTROCKETOPTIMIZE', 'DONOTMINIFY', 'DONOTCACHEOBJECT', 'DONOTASYNCCSS' ] as $constant ) {
            if ( ! defined( $constant ) ) {
                define( $constant, true );
            }
        }

        // LiteSpeed reads its own filter rather than a constant.
        add_filter( 'litespeed_control_set_nocache', '__return_true' );
    }

    /**
     * These templates print their own document, so theme and block styles only
     * have layouts to break here.
     */
    public function isolateStyles(): void {
        if ( ! apply_filters( 'docsify_docs_isolate_styles', true ) ) {
            return;
        }

        /** @var string[] $keep */
        $keep  = apply_filters( 'docsify_docs_kept_styles', self::KEPT_STYLES );
        $queue = wp_styles()->queue;

        foreach ( $queue as $handle ) {
            if ( ! in_array( $handle, $keep, true ) ) {
                wp_dequeue_style( $handle );
            }
        }
    }
}
