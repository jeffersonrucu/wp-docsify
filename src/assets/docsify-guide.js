( function () {
    'use strict';

    var config = window.docsifyGuide || {};
    var labels = config.labels || {};
    var MARKER = 'docsify-guide';
    var DELAY  = 2000;
    var CALM   = window.matchMedia( '(prefers-reduced-motion: reduce)' );
    var timer  = null;

    function label( key, fallback ) {
        return labels[ key ] || fallback;
    }

    /**
     * The marker is an HTML comment, so a page still reads as a plain numbered
     * list when this script is unavailable.
     */
    function findList( article ) {
        var walker = document.createTreeWalker( article, NodeFilter.SHOW_COMMENT );

        while ( walker.nextNode() ) {
            if ( walker.currentNode.nodeValue.trim() !== MARKER ) {
                continue;
            }

            var node = walker.currentNode.parentNode;

            while ( node && node !== article ) {
                if ( node.nextElementSibling ) {
                    break;
                }

                node = node.parentNode;
            }

            var candidate = node === article ? article.firstElementChild : ( node && node.nextElementSibling );

            while ( candidate && candidate.tagName !== 'OL' ) {
                candidate = candidate.nextElementSibling;
            }

            return candidate || null;
        }

        return null;
    }

    function button( action, text, primary ) {
        var element = document.createElement( 'button' );
        element.type      = 'button';
        element.textContent = text;
        element.className = 'dg-button' + ( primary ? ' dg-button--primary' : '' );
        element.setAttribute( 'data-dg', action );

        return element;
    }

    function buildStep( item, number ) {
        var step  = document.createElement( 'li' );
        step.className = 'dg-step';

        var media = item.querySelector( 'img' );
        var text  = document.createElement( 'div' );
        text.className = 'dg-step__text';

        if ( media ) {
            var holder = media.closest( 'p' ) || media;
            holder.remove();
        }

        text.innerHTML = item.innerHTML;

        var mark = document.createElement( 'span' );
        mark.className   = 'dg-step__number';
        mark.textContent = number;

        var head = document.createElement( 'div' );
        head.className = 'dg-step__head';
        head.append( mark, text );
        step.append( head );

        if ( media ) {
            var figure = document.createElement( 'figure' );
            figure.className = 'dg-step__shot';
            media.loading = 'lazy';

            var zoom = document.createElement( 'button' );
            zoom.type      = 'button';
            zoom.className = 'dg-step__zoom';
            zoom.setAttribute( 'aria-label', label( 'zoom', 'Enlarge image' ) );
            zoom.append( media );
            figure.append( zoom );
            step.append( figure );
        }

        return step;
    }

    function render( guide, mode, current ) {
        var steps = guide.querySelectorAll( '.dg-step' );
        var total = steps.length;

        guide.dataset.mode    = mode;
        guide.dataset.current = current;

        Array.prototype.forEach.call( steps, function ( step, index ) {
            step.hidden = mode === 'step' && index !== current - 1;
        } );

        guide.querySelector( '.dg-track > i' ).style.width =
            ( mode === 'step' ? current / total : 1 ) * 100 + '%';
        guide.querySelector( '[data-dg=previous]' ).disabled = mode !== 'step' || current === 1;
        guide.querySelector( '[data-dg=next]' ).disabled     = mode !== 'step' || current === total;
        guide.querySelector( '[data-dg=all]' ).textContent   = mode === 'step'
            ? label( 'all', 'See every step' )
            : label( 'one', 'One step at a time' );
        guide.querySelector( '.dg-counter' ).textContent = mode === 'step'
            ? label( 'counter', 'Step %1$s of %2$s' ).replace( '%1$s', current ).replace( '%2$s', total )
            : label( 'steps', '%s steps' ).replace( '%s', total );
    }

    function stop( guide ) {
        if ( ! timer ) {
            return;
        }

        window.clearInterval( timer );
        timer = null;

        var fill = guide && guide.querySelector( '.dg-track > i' );

        if ( fill ) {
            fill.style.transition = '';
        }

        var play = guide && guide.querySelector( '[data-dg=play]' );

        if ( play ) {
            play.textContent = label( 'play', 'Play' );
        }
    }

    /**
     * The bar crawls to the next step over the delay, so the reader sees how
     * much of the step is left instead of waiting for a jump.
     */
    function tick( guide, current, total ) {
        if ( CALM.matches ) {
            return;
        }

        var fill = guide.querySelector( '.dg-track > i' );

        fill.style.transition = 'none';
        fill.style.width      = ( current - 1 ) / total * 100 + '%';
        void fill.offsetWidth;
        fill.style.transition = 'width ' + DELAY + 'ms linear';
        fill.style.width      = current / total * 100 + '%';
    }

    function play( guide ) {
        var total = guide.querySelectorAll( '.dg-step' ).length;

        guide.querySelector( '[data-dg=play]' ).textContent = label( 'pause', 'Pause' );
        render( guide, 'step', 1 );
        tick( guide, 1, total );

        timer = window.setInterval( function () {
            var current = Number( guide.dataset.current );

            if ( current >= total ) {
                stop( guide );
                return;
            }

            render( guide, 'step', current + 1 );
            tick( guide, current + 1, total );
        }, DELAY );
    }

    function move( guide, offset ) {
        var total   = guide.querySelectorAll( '.dg-step' ).length;
        var current = Number( guide.dataset.current || 1 );

        stop( guide );
        render( guide, 'step', Math.min( total, Math.max( 1, current + offset ) ) );
    }

    function build( article ) {
        var list = findList( article );

        if ( ! list || ! list.children.length ) {
            return;
        }

        var guide = document.createElement( 'div' );
        guide.className = 'dg-guide';

        var bar = document.createElement( 'div' );
        bar.className = 'dg-bar';

        var track = document.createElement( 'div' );
        track.className = 'dg-track';
        track.append( document.createElement( 'i' ) );

        var counter = document.createElement( 'span' );
        counter.className = 'dg-counter';

        bar.append(
            button( 'previous', label( 'previous', 'Previous' ) ),
            button( 'next', label( 'next', 'Next' ), true ),
            button( 'play', label( 'play', 'Play' ) ),
            track,
            counter,
            button( 'all', label( 'all', 'See every step' ) )
        );

        var steps = document.createElement( 'ol' );
        steps.className = 'dg-steps';

        Array.prototype.forEach.call( list.children, function ( item, index ) {
            steps.append( buildStep( item, index + 1 ) );
        } );

        guide.append( bar, steps );
        list.replaceWith( guide );
        render( guide, 'step', 1 );
    }


    /**
     * A single <dialog> serves every step: it brings focus handling and the
     * Escape key for free.
     */
    function lightbox() {
        var dialog = document.getElementById( 'dg-lightbox' );

        if ( dialog ) {
            return dialog;
        }

        dialog = document.createElement( 'dialog' );
        dialog.id        = 'dg-lightbox';
        dialog.className = 'dg-lightbox';
        dialog.append( document.createElement( 'img' ) );
        dialog.addEventListener( 'click', function () {
            dialog.close();
        } );
        document.body.append( dialog );

        return dialog;
    }

    function enlarge( trigger ) {
        var source = trigger.querySelector( 'img' );

        if ( ! source ) {
            return;
        }

        var dialog = lightbox();
        var image  = dialog.querySelector( 'img' );

        image.src = source.currentSrc || source.src;
        image.alt = source.alt;
        dialog.showModal();
    }

    document.addEventListener( 'click', function ( event ) {
        var zoom = event.target.closest( '.dg-step__zoom' );

        if ( zoom ) {
            enlarge( zoom );
            return;
        }

        var trigger = event.target.closest( '.dg-guide button[data-dg]' );

        if ( ! trigger ) {
            return;
        }

        var guide  = trigger.closest( '.dg-guide' );
        var action = trigger.getAttribute( 'data-dg' );

        if ( action === 'previous' ) {
            move( guide, -1 );
        } else if ( action === 'next' ) {
            move( guide, 1 );
        } else if ( action === 'all' ) {
            stop( guide );
            render( guide, guide.dataset.mode === 'step' ? 'all' : 'step', Number( guide.dataset.current || 1 ) );
        } else if ( action === 'play' ) {
            if ( timer ) {
                stop( guide );
            } else {
                play( guide );
            }
        }
    } );

    document.addEventListener( 'keydown', function ( event ) {
        if ( event.target.matches( 'input, textarea, select' ) ) {
            return;
        }

        var guide = document.querySelector( '.dg-guide' );

        if ( ! guide || guide.dataset.mode !== 'step' ) {
            return;
        }

        if ( event.key === 'ArrowRight' ) {
            move( guide, 1 );
        } else if ( event.key === 'ArrowLeft' ) {
            move( guide, -1 );
        }
    } );

    function guidePlugin( hook ) {
        hook.doneEach( function () {
            stop( null );

            var article = document.querySelector( '.markdown-section' );

            if ( article ) {
                build( article );
            }
        } );
    }

    window.$docsify         = window.$docsify || {};
    window.$docsify.plugins = [ guidePlugin ].concat( window.$docsify.plugins || [] );
}() );
