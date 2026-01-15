(function () {
    tinymce.PluginManager.add('steam_dash_trailer', function (editor) {

        editor.addButton('steam_dash_trailer', {
            text: '插入视频',
            tooltip: '插入视频（YouTube / Bilibili / Steam）',
            onclick: function () {
                editor.windowManager.open({
                    title: '插入视频',
                    body: [
                        {
                            type: 'textbox',
                            name: 'video_url',
                            label: '视频网址（YouTube / Bilibili / Steam）',
                            placeholder: '粘贴 Steam / YouTube / Bilibili 链接'
                        },
                        {
                            type: 'container',
                            html:
                                '<div style="margin-top:10px;padding:10px;background:#f5f5f5;border:1px solid #ddd;line-height:1.6;">' +
                                '<strong>Steam Trailer 获取说明：</strong><br>' +
                                '1. 打开 Steam 游戏页面的视频 Trailer<br>' +
                                '2. 按 F12 打开开发者工具，切换到 Network<br>' +
                                '3. 播放视频并搜索 .m4s<br>' +
                                '4. 复制任意 .m4s 链接<br>' +
                                '5. 插件会自动转换为 dash_h264.mpd 播放' +
                                '</div>'
                        }
                    ],
                    onsubmit: function (e) {
                        var video_url = e.data.video_url || '';
                        video_url = video_url.trim();

                        if (!video_url) {
                            editor.notificationManager.open({
                                text: 'URL 不能为空',
                                type: 'error'
                            });
                            return;
                        }

                        var type = 'auto';
                        if (video_url.indexOf('steamstatic.com/store_trailers') !== -1) {
                            type = 'steam';
                        }

                        var shortcode = '[steam_dash url="' + video_url + '" type="' + type + '"]';
                        editor.execCommand('mceInsertContent', false, shortcode);
                    }
                });
            }
        });

    });
})();
