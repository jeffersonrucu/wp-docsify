/* global wp, docsifyDocsAdmin */
( function () {
    'use strict';

    var root = document.querySelector( '[data-docsify-docs-logo]' );

    if ( ! root ) {
        return;
    }

    var input   = root.querySelector( 'input[type="hidden"]' );
    var preview = root.querySelector( '.docsify-docs-logo__preview' );
    var select  = root.querySelector( '[data-action="select"]' );
    var remove  = root.querySelector( '[data-action="remove"]' );
    var frame;

    select.addEventListener( 'click', function () {
        if ( ! frame ) {
            frame = wp.media( {
                title: docsifyDocsAdmin.frameTitle,
                button: { text: docsifyDocsAdmin.frameButton },
                library: { type: 'image' },
                multiple: false
            } );

            frame.on( 'select', function () {
                var attachment = frame.state().get( 'selection' ).first().toJSON();

                input.value    = attachment.id;
                preview.src    = attachment.url;
                preview.hidden = false;
                remove.hidden  = false;
            } );
        }

        frame.open();
    } );

    remove.addEventListener( 'click', function () {
        input.value    = '';
        preview.hidden = true;
        remove.hidden  = true;
        preview.removeAttribute( 'src' );
    } );
} )();
