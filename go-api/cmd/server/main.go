package main

import (
	"fmt"
	"library-api/internal/cache"
	"library-api/internal/model"
	"library-api/internal/router"
	"log"

	"gorm.io/driver/mysql"
	"gorm.io/gorm"
)

func main() {

	dsn := "root:212729@tcp(127.0.0.1:3306)/library_db?charset=utf8mb4&parseTime=True&loc=Local"

	db, err := gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		log.Fatal("❌ 数据库连接失败:", err)
	}
	fmt.Println("✅ 数据库连接成功")

	// 自动同步表结构（已有数据不会被覆盖）
	db.AutoMigrate(
		&model.Category{},
		&model.Book{},
		&model.Reader{},
		&model.BorrowRecord{},
		&model.Fine{},
		&model.Admin{},
	)
	fmt.Println("✅ 数据表同步完成")

	// 连接 Redis 缓存
	redisCache := cache.New("localhost:6379", "", 0)
	if err := redisCache.Ping(); err != nil {
		fmt.Println("⚠️  Redis 未连接，缓存功能暂不可用（不影响其他功能）:", err)
	} else {
		fmt.Println("✅ Redis 缓存连接成功")
	}

	r := router.SetupRouter(db, redisCache)
	fmt.Println("🚀 Go API 服务启动在 http://localhost:8080 ...")
	r.Run(":8080")
}
