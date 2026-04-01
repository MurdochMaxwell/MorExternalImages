# MorExternalImages

A MediaWiki extension that allows safe embedding of external images using a custom `<extimg>` tag.

## Why?

MediaWiki normally blocks external images or requires `$wgRawHtml`, which is unsafe.

This extension provides:
- Safe image embedding
- Domain whitelisting
- Simple syntax

## Usage

```wiki
<extimg src="https://pbs.twimg.com/media/..." width="400" />
