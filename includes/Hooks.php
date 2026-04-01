<?php

namespace MediaWiki\Extension\MorExternalImages;

use Parser;
use PPFrame;

class Hooks {

    public static function onParserFirstCallInit( Parser $parser ) {
        $parser->setHook( 'extimg', [ self::class, 'renderExternalImage' ] );
    }

    public static function renderExternalImage( $input, array $args, Parser $parser, PPFrame $frame ) {

        $src = trim( $args['src'] ?? '' );
        $width = isset( $args['width'] ) ? (int)$args['width'] : 300;
        $alt = $args['alt'] ?? '';

        // ❌ Missing src
        if ( $src === '' ) {
            return '<strong>Missing image source</strong>';
        }

        // 🔍 Parse URL
        $parsed = parse_url( $src );
        if ( !$parsed || !isset( $parsed['host'] ) || !isset( $parsed['scheme'] ) ) {
            return '<strong>Invalid image URL</strong>';
        }

        // 🔒 Only allow http/https
        $scheme = strtolower( $parsed['scheme'] );
        if ( $scheme !== 'http' && $scheme !== 'https' ) {
            return '<strong>Invalid image URL</strong>';
        }

        // 🌐 Normalize host
        $host = strtolower( $parsed['host'] );

        // 📜 Get allowed domains from config
        $allowedDomains = $GLOBALS['wgMorExternalImagesAllowedDomains'] ?? [ 'twimg.com' ];

        // ✅ Domain check (supports subdomains)
        $isAllowed = false;

        foreach ( $allowedDomains as $domain ) {
            $domain = strtolower( trim( $domain ) );

            if ( $domain === '' ) {
                continue;
            }

            // Exact match OR subdomain match
            if ( $host === $domain || substr( $host, -strlen( '.' . $domain ) ) === '.' . $domain ) {
                $isAllowed = true;
                break;
            }
        }

        if ( !$isAllowed ) {
            return '<strong>Invalid image source</strong>';
        }

        // 📏 Width safety
        if ( $width < 1 ) {
            $width = 300;
        }

        if ( $width > 2000 ) {
            $width = 2000;
        }

        // 🔐 Escape output
        $src = htmlspecialchars( $src, ENT_QUOTES );
        $alt = htmlspecialchars( $alt, ENT_QUOTES );

        // 🖼️ Output image
        return "<img src=\"{$src}\" width=\"{$width}\" alt=\"{$alt}\" loading=\"lazy\">";
    }
}
