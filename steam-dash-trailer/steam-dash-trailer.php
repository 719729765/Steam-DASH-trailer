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
 * 自动获取 Steam Trailer 封面（带缓存）
 */
function sdt_get_steam_trailer_poster($url) {
    if (!preg_match('#store_trailers/(\d+)/#', $url, $m)) {
        return '';
    }

    $appid = $m[1];
    $cache_key = 'sdt_poster_' . md5($appid);
    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return $cached;
    }

    $api = 'https://store.steampowered.com/api/appdetails?appids=' . $appid;
    $response = wp_remote_get($api, array('timeout' => 5));

    if (is_wp_error($response)) {
        return '';
    }

    $json = json_decode(wp_remote_retrieve_body($response), true);

    if (
        empty($json[$appid]['success']) ||
        empty($json[$appid]['data']['movies'][0]['thumbnail'])
    ) {
        return '';
    }

    $poster = esc_url_raw($json[$appid]['data']['movies'][0]['thumbnail']);
    set_transient($cache_key, $poster, DAY_IN_SECONDS);

    return $poster;
}

/**
 * TinyMCE 按钮
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
 * Gutenberg block
 */
function sdt_register_gutenberg_block() {
    if (!function_exists('register_block_type')) {
        return;
    }

    wp_register_script(
        'steam-dash-block',
        plugin_dir_url(__FILE__) . 'block.js',
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-i18n'),
        '3.7',
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

    $atts = shortcode_atts(array(
        'url'  => '',
        'type' => 'auto',
    ), $atts, 'steam_dash');

    if (empty($atts['url'])) {
        return '';
    }

    $url  = esc_url_raw($atts['url']);
    $type = in_array($atts['type'], array('auto', 'steam')) ? $atts['type'] : 'auto';

    if (!preg_match('#^https?://#', $url)) {
        return '';
    }

    /* =======================
     * Steam DASH
     * ======================= */
    if ($type === 'steam' || strpos($url, 'steamstatic.com/store_trailers') !== false) {

        $poster = sdt_get_steam_trailer_poster($url);

        // AV1 m4s → h264 mpd
        if (strpos($url, '.m4s') !== false && strpos($url, '/dash_av1/') !== false) {
            $parts = explode('/dash_av1/', $url);
            if (count($parts) === 2) {
                $url = $parts[0] . '/dash_h264.mpd';
            }
        }

        $player_count++;
        $player_id = 'steam-player-' . $player_count;

        if (!wp_script_is('steam-dash-js', 'enqueued')) {
            wp_enqueue_script(
                'steam-dash-js',
                plugin_dir_url(__FILE__) . 'dash.all.min.js',
                array(),
                '4.7.4',
                true
            );

            // 一次性初始化逻辑（有封面 / 无封面双模式）
            wp_add_inline_script('steam-dash-js', '
document.addEventListener("DOMContentLoaded", function(){

    // 无封面：自动初始化
    document.querySelectorAll("video[data-src]:not([data-cover])").forEach(function(video){
        if(window.dashjs){
            dashjs.MediaPlayer().create().initialize(video, video.dataset.src, false);
        }
    });

    // 有封面：点击初始化
    document.addEventListener("click", function(e){
        var cover = e.target.closest(".sdt-cover");
        if(!cover) return;

        var wrap = cover.closest(".sdt-video-wrap");
        var video = wrap.querySelector("video");

        cover.style.display = "none";
        video.style.display = "block";

        if(window.dashjs){
            dashjs.MediaPlayer().create().initialize(video, video.dataset.src, true);
        }
    });

});
            ');
        }

        return sprintf(
            '<div class="sdt-video-wrap" style="position:relative;width:100%%;padding-top:56.25%%;background:#000;">
                %s
                <video id="%s"
                       data-src="%s"
                       %s
                       controls
                       style="position:absolute;top:0;left:0;width:100%%;height:100%%;display:%s;">
                </video>
            </div>',
            $poster ? '
            <div class="sdt-cover" style="position:absolute;top:0;left:0;width:100%;height:100%;background:url('.esc_url($poster).') center/cover no-repeat;cursor:pointer;">
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:64px;height:64px;background:rgba(0,0,0,.6);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <svg width="36" height="36" viewBox="0 0 36 36">
                        <path d="M31.3 19.5L10.1 32.4C8.9 33.1 8 32.6 8 31.1V5.4C8 3.9 8.9 3.4 10.1 4.2L31.3 17c1.2.5 1.2 1.7 0 2.5z" fill="#fff"/>
                    </svg>
                </div>
            </div>' : '',
            esc_attr($player_id),
            esc_url($url),
            $poster ? 'data-cover="1"' : '',
            $poster ? 'none' : 'block'
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
