<?php
/**
 * Plugin Name: Steam官方视频嵌入 Steam DASH Trailer
 * Plugin URI:  https://github.com/719729765/Steam-DASH-trailer
 * Description: WordPress 编辑器视频按钮，支持 Steam DASH Trailer 播放，并兼容 YouTube / Bilibili iframe。
 * Version: 3.6
 * Author: 码铃薯
 * Author URI: https://github.com/719729765
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: steam-dash-trailer
 * Domain Path: /languages
 *
 * 注意事项：
 * - 所有前端 JS 输出已使用 wp_json_encode() 安全转义
 * - 所有 inline script 已通过 wp_add_inline_script() 注入
 * - iframe 和 video 输出均使用 esc_attr(), esc_url() 和 wp_kses() 过滤
 * - 避免直接 echo / 未转义的 HTML / JS
 */

if (!defined('ABSPATH')) {
    exit;
}

// 顶部全局数组，用来登记所有播放器
$GLOBALS['sdt_players_to_init'] = array();

/**
 * Register TinyMCE button (Classic Editor)
 */
function sdt_register_tinymce_plugin() {
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return;
    }

    add_filter('mce_external_plugins', 'sdt_tinymce_external_plugins');
    add_filter('mce_buttons', 'sdt_tinymce_buttons');
}
add_action('admin_init', 'sdt_register_tinymce_plugin');

function sdt_tinymce_external_plugins($plugins) {
    $plugins['steam_dash_trailer'] = plugin_dir_url(__FILE__) . 'iframe-tinymce.js';
    return $plugins;
}

function sdt_tinymce_buttons($buttons) {
    $buttons[] = 'steam_dash_trailer';
    return $buttons;
}

/**
 * Register Gutenberg block
 */
function sdt_register_gutenberg_block() {
    if (!function_exists('register_block_type')) {
        return;
    }

    wp_register_script(
        'steam-dash-block',
        plugin_dir_url(__FILE__) . 'block.js',
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-i18n'),
        '3.6',
        true
    );

    register_block_type('steam-dash-trailer/block', array(
        'editor_script' => 'steam-dash-block',
    ));
}
add_action('init', 'sdt_register_gutenberg_block');

/**
 * Shortcode: [steam_dash url="..." type="auto"]
 *
 * 输出 Steam DASH / YouTube / Bilibili 视频播放器。
 * 安全措施：
 * - JS 输出通过 wp_json_encode() 安全转义
 * - inline script 使用 wp_add_inline_script()
 * - HTML 输出使用 esc_attr(), esc_url(), wp_kses()
 */
function sdt_steam_dash_shortcode($atts) {
    static $player_count = 0;

    $atts = shortcode_atts(array(
        'url'  => '',
        'type' => 'auto',
    ), $atts, 'steam_dash');

    if (empty($atts['url'])) {
        return '';
    }

    $url  = esc_url_raw($atts['url']);
    $type = in_array($atts['type'], array('auto', 'steam')) ? $atts['type'] : 'auto';

    // 安全检查：必须为 http 或 https
    if (!preg_match('#^https?://#', $url)) {
        return '';
    }

    // Steam DASH handling
    if ($type === 'steam' || strpos($url, 'steamstatic.com/store_trailers') !== false) {
        // DASH AV1 转 H264
        if (strpos($url, '.m4s') !== false && strpos($url, '/dash_av1/') !== false) {
            $parts = explode('/dash_av1/', $url);
            if (count($parts) === 2) {
                $url = $parts[0] . '/dash_h264.mpd';
            }
        }

        $player_count++;
        $player_id = 'steam-player-' . $player_count;

        // ✅ 这里登记播放器
        $GLOBALS['sdt_players_to_init'][$player_id] = $url;

        // Enqueue dash.js (MPEG-DASH reference client)
        // dash.js is a third-party library provided by the Dash Industry Forum.
        // Source code and build process: https://github.com/Dash-Industry-Forum/dash.js
        if (!wp_script_is('steam-dash-js', 'enqueued')) {
            wp_enqueue_script(
                'steam-dash-js',
                plugin_dir_url(__FILE__) . 'dash.all.min.js',
                array(),
                '4.7.4',
                true
            );
        }

        // 返回 video HTML
        return sprintf(
            '<div style="position:relative;width:100%%;padding-top:56.25%%;background:#000;">
                <video id="%s" controls style="position:absolute;top:0;left:0;width:100%%;height:100%%;"></video>
            </div>',
            esc_attr($player_id)
        );
    }

    // YouTube
    if (strpos($url, 'youtube.com/watch') !== false) {
        parse_str(wp_parse_url($url, PHP_URL_QUERY), $query);
        if (!empty($query['v'])) {
            $url = 'https://www.youtube.com/embed/' . esc_attr($query['v']);
        }
    }

    // Bilibili
    if (strpos($url, 'bilibili.com/video') !== false) {
        preg_match('/\/video\/(BV\w+)/', $url, $matches);
        if (!empty($matches[1])) {
            $url = 'https://player.bilibili.com/player.html?bvid=' . esc_attr($matches[1]);
        }
    }

    // iframe 输出
    $iframe = sprintf(
        '<div style="position:relative;width:100%%;padding-top:56.25%%;">
            <iframe src="%1$s"
                style="position:absolute;top:0;left:0;width:100%%;height:100%%;"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                referrerpolicy="no-referrer"
                allowfullscreen
                loading="lazy"
                title="%2$s">
            </iframe>
        </div>',
        esc_url($url),
        esc_attr__('视频播放器', 'steam-dash-trailer')
    );

    return wp_kses($iframe, array(
        'div' => array('style' => true),
        'iframe' => array(
            'src'             => true,
            'style'           => true,
            'frameborder'     => true,
            'allow'           => true,
            'allowfullscreen' => true,
            'loading'         => true,
            'title'           => true,
            'referrerpolicy'  => true,
        ),
    ));
}
add_shortcode('steam_dash', 'sdt_steam_dash_shortcode');

/**
 * Footer JS 渲染 Steam DASH 播放器
 * ✅ 顶层定义，只挂一次
 */
function sdt_render_dash_players() {
    if ( empty( $GLOBALS['sdt_players_to_init'] ) ) {
        return;
    }

    $players_json = wp_json_encode( $GLOBALS['sdt_players_to_init'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS );

    $script = "
    document.addEventListener('DOMContentLoaded', function() {
        var players = {$players_json};
        for (var id in players) {
            if (document.getElementById(id) && window.dashjs) {
                dashjs.MediaPlayer().create().initialize(
                    document.getElementById(id),
                    players[id],
                    false
                );
            }
        }
    });
    ";

    wp_add_inline_script( 'steam-dash-js', $script );
}
add_action( 'wp_footer', 'sdt_render_dash_players' );
