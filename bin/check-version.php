<?php
/**
 * Fails when the version declared in the plugin header, config.php and
 * readme.txt drift apart, which is how a release ends up shipping the wrong
 * "Stable tag" to WordPress.org.
 */

declare( strict_types = 1 );

$root = dirname( __DIR__ );

/**
 * @return string|null
 */
function docsifydocs_match( string $file, string $pattern ) {
    $contents = file_get_contents( $file );

    if ( $contents === false ) {
        fwrite( STDERR, "Could not read {$file}\n" );
        exit( 1 );
    }

    return preg_match( $pattern, $contents, $matches ) === 1 ? trim( $matches[1] ) : null;
}

$versions = [
    'docsify-docs.php (Version)' => docsifydocs_match( $root . '/docsify-docs.php', '/^\s*\*\s*Version:\s*(.+)$/m' ),
    'config.php (DOCSIFYDOCS_VERSION)' => docsifydocs_match( $root . '/config.php', "/define\(\s*'DOCSIFYDOCS_VERSION',\s*'([^']+)'/" ),
    'readme.txt (Stable tag)' => docsifydocs_match( $root . '/readme.txt', '/^Stable tag:\s*(.+)$/m' ),
];

$failed = false;

foreach ( $versions as $label => $version ) {
    if ( $version === null ) {
        fwrite( STDERR, "Missing version in {$label}\n" );
        $failed = true;
    }
}

if ( $failed ) {
    exit( 1 );
}

$unique = array_unique( $versions );

if ( count( $unique ) > 1 ) {
    fwrite( STDERR, "Version mismatch:\n" );

    foreach ( $versions as $label => $version ) {
        fwrite( STDERR, sprintf( "  %-34s %s\n", $label, $version ) );
    }

    exit( 1 );
}

$version = (string) reset( $versions );

if ( preg_match( '/^\d+\.\d+\.\d+$/', $version ) !== 1 ) {
    fwrite( STDERR, "Version \"{$version}\" is not semantic versioning (x.y.z)\n" );
    exit( 1 );
}

$changelog = file_get_contents( $root . '/readme.txt' );

if ( $changelog !== false && strpos( $changelog, "= {$version} =" ) === false ) {
    fwrite( STDERR, "readme.txt has no changelog entry for version {$version}\n" );
    exit( 1 );
}

$requirements = [
    'Requires PHP' => [
        docsifydocs_match( $root . '/docsify-docs.php', '/^\s*\*\s*Requires PHP:\s*(.+)$/m' ),
        docsifydocs_match( $root . '/readme.txt', '/^Requires PHP:\s*(.+)$/m' ),
    ],
    'Requires at least' => [
        docsifydocs_match( $root . '/docsify-docs.php', '/^\s*\*\s*Requires at least:\s*(.+)$/m' ),
        docsifydocs_match( $root . '/readme.txt', '/^Requires at least:\s*(.+)$/m' ),
    ],
];

foreach ( $requirements as $header => $values ) {
    list( $in_plugin, $in_readme ) = $values;

    if ( $in_plugin !== $in_readme ) {
        fwrite( STDERR, sprintf( "%s differs: plugin header \"%s\", readme.txt \"%s\"\n", $header, (string) $in_plugin, (string) $in_readme ) );
        $failed = true;
    }
}

if ( $failed ) {
    exit( 1 );
}

echo "Version {$version} is consistent across the plugin header, config.php and readme.txt\n";
