<?php

namespace DocsifyDocs\Admin;

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Creates and tracks the WordPress page that renders the documentation.
 */
class DocsPage {

    public const TEMPLATE = 'template-docsify-docs.php';
    private const OPTION  = 'docsify_docs_page_id';
    private const ACTION  = 'docsify_docs_page';

    public function run(): void {
        add_action( 'admin_post_' . self::ACTION, [ $this, 'handle' ] );
    }

    /**
     * Returns the tracked page, or null when it is missing or was trashed.
     */
    public function find(): ?\WP_Post {
        $page_id = (int) get_option( self::OPTION, 0 );

        if ( $page_id > 0 ) {
            $page = get_post( $page_id );
            if ( $page && $page->post_type === 'page' && $page->post_status !== 'trash' ) {
                return $page;
            }
        }

        return $this->findByTemplate();
    }

    /**
     * Recovers a page created before this feature existed, or by hand.
     */
    private function findByTemplate(): ?\WP_Post {
        $pages = get_posts(
            [
                'post_type'   => 'page',
                'post_status' => [ 'publish', 'draft', 'private' ],
                'numberposts' => 1,
                // phpcs:ignore WordPress.DB.SlowDBQuery -- admin-only lookup for a single settings screen
                'meta_key'    => '_wp_page_template',
                // phpcs:ignore WordPress.DB.SlowDBQuery -- admin-only lookup for a single settings screen
                'meta_value'  => self::TEMPLATE,
            ]
        );

        if ( empty( $pages ) ) {
            return null;
        }

        update_option( self::OPTION, $pages[0]->ID );

        return $pages[0];
    }

    public function handle(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'docsify-docs' ), '', [ 'response' => 403 ] );
        }

        check_admin_referer( self::ACTION );

        $mode = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : '';

        if ( $mode === 'slug' ) {
            $slug = isset( $_POST['slug'] ) ? sanitize_title( wp_unslash( $_POST['slug'] ) ) : '';
            $this->redirect( $this->updateSlug( $slug ) ? 'slug_updated' : 'slug_failed' );
        }

        if ( $mode === 'reset' ) {
            $existing = $this->find();
            if ( $existing ) {
                wp_trash_post( $existing->ID );
                delete_option( self::OPTION );
            }
        }

        $this->redirect( $this->create() ? 'created' : 'failed' );
    }

    private function redirect( string $notice ): void {
        wp_safe_redirect(
            add_query_arg(
                [
                    'page'              => 'docsify-docs',
                    'docsify_docs_page' => $notice,
                ],
                admin_url( 'admin.php' )
            )
        );
        exit;
    }

    /**
     * wp_update_post() appends a suffix when the slug is already taken.
     */
    private function updateSlug( string $slug ): bool {
        $page = $this->find();

        if ( ! $page || $slug === '' ) {
            return false;
        }

        $result = wp_update_post(
            [
                'ID'        => $page->ID,
                'post_name' => $slug,
            ],
            true
        );

        return ! is_wp_error( $result );
    }

    private function create(): bool {
        if ( $this->find() ) {
            return true;
        }

        // The second argument makes WordPress return a WP_Error instead of 0.
        $page_id = wp_insert_post(
            [
                'post_title'   => __( 'Documentation', 'docsify-docs' ),
                'post_name'    => 'docs',
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => '',
                'meta_input'   => [ '_wp_page_template' => self::TEMPLATE ],
            ],
            true
        );

        if ( is_wp_error( $page_id ) ) {
            return false;
        }

        update_option( self::OPTION, $page_id );

        return true;
    }

    /**
     * Renders the generate/reset controls for the settings screen.
     */
    public function renderControls(): void {
        $page = $this->find();
        ?>
        <h2><?php esc_html_e( 'Documentation Page', 'docsify-docs' ); ?></h2>
        <?php if ( $page ) : ?>
            <p>
                <?php
                printf(
                    /* translators: %s: link to the documentation page */
                    esc_html__( 'The documentation page already exists: %s', 'docsify-docs' ),
                    '<a href="' . esc_url( (string) get_permalink( $page ) ) . '" target="_blank" rel="noopener">'
                        . esc_html( $page->post_title ) . '</a>'
                );
                ?>
            </p>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
                    style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:16px;">
                <?php wp_nonce_field( self::ACTION ); ?>
                <input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>">
                <input type="hidden" name="mode" value="slug">
                <label for="docsify-docs-slug"><?php esc_html_e( 'Page URL', 'docsify-docs' ); ?></label>
                <code><?php echo esc_html( trailingslashit( home_url() ) ); ?></code>
                <input type="text" id="docsify-docs-slug" name="slug" class="regular-text"
                        value="<?php echo esc_attr( $page->post_name ); ?>" required>
                <?php submit_button( __( 'Save URL', 'docsify-docs' ), 'secondary', 'submit', false ); ?>
            </form>

            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <a href="<?php echo esc_url( (string) get_permalink( $page ) ); ?>" class="button button-primary"
                    target="_blank" rel="noopener">
                    <?php esc_html_e( 'View page', 'docsify-docs' ); ?>
                </a>
                <a href="<?php echo esc_url( (string) get_edit_post_link( $page->ID ) ); ?>" class="button">
                    <?php esc_html_e( 'Edit page', 'docsify-docs' ); ?>
                </a>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
                        onsubmit="return confirm('<?php echo esc_js( __( 'This moves the current page to the trash and creates a new one. Continue?', 'docsify-docs' ) ); ?>');">
                    <?php wp_nonce_field( self::ACTION ); ?>
                    <input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>">
                    <input type="hidden" name="mode" value="reset">
                    <?php submit_button( __( 'Reset page', 'docsify-docs' ), 'secondary', 'submit', false ); ?>
                </form>
            </div>
        <?php else : ?>
            <p><?php esc_html_e( 'Create the page that renders your documentation, already using the correct template.', 'docsify-docs' ); ?></p>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( self::ACTION ); ?>
                <input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>">
                <input type="hidden" name="mode" value="create">
                <?php submit_button( __( 'Generate documentation page', 'docsify-docs' ), 'primary', 'submit', false ); ?>
            </form>
        <?php endif; ?>
        <?php
    }

    public function renderNotice(): void {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only notice flag
        $notice = isset( $_GET['docsify_docs_page'] ) ? sanitize_key( wp_unslash( $_GET['docsify_docs_page'] ) ) : '';

        if ( $notice === 'created' ) {
            echo '<div class="notice notice-success is-dismissible"><p>'
                . esc_html__( 'Documentation page ready.', 'docsify-docs' ) . '</p></div>';
            return;
        }

        if ( $notice === 'failed' ) {
            echo '<div class="notice notice-error is-dismissible"><p>'
                . esc_html__( 'Could not create the documentation page.', 'docsify-docs' ) . '</p></div>';
            return;
        }

        if ( $notice === 'slug_updated' ) {
            echo '<div class="notice notice-success is-dismissible"><p>'
                . esc_html__( 'Page URL updated.', 'docsify-docs' ) . '</p></div>';
            return;
        }

        if ( $notice === 'slug_failed' ) {
            echo '<div class="notice notice-error is-dismissible"><p>'
                . esc_html__( 'Could not update the page URL.', 'docsify-docs' ) . '</p></div>';
        }
    }
}
