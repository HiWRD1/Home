# Personal Home Portal


一个现代化的个人首页解决方案，集成链接导航、名言展示和访问统计功能。专为2025年设计。

```bash
git clone https://github.com/WRD1145/Personal-Home-Portal.git
```

## ✨ 核心功能

- **高效链接跳转**  
  卡片式设计，点击即达目标网站
- **智能一言系统**  
  每日更新励志名言，支持手动刷新
- **精准访问统计**  
  实时显示日/月/年/总访问量（2025年数据）
- **动态视觉特效**  
  渐变背景 + 鼠标轨迹 + 点击动画
- **响应式设计**  
  完美适配桌面/平板/手机设备
- **2025优化**  
  针对现代浏览器性能优化

## 🛠️ 技术栈

- **前端**：HTML5, CSS3, ES2025
- **后端**：PHP 8.3+
- **依赖**：Font Awesome 6.5
- **API**：Hitokoto 一言服务

## ⚙️ 安装指南

1. 克隆仓库：
```bash
git clone https://github.com/WRD1145/Personal-Home-Portal.git
```

2. 设置权限：
```bash
chmod -R 755 data
```

3. 配置服务器：
- PHP 8.3+ (推荐8.4)
- 确保启用JSON扩展
- 设置data/目录可写

4. 访问首页：
```
https://yourdomain.com
```

## 🎨 个性化定制

1. 修改卡片内容：
```html
<!-- index.php -->
<div class="card" id="your-id">
    <h2><i class="fas fa-icon"></i> 标题</h2>
    <p>描述内容</p>
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

## 📊 功能路线图 (2025)

| 功能 | 状态 | 进度 |
|------|------|------|
| 链接跳转 | ✅ 已完成 | 100% |
| 一言功能 | ✅ 已完成 | 100% |
| 访问量统计 | ✅ 已完成 | 100% |
| 卡片式后台管理 | ⏳ Q3开发 | 30% |
| AI内容生成 | ⏳ Q4计划 | 0% |
| 访客地理位置 | ⏳ Q4计划 | 0% |

## 📜 许可证

本项目采用 GNU GPLv3 许可证发布：
```text
版权所有 (C) 2023-2025 WRD1145

本程序是自由软件，您可以自由地重新发布和修改它，但必须遵守以下条件：
1. 保留版权声明和许可声明
2. 明确提供源代码
3. 所有修改必须使用相同许可证发布

详细条款请查看 LICENSE 文件。
```

## 👤 开发者信息

**Created by [WRD1145](https://github.com/WRD1145)**  
**2025 Edition | Powered by DeepSeek AI**

🚀 欢迎提交PR或Issue参与开发！


