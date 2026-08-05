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
        if ( get_page_template_slug() !== 'template-docsify-docs.php' ) {
            return $page_template;
        }

        add_action( 'wp_enqueue_scripts', [ $this, 'isolateStyles' ], PHP_INT_MAX );

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
