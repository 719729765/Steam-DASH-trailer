
# Steam官方视频嵌入 Steam DASH Trailer


**WordPress 插件：轻松在文章/页面中插入 Steam 官方 DASH Trailer、YouTube、Bilibili 视频，全宽 16:9 自适应播放！**

---

## 功能亮点

- 🎮 **一键引用 Steam 官方视频**|**Steam 官方 DASH Trailer 自动拼接 H.264 MPD**
  - 直接粘贴 Steam `.m4s` 链接，插件自动转换为可播放地址  
  - 全宽 16:9 响应式，无需下载或额外操作
- ▶️ **支持 YouTube / Bilibili**
  - 粘贴链接即可自动生成 iframe 并自适应 16:9
- 🖥 **经典编辑器友好**
  - TinyMCE 插件按钮，点击弹窗输入 URL
- 💡 **内置 Steam 视频教程**
  - 弹窗下方展示操作步骤，即使不懂技术也能轻松使用  
  - 谷歌浏览器提示兜底，确保用户能找到正确链接

---

## 安装说明

1. 下载或克隆仓库到 `wp-content/plugins/steam-dash-trailer/`  
2. 在 WordPress 后台插件列表启用  
3. 打开文章/页面编辑器，点击 **插入视频** 按钮  

---

## 使用教程

### 1️⃣ Steam 视频

1. 打开 Steam 游戏官方视频介绍（Trailer）  
2. 按 F12 → Network 面板  
3. 点击播放视频  
4. 搜索 `.m4s`，找到类似 URL：

```

[https://video.fastly.steamstatic.com/.../dash_av1/chunk-stream1-00012.m4s](https://video.fastly.steamstatic.com/.../dash_av1/chunk-stream1-00012.m4s)

````

5. 粘贴链接到插件弹窗，自动转换为 `dash_h264.mpd` 并全宽播放  

### 2️⃣ YouTube / Bilibili

- 直接复制视频 URL 粘贴到弹窗输入框即可  
- 插件会自动生成 iframe 并自适应 16:9  

---

## 示例短代码

```text
[steam_dash url="https://video.fastly.steamstatic.com/.../chunk-stream1-00012.m4s" type="steam"]
[steam_dash url="https://www.youtube.com/watch?v=xxxxxxx" type="auto"]
[steam_dash url="https://www.bilibili.com/video/BVxxxx" type="auto"]
````

---

## 前端效果

* Steam 视频全宽 16:9
* YouTube / Bilibili iframe 自动 16:9

---

## 🔗 插件演示

- 演示地址 1：[插件实时演示（完整功能）](https://www.youzwy.com)
- 演示地址 2：[轻量示例站（基础播放）](https://game.tachgal.com)

## 开发者信息

* 作者：码铃薯
* 官网：[码铃薯](https://www.tudoucode.cn)

---

## 免责声明

* 插件不提供视频下载功能，仅引用官方播放地址
* 使用 Steam 视频时，请遵守 Steam 平台使用条款
