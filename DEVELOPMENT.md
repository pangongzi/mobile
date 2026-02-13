# 开发指南

## 环境要求

- PHP >= 7.4
- Composer
- Git

## 本地开发设置

```bash
# 克隆项目
git clone https://github.com/pangongzi/mobile.git
cd mobile

# 安装依赖
composer install

# 运行示例
php examples/basic_usage.php
```

## 项目结构

```
mobile/
├── src/                    # 源代码
│   ├── MobileLocator.php   # 主类文件
│   └── data/               # 数据文件
│       └── mobile.dat      # 二进制数据文件
├── vendor/                 # Composer依赖
├── composer.json           # Composer配置
├── README.md               # 项目说明
├── CHANGELOG.md            # 更新日志
└── LICENSE                 # 许可证
```

## 代码规范

本项目遵循 PSR-12 代码规范，建议使用代码格式化工具保持一致性。

## 发布流程

1. 更新 `CHANGELOG.md`
2. 更新版本号
3. 运行基本功能测试
4. 提交更改
5. 创建Git标签
6. 推送到GitHub

```bash
# 发布新版本
git add .
git commit -m "Release v2.x.x"
git tag v2.x.x
git push origin master --tags
```