/* global SwaggerUIBundle */
( function () {
    'use strict';

    var config = window.docsifySwaggerUi || {};

    function getSpecificationUrl( href ) {
        if ( href.indexOf( '#/' ) === 0 ) {
            return ( config.basePath || '' ) + href.slice( 2 );
        }

        return href;
    }

    function loadStyle( url ) {
        return new Promise( function ( resolve, reject ) {
            var existing = document.querySelector( 'link[data-docsify-swagger-ui-style="' + url + '"]' );

            if ( existing ) {
                resolve();
                return;
            }

            var stylesheet = document.createElement( 'link' );
            stylesheet.rel  = 'stylesheet';
            stylesheet.href = url;
            stylesheet.setAttribute( 'data-docsify-swagger-ui-style', url );
            stylesheet.onload  = resolve;
            stylesheet.onerror = reject;
            document.head.appendChild( stylesheet );
        } );
    }

    function loadSwaggerUi() {
        if ( window.SwaggerUIBundle ) {
            return Promise.resolve();
        }

        return new Promise( function ( resolve, reject ) {
            var script = document.createElement( 'script' );
            script.src     = config.bundleUrl;
            script.onload  = resolve;
            script.onerror = reject;
            document.body.appendChild( script );
        } );
    }

    function renderSwaggerUi() {
        var article = document.querySelector( '.markdown-section' );

        if ( ! article ) {
            return;
        }

        document.body.classList.remove( 'docsify-swagger-page' );

        var previous = document.getElementById( 'docsify-swagger-ui' );

        if ( previous ) {
            previous.remove();
        }

        var links = article.querySelectorAll( 'a' );
        var link  = Array.prototype.find.call( links, function ( candidate ) {
            return candidate.textContent.trim().toLowerCase() === 'swagger';
        } );

        if ( ! link ) {
            return;
        }

        document.body.classList.add( 'docsify-swagger-page' );

        var parent = link.closest( 'p' );

        if ( ! parent ) {
            return;
        }

        parent.remove();

        // Rendering outside .markdown-section keeps the Docsify theme (tables,
        // code tokens, headings) from cascading into the Swagger UI markup.
        var container = document.createElement( 'div' );
        container.id    = 'docsify-swagger-ui';
        container.style.setProperty( '--docsify-swagger-accent', config.accent || '#2674d9' );
        article.parentNode.insertBefore( container, article.nextSibling );

        // Pagination is appended to the article, so it would sit above the API
        // reference instead of closing the page.
        var pagination = article.querySelector( '.docsify-pagination-container' );

        if ( pagination ) {
            container.parentNode.appendChild( pagination );
        }

        loadStyle( config.styleUrl )
            .then( function () {
                return loadStyle( config.themeUrl );
            } )
            .then( loadSwaggerUi )
            .then( function () {
                window.SwaggerUIBundle( {
                    url:                      getSpecificationUrl( link.getAttribute( 'href' ) ),
                    dom_id:                   '#docsify-swagger-ui',
                    deepLinking:              true,
                    displayRequestDuration:   true,
                    docExpansion:             'list',
                    defaultModelsExpandDepth: 1,
                    persistAuthorization:     true,
                    tryItOutEnabled:          true,
                    // Swagger inlines the highlighter colors, so a light theme
                    // is the only way to match Docsify's code blocks.
                    syntaxHighlight:          { activated: true, theme: 'idea' },
                } );
            } )
            .catch( function () {
                container.textContent = 'Unable to load Swagger UI.';
            } );
    }

    function swaggerUiPlugin( hook ) {
        hook.doneEach( function () {
            window.requestAnimationFrame( renderSwaggerUi );
        } );
    }

    window.$docsify         = window.$docsify || {};
    window.$docsify.plugins = [ swaggerUiPlugin ].concat( window.$docsify.plugins || [] );
}() );
