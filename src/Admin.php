<?php

namespace DocsifyDocs;

use DocsifyDocs\Admin\DocsPage;

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Admin {

    private const SETTINGS_HOOK = 'toplevel_page_docsify-docs';

    private DocsPage $docs_page;

    public function __construct() {
        $this->docs_page = new DocsPage();
    }

    public function run(): void {
        $this->docs_page->run();
        add_action( 'admin_menu', [ $this, 'addMenu' ] );
        add_action( 'admin_init', [ $this, 'registerSettings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAssets' ] );

        // The deny rule next to the files has to follow the setting that asks for it.
        add_action(
            'update_option_docsify_docs_options',
            function (): void {
                ( new Hardening() )->sync();
            }
        );
    }

    public function enqueueAssets( string $hook ): void {
        if ( $hook !== self::SETTINGS_HOOK ) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_script(
            'docsify-docs-admin',
            DOCSIFYDOCS_URL . 'src/assets/admin.js',
            [ 'media-editor' ],
            DOCSIFYDOCS_VERSION,
            true
        );

        wp_localize_script(
            'docsify-docs-admin',
            'docsifyDocsAdmin',
            [
                'frameTitle'  => __( 'Select the documentation logo', 'docsify-docs' ),
                'frameButton' => __( 'Use this logo', 'docsify-docs' ),
            ]
        );
    }

    public function addMenu(): void {
        add_menu_page(
            __( 'Docsify Docs', 'docsify-docs' ),
            __( 'Docsify Docs', 'docsify-docs' ),
            'manage_options',
            'docsify-docs',
            [ $this, 'renderSettingsPage' ],
            'dashicons-media-document',
            30
        );

        add_submenu_page(
            'docsify-docs',
            __( 'Settings', 'docsify-docs' ),
            __( 'Settings', 'docsify-docs' ),
            'manage_options',
            'docsify-docs',
            [ $this, 'renderSettingsPage' ]
        );
    }

    public function registerSettings(): void {
        register_setting(
            'docsify_docs_settings',
            'docsify_docs_options',
            [ 'sanitize_callback' => [ $this, 'sanitizeOptions' ] ]
        );

        add_settings_section( 'docsify_docs_access', __( 'Access Control', 'docsify-docs' ), '__return_null', 'docsify-docs' );

        add_settings_field( 'is_restricted', __( 'Enable Restriction', 'docsify-docs' ), [ $this, 'renderIsRestricted' ], 'docsify-docs', 'docsify_docs_access' );
        add_settings_field( 'allowed_roles', __( 'Allowed Roles', 'docsify-docs' ), [ $this, 'renderAllowedRoles' ], 'docsify-docs', 'docsify_docs_access' );
        add_settings_field( 'protect_files', __( 'Protect Files', 'docsify-docs' ), [ $this, 'renderProtectFiles' ], 'docsify-docs', 'docsify_docs_access' );

        add_settings_section( 'docsify_docs_appearance', __( 'Appearance', 'docsify-docs' ), '__return_null', 'docsify-docs' );

        add_settings_field( 'logo_id', __( 'Logo', 'docsify-docs' ), [ $this, 'renderLogo' ], 'docsify-docs', 'docsify_docs_appearance' );
        add_settings_field( 'theme_color', __( 'Theme Color', 'docsify-docs' ), [ $this, 'renderThemeColor' ], 'docsify-docs', 'docsify_docs_appearance' );
        add_settings_field( 'repo_url', __( 'Repository URL', 'docsify-docs' ), [ $this, 'renderRepoUrl' ], 'docsify-docs', 'docsify_docs_appearance' );
    }

    /**
     * @param mixed $input
     * @return array<string,mixed>
     */
    public function sanitizeOptions( $input ): array {
        $output = [];

        $output['is_restricted'] = ! empty( $input['is_restricted'] );

        $valid_roles = array_keys( wp_roles()->roles );
        if ( isset( $input['allowed_roles'] ) && is_array( $input['allowed_roles'] ) ) {
            $output['allowed_roles'] = array_values( array_intersect( $input['allowed_roles'], $valid_roles ) );
        } else {
            $output['allowed_roles'] = [];
        }

        $output['protect_files'] = ! empty( $input['protect_files'] );

        $output['logo_id'] = absint( $input['logo_id'] ?? 0 );

        $color                 = sanitize_hex_color( $input['theme_color'] ?? '' );
        $output['theme_color'] = $color ?: DOCSIFYDOCS_DEFAULT_THEME_COLOR;
        $output['repo_url']    = esc_url_raw( $input['repo_url'] ?? '' );

        return $output;
    }

    public function renderIsRestricted(): void {
        $options = get_option( 'docsify_docs_options', [] );
        $checked = isset( $options['is_restricted'] ) ? $options['is_restricted'] : DOCSIFYDOCS_DEFAULT_IS_RESTRICTED;
        ?>
        <label>
            <input type="checkbox" name="docsify_docs_options[is_restricted]" value="1" <?php checked( $checked ); ?>>
            <?php esc_html_e( 'Restrict access to logged-in users with specific roles', 'docsify-docs' ); ?>
        </label>
        <?php
    }

    public function renderAllowedRoles(): void {
        $options = get_option( 'docsify_docs_options', [] );
        $allowed = $options['allowed_roles'] ?? DOCSIFYDOCS_DEFAULT_ALLOWED_ROLES;

        foreach ( wp_roles()->roles as $role_key => $role_data ) {
            $checked = in_array( $role_key, $allowed, true );
            ?>
            <label style="display:block;margin-bottom:5px;">
                <input type="checkbox"
                        name="docsify_docs_options[allowed_roles][]"
                        value="<?php echo esc_attr( $role_key ); ?>"
                    <?php checked( $checked ); ?>>
                <?php echo esc_html( translate_user_role( $role_data['name'] ) ); ?>
            </label>
            <?php
        }
        echo '<p class="description">' . esc_html__( 'Select which roles can view the documentation.', 'docsify-docs' ) . '</p>';
    }

    public function renderProtectFiles(): void {
        $options = get_option( 'docsify_docs_options', [] );
        $checked = isset( $options['protect_files'] ) ? $options['protect_files'] : DOCSIFYDOCS_DEFAULT_PROTECT_FILES;
        ?>
        <label>
            <input type="checkbox" name="docsify_docs_options[protect_files]" value="1" <?php checked( $checked ); ?>>
            <?php esc_html_e( 'Serve the .md files through WordPress so the rule above also applies to them', 'docsify-docs' ); ?>
        </label>
        <p class="description">
            <?php esc_html_e( 'Docsify fetches every file over the network. Without this, the .md files stay readable by direct URL even while the page is restricted.', 'docsify-docs' ); ?>
        </p>
        <?php
        if ( $checked && ! Docs::hasPrettyPermalinks() ) {
            ?>
            <p class="description" style="color:#b32d2e;">
                <?php
                printf(
                    /* translators: %s: link to the permalink settings screen */
                    esc_html__( 'Inactive: the endpoint needs pretty permalinks. Pick any option other than Plain in %s.', 'docsify-docs' ),
                    '<a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '">'
                        . esc_html__( 'Settings > Permalinks', 'docsify-docs' ) . '</a>'
                );
                ?>
            </p>
            <?php
        }
    }

    public function renderLogo(): void {
        $options  = get_option( 'docsify_docs_options', [] );
        $logo_id  = absint( $options['logo_id'] ?? 0 );
        $logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';
        ?>
        <div class="docsify-docs-logo" data-docsify-docs-logo>
            <input type="hidden" name="docsify_docs_options[logo_id]" value="<?php echo esc_attr( (string) $logo_id ); ?>">
            <img class="docsify-docs-logo__preview"
                style="display:block;max-width:220px;max-height:80px;margin-bottom:8px;"
                alt=""
                <?php if ( $logo_url ) : ?>
                    src="<?php echo esc_url( $logo_url ); ?>"
                <?php else : ?>
                    hidden
                <?php endif; ?>
            >
            <button type="button" class="button" data-action="select">
                <?php esc_html_e( 'Select logo', 'docsify-docs' ); ?>
            </button>
            <button type="button" class="button-link-delete" data-action="remove" <?php echo $logo_url ? '' : 'hidden'; ?>>
                <?php esc_html_e( 'Remove', 'docsify-docs' ); ?>
            </button>
        </div>
        <p class="description">
            <?php esc_html_e( 'Shown at the top of the documentation sidebar. Falls back to the sample logo when empty.', 'docsify-docs' ); ?>
            <br>
            <?php esc_html_e( 'Ideal size: horizontal image of 240×40 px (use 480×80 px for retina displays). It is scaled down to fit 40 px tall, keeping its proportions.', 'docsify-docs' ); ?>
        </p>
        <?php
    }

    public function renderThemeColor(): void {
        $options = get_option( 'docsify_docs_options', [] );
        $color   = $options['theme_color'] ?? DOCSIFYDOCS_DEFAULT_THEME_COLOR;
        ?>
        <input type="color" name="docsify_docs_options[theme_color]" value="<?php echo esc_attr( $color ); ?>">
        <p class="description"><?php esc_html_e( 'Accent color used by Docsify.', 'docsify-docs' ); ?></p>
        <?php
    }

    public function renderRepoUrl(): void {
        $options = get_option( 'docsify_docs_options', [] );
        $url     = $options['repo_url'] ?? '';
        ?>
        <input type="url"
                name="docsify_docs_options[repo_url]"
                value="<?php echo esc_attr( $url ); ?>"
                class="regular-text"
                placeholder="https://github.com/username/repo">
        <p class="description"><?php esc_html_e( 'Optional. Adds a GitHub link in the Docsify toolbar.', 'docsify-docs' ); ?></p>
        <?php
    }

    public function renderSettingsPage(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $docs_dir = Docs::dir();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <?php $this->docs_page->renderNotice(); ?>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'docsify_docs_settings' );
                do_settings_sections( 'docsify-docs' );
                submit_button();
                ?>
            </form>

            <hr>
            <?php $this->docs_page->renderControls(); ?>

            <hr>
            <h2><?php esc_html_e( 'How to Use', 'docsify-docs' ); ?></h2>
            <ol>
                <li><?php esc_html_e( 'Generate the documentation page with the button above.', 'docsify-docs' ); ?></li>
                <li><?php esc_html_e( 'Write your documentation by editing the .md files in the folder below.', 'docsify-docs' ); ?></li>
                <li><?php esc_html_e( 'Visit the page to see the result.', 'docsify-docs' ); ?></li>
            </ol>
            <p>
                <?php
                printf(
                    /* translators: %s: file path */
                    esc_html__( 'Documentation files are stored in: %s', 'docsify-docs' ),
                    '<code>' . esc_html( $docs_dir ) . '/</code>'
                );
                ?>
                <br>
                <?php
                printf(
                    /* translators: %s: PHP constant name */
                    esc_html__( 'Define %s in wp-config.php to keep them somewhere else, such as a folder versioned with your project.', 'docsify-docs' ),
                    '<code>DOCSIFYDOCS_DOCS_DIR</code>'
                );
                ?>
            </p>
        </div>
        <?php
    }
}
