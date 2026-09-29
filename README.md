# 图书借阅系统

一个前后端分离的图书馆借阅管理系统，面向管理员与普通读者两类用户，覆盖图书、读者、分类、借阅、逾期罚款等完整业务闭环。

- **后端**：Go（Gin + GORM + JWT + Redis）
- **前端**：PHP 自研轻量 Web 层（无框架，调用 Go API）
- **数据库**：MySQL 8

## 功能特性

**管理员端**
- 仪表盘统计（图书/读者/借阅概览）
- 图书管理：增删改查、关键字搜索、库存（总量/可借量）管理
- 分类管理：多级分类、增删改
- 读者管理：增删改查、状态启用/停用
- 借阅管理：借书、还书、续借、借阅记录、逾期列表、逾期罚款
- 管理员管理：账号列表、角色分配
- 修改密码

**读者端**
- 读者注册 / 登录
- 个人门户：查看借阅记录、自助借书 / 还书、修改密码

## 技术栈

| 层 | 技术 |
| --- | --- |
| Go API | Gin、GORM、golang-jwt、go-redis、golang.org/x/crypto（bcrypt） |
| PHP Web | 原生 PHP 8、Bootstrap 5（模板）、自研路由与 ApiClient |
| 数据库 | MySQL 8（库名 `library_db`） |
| 缓存 | Redis（go-redis/v9，缓存层已实现） |

## 目录结构

```
library-system/
├── go-api/                  # Go 后端 API
│   ├── cmd/server/main.go   # 入口：连接 MySQL、AutoMigrate、启动服务
│   └── internal/
│       ├── router/          # 路由注册（公开 / 管理员 / 读者三类路由）
│       ├── middleware/      # JWT 认证、CORS
│       ├── handler/         # HTTP 处理器
│       ├── service/         # 业务逻辑
│       ├── repository/      # 数据访问层
│       ├── model/           # GORM 模型
│       └── cache/           # Redis 缓存封装
└── php-web/                 # PHP Web 前端
    ├── public/index.php     # 入口 + 路由分发 + CSRF/XSS 防护
    ├── src/Controller/      # 控制器
    ├── src/Service/         # 服务层（调用 Go API）
    ├── src/Http/ApiClient.php
    ├── config/              # API 地址、数据库配置
    └── templates/           # 页面模板
```

## 数据模型

| 表 | 说明 |
| --- | --- |
| `admins` | 管理员（用户名、角色、状态） |
| `categories` | 图书分类（支持父子层级） |
| `books` | 图书（ISBN、标题、作者、库存、分类、价格、位置） |
| `readers` | 读者（借阅证号、姓名、联系方式、最大借阅数、密码） |
| `borrow_records` | 借阅记录（借/还日期、状态、续借次数、罚款金额） |
| `fines` | 罚款记录（金额、原因、是否已缴） |

## 快速开始

### 1. 准备数据库

创建数据库并导入初始数据（含示例图书、分类、读者与管理员账号）：

```sql
CREATE DATABASE IF NOT EXISTS library_db DEFAULT CHARSET utf8mb4;
-- 导入项目根目录外层的 library_db.sql（含建表 + 示例数据）
```

> 后端启动时会通过 GORM `AutoMigrate` 自动同步表结构，不会覆盖已有数据。

### 2. 启动 Go API

```bash
cd go-api
go mod tidy
go run cmd/server/main.go
```

服务默认启动在 `http://localhost:8080`。数据库连接串当前硬编码在 `cmd/server/main.go` 的 `dsn` 中，请按需修改。

### 3. 启动 PHP Web

```bash
cd php-web
php -S localhost:8000 -t public
```

访问 `http://localhost:8000`。PHP 通过 `config/api.php` 中的 `go_api_base_url`（默认 `http://localhost:8080/api`）调用后端。

## 配置说明

| 文件 | 内容 |
| --- | --- |
| `go-api/cmd/server/main.go` | MySQL DSN（`root:212729@tcp(127.0.0.1:3306)/library_db`） |
| `php-web/config/api.php` | Go API 基础地址、分页大小 |
| `php-web/config/database.php` | PHP 直连数据库的 MySQL 配置 |

## 主要 API 路由

- **公开**：`POST /api/auth/login`、`POST /api/reader/login`、`GET /api/books`、`GET /api/books/search`、`GET /api/books/:id`、`GET /api/categories`、`GET /api/stats/dashboard`
- **管理员（需 JWT）**：图书/读者/分类/借阅/管理员/罚款相关增删改查
- **读者（需 JWT）**：`GET /api/reader/records`、`POST /api/reader/borrow`、`POST /api/reader/return`、`POST /api/reader/change-password`

完整路由见 `go-api/internal/router/router.go`。

## 相关文档

开发过程的详细步骤文档位于上级目录（`D:\广电云`）：

- 《Go API 开发详细步骤（新手版）.md》
- 《Go API 代码分析手册（新手版）.md》
- 《PHP Web 层开发详细步骤（新手版）.md》
- 《数据库设计详细步骤.md》
- 《Redis 缓存接入教程》（Docker / Memurai 两条路线）
- 《前端页面优化详细步骤.md》
