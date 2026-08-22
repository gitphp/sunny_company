Sunny_Company 是一款基于Laravel13开发的开源免费的企业建站系统，适用于小微企业官网开发使用。

## 基于laravel13 + PHP8.3 + vue3.5 + mysql8


```markdown
# Sunny_Company - Laravel 13 + Vue 3.5

> 基于 Laravel 13 与 Vue 3.5 构建的现代化企业品牌官网， 实现无缝单页应用体验，支持高性能 SEO 与服务端渲染。

## ✨ 技术栈

| 类别          | 技术选型                                                                 |
| ------------- | ------------------------------------------------------------------------ |
| **后端框架**  | Laravel 13 (PHP ^8.4)                                                    |
| **前端框架**  | Vue 3.5 (Composition API + `<script setup>`)                             |
| **构建工具**  | Vite 6 (热更新与按需编译)                                                |
| **UI/样式**   | Tailwind CSS 4 + Ant Design Vue (企业级组件库)                          |
| **状态管理**  | Pinia (持久化与模块化管理)                                               |
| **数据库**    | MySQL / PostgreSQL (支持 Eloquent ORM)                                   |
| **SEO/SSR**   | Laravel Vite SSR 插件 (提升首屏加载与搜索引擎可见性)                    |


## 🚀 快速开始

### 环境要求

- **PHP** ^8.4
- **Composer** ^2.5
- **Node.js** ^20.x (推荐 LTS 版本)
- **NPM** ^10.x 或 **PNPM** ^9.x
- **MySQL** 8+ 

### 安装步骤

1. **克隆项目**
   ```bash
   git clone https://github.com/your-company/your-project.git
   cd your-project
   ```

2. **安装 PHP 依赖**
   ```bash
   composer install --no-dev  # 生产环境去掉 --no-dev 以安装开发依赖
   ```

3. **安装前端依赖**
   ```bash
   npm install  # 或 pnpm install
   ```

4. **环境配置**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   编辑 `.env` 文件，配置数据库连接信息：
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **运行数据库迁移与数据填充**
   ```bash
   php artisan migrate --seed
   ```

6. **启动开发服务器**
   ```bash
   # 同时启动 Laravel 后端服务与 Vite 热更新 (推荐)
   npm run dev

   # 或者分开启动
   php artisan serve  # 后端: http://localhost:8000
   npm run dev        # 前端资源构建
   ```
   访问 `http://localhost:8000` 即可预览网站。

## 📦 可用的 NPM 脚本

| 命令                 | 说明                                                                 |
| -------------------- | -------------------------------------------------------------------- |
| `npm run dev`        | 启动 Vite 开发服务器 (热更新)                                        |
| `npm run build`      | 构建生产环境前端资源 (含 SSR 打包)                                   |
| `npm run preview`    | 本地预览构建后的生产版本                                             |
| `npm run lint`       | 使用 ESLint 检查 Vue/JS 代码规范 (如配置)                           |
| `npm run format`     | 使用 Prettier 格式化代码                                             |

## 🛠️ 核心功能模块

- **首页**：轮播横幅、企业简介、核心产品展示、新闻动态。
- **关于我们**：公司介绍、发展历程、企业文化、荣誉资质。
- **产品中心**：产品分类展示、详情页、支持搜索与筛选。
- **新闻中心**：行业资讯、企业公告列表与详情。
- **联系我们**：在线留言表单、公司地址地图、联系方式。
- **后台管理 (可选)**：基于 Laravel Nova 或 Filament 构建的内容管理界面。

## 🖥️ 生产部署

1. **优化配置**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **构建前端资源**
   ```bash
   npm run build
   ```

3. **设置环境变量**
   确保 `.env` 中 `APP_ENV=production` 且 `APP_DEBUG=false`。

4. **配置 Web 服务器 (Nginx/Apache)**
   将网站根目录指向 `public/` 文件夹，并配置好伪静态规则以支持 Inertia 路由。

## 🤝 贡献指南

欢迎提交 Issue 或 Pull Request。请确保代码遵循 PSR-12 编码规范，并编写相应的测试用例。

## 📄 许可证

本项目采用 [MIT 许可证](LICENSE) 开源。

---

**项目维护者**： [sunny/团队]  
**联系方式**： [邮箱/www.budff.com]  
**最后更新**： 2026-08-22
```

---

### 使用建议

1. **项目名称与描述**：请将第一行的“企业官网”和“技术栈”概述替换为你公司的实际名称与业务简介。
2. **许可证文件**：如果使用 MIT 或 Apache 等协议，请确保在项目根目录创建对应的 `LICENSE` 文件。
3. **联系方式**：替换维护者和联系方式的信息。
4. **后台管理**：如果项目初期没有后台，可以删除相关描述或标注为“规划中”。
5. **SSR 支持**：如果你的项目不打算启用服务端渲染，可以删除 `vite.config.js` 中相关的 SSR 配置描述。

这份文档覆盖了从技术选型到部署上线的全过程，也清晰定义了项目的目录结构和开发规范，方便团队协作。如果还需要调整，随时告诉我。


