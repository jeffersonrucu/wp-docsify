<?php

namespace DocsifyDocs;

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Deactivator {

    public static function deactivate(): void {
        // Options and docs are preserved on deactivation.

        // Clearing the flag makes the next activation register the endpoint again.
        // The rule itself may survive this request, since init already added it;
        // without a handler behind it, it only answers 404 until the next flush.
        delete_option( 'docsify_docs_rewrite' );
        flush_rewrite_rules( false );
    }
}
