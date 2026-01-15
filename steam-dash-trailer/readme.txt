=== Steam官方视频嵌入 Steam DASH Trailer ===
Contributors: qq420218831
Tags: video, steam, youtube, bilibili, embed
Requires at least: 5.2
Tested up to: 6.9
Requires PHP: 7.2
Stable tag: 3.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: steam-steam-dash-trailer
Domain Path: /languages

Embed Steam DASH, YouTube, and Bilibili videos in posts/pages with responsive 16:9 playback.


== Description ==

Steam官方视频嵌入 Steam DASH Trailer allows you to insert videos from Steam, YouTube, and Bilibili easily using a shortcode or editor button.

Features:

* Full-width 16:9 responsive playback
* Automatic Steam DASH trailer conversion from `.m4s` to H.264 `.mpd`
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
5. Copy any .m4s URL; the plugin automatically converts it to H.264 .mpd.

= Can I use YouTube or Bilibili links? =
Yes. Paste a YouTube watch URL or Bilibili video URL; the plugin will automatically generate an iframe embed.

= Is it responsive? =
Yes, all videos use a 16:9 full-width responsive container.

= Security Notice =
Users must provide publicly accessible URLs. Plugin does not host or distribute video content.

== Screenshots ==
1. Classic editor popup dialog (/assets/screenshot-1.png)
2. Gutenberg block preview (/assets/screenshot-2.png)
3. Steam DASH video playback (/assets/screenshot-3.png)

== Changelog ==

= 3.6 =
* License added for GPLv2+
* Fully compatible with latest WordPress (6.9)
* Removed load_plugin_textdomain() per WP guidelines
* Gutenberg block registration improved
* TinyMCE button improved
* Security: escaped and encoded JS / iframe variables

= 3.5 =
* Full internationalization (i18n) for all strings
* Gutenberg block registration added
* Security: JS and iframe variables escaped properly
* Shortcode renamed `steam_dash` for clarity
* TinyMCE button improved with i18n and URL validation

= 3.4 =
* Initial rewrite for official plugin audit compliance
* Compatibility with classic editor and Gutenberg

= 3.3 =
* Original release

== Upgrade Notice ==
Version 3.6 is a major update. Please re-save your posts containing `[steam_dash]` shortcodes if you experience layout issues.

== License ==
GPLv2 or later
All included JS and PHP code is licensed under GPLv2 or later.
