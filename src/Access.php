<?php

namespace DocsifyDocs;

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Single source of truth for who may read the documentation, shared by the page
 * template and by the endpoint that hands out the raw files.
 */
class Access {

    public const ALLOW  = 'allow';
    public const LOGIN  = 'login';
    public const DENIED = 'denied';

    /**
     * @return self::ALLOW|self::LOGIN|self::DENIED
     */
    public static function check(): string {
        $options       = get_option( 'docsify_docs_options', [] );
        $is_restricted = isset( $options['is_restricted'] ) ? $options['is_restricted'] : DOCSIFYDOCS_DEFAULT_IS_RESTRICTED;

        if ( ! $is_restricted ) {
            return self::ALLOW;
        }

        if ( ! is_user_logged_in() ) {
            return self::LOGIN;
        }

        $allowed_roles = $options['allowed_roles'] ?? DOCSIFYDOCS_DEFAULT_ALLOWED_ROLES;

        if ( empty( $allowed_roles ) || ! is_array( $allowed_roles ) ) {
            return self::DENIED;
        }

        $user = wp_get_current_user();

        return empty( array_intersect( $user->roles, $allowed_roles ) ) ? self::DENIED : self::ALLOW;
    }
}
