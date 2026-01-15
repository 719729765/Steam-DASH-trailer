(function() { 
    tinymce.create('tinymce.plugins.b2_iframe_embed', {
        init: function(ed, url) {
            ed.addButton('b2_iframe_embed', {
                text: '插入视频',
                tooltip: '点击插入视频 iframe（YouTube/Bilibili/Steam）',
                onclick: function() {
                    // 使用 TinyMCE 自带对话框
                    ed.windowManager.open({
                        title: '插入视频',
                        body: [
                            {
                                type: 'textbox',
                                name: 'video_url',
                                label: '视频网址（YouTube/Bilibili/Steam）',
                                placeholder: '粘贴 Steam .m4s 或 YouTube/Bilibili 链接'
                            },
                            {
                                type: 'container',
                                html:
                                    '<div style="margin-top:10px;padding:10px;background:#f5f5f5;border:1px solid #ddd;line-height:1.5;">' +
                                    '<strong>💡 Steam Trailer 获取教程：</strong><br>' +
                                    '1️⃣ 打开 Steam 游戏官方视频介绍Trailer<br>' +
                                    '2️⃣ 按 F12 → Network 面板<br>' +
                                    '3️⃣ 点击播放视频<br>' +
                                    '4️⃣ 搜索 .m4s，找到类似 URL：<br>' +
                                    'https://video.fastly.steamstatic.com/.../dash_av1/chunk-stream1-00012.m4s<br>' +
                                    '5️⃣ 复制任意一个 .m4s 链接，插件会自动转换为 dash_h264.mpd 并全宽播放' +
                                    '</div>'
                            }
                        ],
                        onsubmit: function(e) {
                            var video_url = e.data.video_url;
                            if(!video_url || video_url.trim() === "") {
                                ed.notificationManager.open({text: 'URL 不能为空', type: 'error'});
                                return;
                            }
                            video_url = video_url.trim();
                            var type = "auto";
                            if(video_url.indexOf("steamstatic.com/store_trailers") !== -1){
                                type = "steam";
                            }

                            // 插入 shortcode，宽度全宽，高度自动 16:9
                            var shortcode = '[b2_iframe url="' + video_url + '" type="' + type + '"]';
                            ed.execCommand('mceInsertContent', false, shortcode);
                        }
                    });
                }
            });
        },
        createControl: function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('b2_iframe_embed', tinymce.plugins.b2_iframe_embed);
})();
