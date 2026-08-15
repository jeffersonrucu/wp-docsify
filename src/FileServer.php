<?php

namespace DocsifyDocs;

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Hands the documentation files to the browser through WordPress.
 *
 * Docsify fetches every .md over XHR, so a documentation directory reachable by
 * URL is readable by anyone who guesses the path, no matter how well the page
 * itself is restricted. This endpoint applies the same rule to both.
 */
class FileServer {

    public const QUERY_VAR = 'docsify_docs_file';
    public const ROUTE     = 'docsify-docs-files';

    private const FLUSH_FLAG = 'docsify_docs_rewrite';

    /** Extensions the endpoint may hand out, mapped to the type it reports. */
    private const ALLOWED_TYPES = [
        'md'       => 'text/markdown; charset=UTF-8',
        'markdown' => 'text/markdown; charset=UTF-8',
        'mmd'      => 'text/plain; charset=UTF-8',
        'txt'      => 'text/plain; charset=UTF-8',
        'json'     => 'application/json',
        'yaml'     => 'application/yaml; charset=UTF-8',
        'yml'      => 'application/yaml; charset=UTF-8',
        'csv'      => 'text/csv; charset=UTF-8',
        'svg'      => 'image/svg+xml',
        'png'      => 'image/png',
        'jpg'      => 'image/jpeg',
        'jpeg'     => 'image/jpeg',
        'gif'      => 'image/gif',
        'webp'     => 'image/webp',
        'avif'     => 'image/avif',
        'ico'      => 'image/x-icon',
        'pdf'      => 'application/pdf',
    ];

    /** Types that can carry scripts, so they go out with scripting disabled. */
    private const SANDBOXED_TYPES = [ 'svg' ];

    public function run(): void {
        add_action( 'init', [ $this, 'addRewriteRule' ] );
        add_filter( 'query_vars', [ $this, 'addQueryVar' ] );
        add_action( 'template_redirect', [ $this, 'serve' ] );
    }

    public static function url(): string {
        return trailingslashit( home_url( self::ROUTE ) );
    }

    public function addRewriteRule(): void {
        add_rewrite_rule(
            '^' . self::ROUTE . '/(.+)$',
            'index.php?' . self::QUERY_VAR . '=$matches[1]',
            'top'
        );
    }

    /**
     * @param string[] $query_vars
     * @return string[]
     */
    public function addQueryVar( $query_vars ): array {
        $query_vars[] = self::QUERY_VAR;

        return $query_vars;
    }

    /**
     * Rewrite rules only reach the database on flush, so an install or update
     * that adds the rule has to ask for one.
     */
    public static function maybeFlush(): void {
        if ( get_option( self::FLUSH_FLAG ) === DOCSIFYDOCS_VERSION ) {
            return;
        }

        self::flush();
    }

    public static function flush(): void {
        flush_rewrite_rules( false );
        update_option( self::FLUSH_FLAG, DOCSIFYDOCS_VERSION );
    }

    public function serve(): void {
        $requested = get_query_var( self::QUERY_VAR );

        if ( ! is_string( $requested ) || $requested === '' ) {
            return;
        }

        // Authorize before touching the filesystem: a 404 would leak which files exist.
        if ( Access::check() !== Access::ALLOW ) {
            $this->abort( 403 );
        }

        $path = $this->resolve( $requested );

        if ( $path === null ) {
            $this->abort( 404 );
        }

        $this->send( $path );
    }

    /**
     * Returns the absolute path of a readable documentation file, or null when
     * the request escapes the documentation directory or asks for a type that
     * is not handed out.
     */
    private function resolve( string $requested ): ?string {
        $requested = wp_unslash( $requested );

        if ( strpos( $requested, "\0" ) !== false ) {
            return null;
        }

        $base = realpath( Docs::dir() );

        if ( $base === false ) {
            return null;
        }

        $path = realpath( $base . '/' . $requested );

        if ( $path === false || ! is_file( $path ) || ! is_readable( $path ) ) {
            return null;
        }

        // realpath() collapsed any ../ and followed symlinks; the result still has to be inside.
        if ( strpos( $path, $base . DIRECTORY_SEPARATOR ) !== 0 ) {
            return null;
        }

        if ( ! isset( self::ALLOWED_TYPES[ $this->extension( $path ) ] ) ) {
            return null;
        }

        return $path;
    }

    private function send( string $path ): void {
        $extension = $this->extension( $path );
        $size      = filesize( $path );

        // A page cache would hand a restricted file to whoever asks for it next.
        defined( 'DONOTCACHEPAGE' ) or define( 'DONOTCACHEPAGE', true );

        // A compression buffer would rewrite the body and leave Content-Length lying.
        while ( ob_get_level() > 0 ) {
            ob_end_clean();
        }

        status_header( 200 );
        nocache_headers();

        header( 'Content-Type: ' . self::ALLOWED_TYPES[ $extension ] );
        header( 'Content-Disposition: inline; filename="' . basename( $path ) . '"' );
        header( 'X-Content-Type-Options: nosniff' );
        header( 'X-Robots-Tag: noindex, nofollow', true );

        if ( $size !== false ) {
            header( 'Content-Length: ' . $size );
        }

        if ( in_array( $extension, self::SANDBOXED_TYPES, true ) ) {
            header( "Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; img-src data:" );
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- streaming a response body
        readfile( $path );
        exit;
    }

    private function extension( string $path ): string {
        return strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
    }

    private function abort( int $code ): void {
        defined( 'DONOTCACHEPAGE' ) or define( 'DONOTCACHEPAGE', true );

        while ( ob_get_level() > 0 ) {
            ob_end_clean();
        }

        status_header( $code );
        nocache_headers();
        header( 'Content-Type: text/plain; charset=UTF-8' );
        header( 'X-Robots-Tag: noindex, nofollow', true );

        echo $code === 403
            ? esc_html__( 'You are not allowed to read this documentation.', 'docsify-docs' )
            : esc_html__( 'Documentation file not found.', 'docsify-docs' );

        exit;
    }
}
