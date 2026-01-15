<?php
/**
 * Plugin Name: Steam官方视频嵌入 Steam DASH Trailer
 * Description: WordPress 后台编辑器视频按钮，支持 Steam 官方 DASH Trailer 自动拼接播放，全宽 16:9 自适应，同时兼容 YouTube/Bilibili iframe。
 * Version: 3.3
 * Author: 码铃薯
 * Author URI: https://www.tudoucode.cn
 * Text Domain: steam-dash-trailer
 */

if(!defined('ABSPATH')) exit;

// 添加 TinyMCE 文字按钮
add_action('admin_init', function(){
    if(current_user_can('edit_posts') && current_user_can('edit_pages')){
        add_filter('mce_external_plugins', function($plugins){
            $plugins['steam_dash_trailer'] = plugin_dir_url(__FILE__).'iframe-tinymce.js';
            return $plugins;
        });
        add_filter('mce_buttons', function($buttons){
            array_push($buttons, 'steam_dash_trailer');
            return $buttons;
        });
    }
});

// shortcode 处理 iframe / DASH
add_shortcode('steam_dash', function($atts){
    $atts = shortcode_atts(array(
        'url'=>'',
        'type'=>'auto',  // auto / steam
    ), $atts);

    if(empty($atts['url'])) return '';

    $url = esc_url($atts['url']);
    $type = $atts['type'];

    // Steam DASH trailer 自动拼接 H.264 MPD
    if($type === 'steam' || strpos($url,'steamstatic.com/store_trailers') !== false){
        if(strpos($url,'.m4s') !== false){
            $parts = explode('/dash_av1/', $url);
            if(count($parts) === 2){
                $url = $parts[0] . '/dash_h264.mpd';
            }
        }

        $playerId = 'steam-player-'.uniqid();
        ob_start();
        ?>
        <div style="position: relative; width: 100%; padding-top: 56.25%; background: #000;">
            <video id="<?php echo $playerId; ?>" controls 
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
            ></video>
        </div>
        <script src="<?php echo plugin_dir_url(__FILE__); ?>dash.all.min.js"></script>
        <script>
            (function(){
                var video = document.getElementById("<?php echo $playerId; ?>");
                if(!video) return;
                dashjs.MediaPlayer().create().initialize(video, "<?php echo $url; ?>", false);
            })();
        </script>
        <?php
        return ob_get_clean();
    }

    // YouTube iframe
    if(strpos($url,'youtube.com/watch') !== false){
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        if(isset($query['v'])){
            $url = 'https://www.youtube.com/embed/'.$query['v'];
        }
    }

    // Bilibili iframe
    if(strpos($url,'bilibili.com/video') !== false){
        preg_match('/\/video\/(BV\w+)/', $url, $matches);
        if(isset($matches[1])){
            $url = 'https://player.bilibili.com/player.html?bvid='.$matches[1];
        }
    }

    // 默认 iframe 返回（16:9 自适应）
    return '<div style="position: relative; width: 100%; padding-top: 56.25%;">' .
           '<iframe src="'.$url.'" style="position: absolute; top:0; left:0; width:100%; height:100%;" '.
           'frameborder="0" allowfullscreen></iframe></div>';
});
