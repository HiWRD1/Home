# 个人主页门户（2025版）

[English](https://github.com/WRD1145/Home/blob/main/README.md)

[演示站](www.lihansen.xyz)

> [!CAUTION]
> 建议中文使用（翻译）


一个现代化的个人主页解决方案，集链接导航、励志名言和访客统计于一体。专为2025年设计。

``` bash
git clone https://github.com/WRD1145/Personal-Home-Portal.git
```
## ✨ 核心功能

- **快速链接导航**  
  卡片式设计，即时访问目标网站
- **每日名言系统**  
  每日更新的励志名言，支持手动刷新
- **精准访客统计**  
  实时显示今日/本月/今年/总访问量（2025年数据）
- **动态视觉效果**  
  渐变背景 + 鼠标轨迹 + 点击动画
- **响应式设计**  
  完美适配桌面/平板/移动设备
- **2025优化**  
  针对现代浏览器的性能增强

## 🛠️ 技术栈

- **前端**: HTML5, CSS3, ES2025
- **后端**: PHP 8.3+
- **依赖项**: Font Awesome 6.5
- **API**: Hitokoto 名言服务

## ⚙️ 安装指南

1. 克隆仓库：
```bash
git clone https://github.com/WRD1145/Personal-Home-Portal.git
```

2. 设置权限：
```bash
chmod -R 755 data
```

3. 服务器配置：
- PHP 8.3+（推荐8.4）
- 启用JSON扩展
- data/目录需有写入权限

4. 访问主页：
```
https://yourdomain.com
```

## 🎨 自定义配置

1. 修改卡片内容：
```html
<!-- index.php -->
<div class="card" id="your-id">
    <h2><i class="fas fa-icon"></i> 标题</h2>
    <p>描述</p>
</div>
```

2. 更新卡片链接：
```javascript
// js/script.js
const cardLinks = {
    'your-id': 'https://your-link.com',
    // ...
};
```

3. 调整2025主题色：
```css
/* css/style.css */
:root {
    --primary-color: #2563eb;   /* 2025流行蓝色 */
    --secondary-color: #8b5cf6; /* 2025流行紫色 */
    --accent-color: #ec4899;    /* 2025流行粉色 */
    --success-color: #10b981;   /* 2025流行绿色 */
}
```

## 📊 功能路线图（2025）

| 功能                | 状态           | 进度 |
| ---------------------- | ---------------- | -------- |
| 链接导航        | ✅ 已完成      | 100%     |
| 名言系统          | ✅ 已完成      | 100%     |
| 访客统计     | ✅ 已完成      | 100%     |
| 卡片式管理面板 | ⏳ 第三季度开发中 | 30%      |
| AI内容生成  | ⏳ 第四季度规划中    | 0%       |
| 访客地理位置    | ⏳ 第四季度规划中    | 0%       |

## 📜 许可证

本项目采用 GNU GPLv3 许可证：
```text
版权所有 (C) 2023-2025 WRD1145

本程序是自由软件：您可以自由修改和重新分发它
根据GNU通用公共许可证的条款发布，由自由软件基金会发布，
许可证版本3，或（根据您的选择）任何更新的版本。

本程序基于使用目的分发，但没有任何担保；
甚至没有适销性或特定用途适用性的暗示保证。
详见GNU通用公共许可证获取更多细节。

完整条款请参阅LICENSE文件。
```

## 👤 开发者信息

**创建者 [WRD1145](https://github.com/WRD1145)**  
**2025版 | 由DeepSeek AI驱动**

🚀 欢迎通过PR或Issues提交贡献！

