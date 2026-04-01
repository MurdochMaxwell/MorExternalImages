<?php

namespace MediaWiki\Extension\MorExternalImages;

use Parser;
use PPFrame;

class Hooks {

    public static function onParserFirstCallInit( Parser $parser ) {
        $parser->setHook( 'extimg', [ self::class, 'renderExternalImage' ] );
    }

    public static function renderExternalImage( $input, array $args, Parser $parser, PPFrame $frame ) {
        $src = $args['src'] ?? '';
        $width = isset( $args['width'] ) ? (int)$args['width'] : 300;
        $alt = $args['alt'] ?? '';

        if ( $src === '' ) {
            return '<strong>Missing image source</strong>';
        }

        $parsed = parse_url( $src );
        if ( !$parsed || !isset( $parsed['host'] ) ) {
            return '<strong>Invalid image URL</strong>';
        }

        $allowedDomains = $GLOBALS['wgMorExternalImagesAllowedDomains'] ?? [ 'pbs.twimg.com' ];
        if ( !in_array( $parsed['host'], $allowedDomains, true ) ) {
            return '<strong>Invalid image source</strong>';
        }

        if ( $width < 1 ) {
            $width = 300;
        }

        $src = htmlspecialchars( $src, ENT_QUOTES );
        $alt = htmlspecialchars( $alt, ENT_QUOTES );

        return "<img src=\"{$src}\" width=\"{$width}\" alt=\"{$alt}\" loading=\"lazy\">";
    }
}
