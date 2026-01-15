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
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register TinyMCE button (Classic Editor)
 */
function sdt_register_tinymce_plugin() {
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return;
    }

    add_filter('mce_external_plugins', function ($plugins) {
        $plugins['steam_dash_trailer'] = plugin_dir_url(__FILE__) . 'iframe-tinymce.js';
        return $plugins;
    });

    add_filter('mce_buttons', function ($buttons) {
        $buttons[] = 'steam_dash_trailer';
        return $buttons;
    });
}
add_action('admin_init', 'sdt_register_tinymce_plugin');

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
 */
function sdt_steam_dash_shortcode($atts) {
    static $player_count = 0;
    static $players_to_init = array();

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
        if (strpos($url, '.m4s') !== false && strpos($url, '/dash_av1/') !== false) {
            $parts = explode('/dash_av1/', $url);
            if (count($parts) === 2) {
                $url = $parts[0] . '/dash_h264.mpd';
            }
        }

        $player_count++;
        $player_id = 'steam-player-' . $player_count;
        $players_to_init[$player_id] = $url;

        if (!wp_script_is('steam-dash-js', 'enqueued')) {
            wp_enqueue_script(
                'steam-dash-js',
                plugin_dir_url(__FILE__) . 'dash.all.min.js',
                array(),
                '4.7.4',
                true
            );
        }

        // 在 footer 批量初始化所有播放器
        add_action('wp_footer', function() use ($players_to_init) {
            $script = 'document.addEventListener("DOMContentLoaded", function(){';
            foreach ($players_to_init as $id => $url) {
                $script .= "if(document.getElementById('{$id}') && window.dashjs){dashjs.MediaPlayer().create().initialize(document.getElementById('{$id}'), '{$url}', false);}";
            }
            $script .= '});';
            wp_add_inline_script('steam-dash-js', $script);
        });

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
