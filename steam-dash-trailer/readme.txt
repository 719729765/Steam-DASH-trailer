=== Steam官方视频嵌入 Steam DASH Trailer ===
Contributors: qq420218831
Tags: video, steam, youtube, bilibili, embed
Requires at least: 5.8
Tested up to: 6.9
Requires PHP: 7.2
Stable tag: 3.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: steam-dash-trailer
Domain Path: /languages

Embed Steam DASH, YouTube, and Bilibili videos in posts/pages with responsive 16:9 playback.

== Description ==

Steam官方视频嵌入 Steam DASH Trailer allows you to insert videos from Steam, YouTube, and Bilibili easily using a shortcode or editor button.

Features:

* Full-width 16:9 responsive playback
* Automatic generation of H.264-compatible MPD manifests from Steam DASH `.m4s` streams
* TinyMCE button in classic editor for easy insertion
* Gutenberg block support for the latest editor
* YouTube and Bilibili iframe support
* Lightweight and self-contained (dash.all.min.js included for Steam DASH)
* All front-end text is fully internationalized (i18n) and ready for translation

== Installation ==

1. Upload the `steam-dash-trailer` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the Insert Video button in the classic editor, or the Gutenberg block.
4. Optionally, use the shortcode `[steam_dash url="VIDEO_URL" type="auto"]` in posts or pages.

== Usage ==

=== Shortcode ===

[steam_dash url="VIDEO_URL" type="auto"]

Parameters:

* url (required) – The video URL from Steam, YouTube, or Bilibili
* type (optional) – 'auto' or 'steam'. Use 'steam' to force Steam DASH conversion

Example:

[steam_dash url="https://video.fastly.steamstatic.com/.../dash_av1/chunk-stream1-00012.m4s" type="steam"]

=== Classic Editor ===

* Click the Insert Video button in TinyMCE.
* Enter the video URL in the popup dialog.
* Click Insert to automatically generate the shortcode.

=== Gutenberg Editor ===

* Add the Steam DASH Trailer block.
* Paste the video URL into the block settings.
* The preview automatically adjusts to 16:9 full width.

== Frequently Asked Questions ==

= How do I get a Steam DASH video URL? =
1. Open the official Steam game trailer page.
2. Press F12 → Network panel in your browser.
3. Play the video.
4. Filter network requests for .m4s files.
5. Copy any .m4s URL; the plugin automatically generates a compatible H.264 MPD manifest.

= Can I use YouTube or Bilibili links? =
Yes. Paste a YouTube watch URL or Bilibili video URL; the plugin will automatically generate an iframe embed.

= Is it responsive? =
Yes, all videos use a 16:9 full-width responsive container.

= Security Notice =
Users must provide publicly accessible URLs. Plugin does not host or distribute video content.

== Changelog ==

= 3.6 =
* License added for GPLv2+
* Fully compatible with latest WordPress (6.9)
* Removed load_plugin_textdomain() per WP guidelines
* Gutenberg block registration improved
* TinyMCE button improved
* Security: escaped and encoded JS / iframe variables


== Upgrade Notice ==
Version 3.6 is a major update. Please re-save your posts containing `[steam_dash]` shortcodes if you experience layout issues.

== License ==
GPLv2 or later
All included JS and PHP code is licensed under GPLv2 or later.

== Third-Party Libraries and Build Process ==

This plugin includes the following third-party JavaScript library:

- dash.js (MPEG-DASH reference client)

The file included in this plugin is:
- dash.all.min.js

This file is the official minified distribution provided by the dash.js project.
The original human-readable source code and build process are publicly available at:

https://github.com/Dash-Industry-Forum/dash.js

The library is built and maintained by the Dash Industry Forum.
Build instructions, source files, and licensing information can be found in the official repository linked above.

No modifications have been made to the distributed minified file.



