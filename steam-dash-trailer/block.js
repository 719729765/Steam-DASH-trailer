(function (blocks, element, components, i18n) {
    const { createElement: el } = element;
    const { TextControl } = components;
    const { __ } = i18n;

    blocks.registerBlockType('steam-dash-trailer/block', {
        title: __('Steam 视频', 'steam-dash-trailer'),
        icon: 'video-alt3',
        category: 'embed',
        attributes: {
            url: {
                type: 'string',
                default: ''
            },
            type: {
                type: 'string',
                default: 'auto'
            }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { url, type } = attributes;

            return el(
                'div',
                { className: 'steam-dash-block-editor' },
                // URL 输入框
                el(TextControl, {
                    label: __('视频地址（Steam / YouTube / Bilibili）', 'steam-dash-trailer'),
                    value: url,
                    placeholder: __('粘贴视频链接', 'steam-dash-trailer'),
                    onChange: function (value) {
                        setAttributes({ url: value });
                    }
                }),
                // Shortcode 占位提示
                url
                    ? el(
                          'p',
                          { style: { marginTop: '8px', fontSize: '12px', color: '#666' } },
                          __('将插入短代码：', 'steam-dash-trailer') + ' [steam_dash url="' + url + '" type="' + type + '"]'
                      )
                    : null,
                // Steam 视频获取教程（HTML）
                el('div', {
                    style: { marginTop: '6px', fontSize: '12px', color: '#999', lineHeight: '1.4em' },
                    dangerouslySetInnerHTML: {
                        __html:
                            '<strong>Steam Trailer 获取说明：</strong><br>' +
                            '1. 打开 Steam 游戏页面的视频 Trailer<br>' +
                            '2. 按 F12 打开开发者工具，切换到 Network<br>' +
                            '3. 播放视频并搜索 .m4s<br>' +
                            '4. 复制任意 .m4s 链接<br>' +
                            '5. 插件会自动转换为 dash_h264.mpd 播放'
                    }
                }),
                // 占位视频预览（16:9 响应式）
                url
                    ? el(
                          'div',
                          {
                              style: {
                                  position: 'relative',
                                  width: '100%',
                                  paddingTop: '56.25%', // 16:9
                                  background: '#000',
                                  marginTop: '8px',
                                  borderRadius: '4px',
                                  overflow: 'hidden',
                                  display: 'flex',
                                  alignItems: 'center',
                                  justifyContent: 'center',
                                  color: '#fff',
                                  fontSize: '12px',
                                  textAlign: 'center',
                                  padding: '4px',
                              }
                          },
                          __('视频预览（编辑器中为占位，前端会显示实际播放器）', 'steam-dash-trailer')
                      )
                    : null
            );
        },

        save: function (props) {
            const { attributes } = props;
            if (!attributes.url) {
                return null;
            }

            // 返回 Shortcode，由 PHP 端渲染视频播放器
            return el(
                'p',
                {},
                '[steam_dash url="' + attributes.url + '" type="' + attributes.type + '"]'
            );
        }
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.i18n
);
