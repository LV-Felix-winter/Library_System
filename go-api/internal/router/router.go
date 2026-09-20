package router

import (
	"library-api/internal/cache"
	"library-api/internal/handler"
	"library-api/internal/middleware"
	"library-api/internal/repository"
	"library-api/internal/service"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

func SetupRouter(db *gorm.DB, redisCache *cache.Cache) *gin.Engine {
	r := gin.Default()
	r.Use(middleware.CORS())

	// ========== 初始化各层 ==========
	bookRepo := repository.NewBookRepo(db)
	categoryRepo := repository.NewCategoryRepo(db)
	readerRepo := repository.NewReaderRepo(db)
	borrowRepo := repository.NewBorrowRepo(db)
	adminRepo := repository.NewAdminRepo(db)

	fineService := service.NewFineService(db)
	bookService := service.NewBookService(bookRepo, redisCache)
	readerService := service.NewReaderService(readerRepo)
	borrowService := service.NewBorrowService(borrowRepo, bookRepo, readerRepo, fineService, redisCache)
	authService := service.NewAuthService(adminRepo, readerRepo, redisCache)

	bookHandler := handler.NewBookHandler(bookService)
	readerHandler := handler.NewReaderHandler(readerService)
	borrowHandler := handler.NewBorrowHandler(borrowService)
	categoryHandler := handler.NewCategoryHandler(categoryRepo, redisCache)
	authHandler := handler.NewAuthHandler(authService)
	statsHandler := handler.NewStatsHandler(db, redisCache)
	cacheHandler := handler.NewCacheHandler(redisCache)

	// ========== 注册路由 ==========
	api := r.Group("/api")
	{
		// 公开接口（无需登录）
		api.POST("/auth/login", authHandler.Login)
		api.GET("/books", bookHandler.List)
		api.GET("/books/search", bookHandler.Search)
		api.GET("/books/:id", bookHandler.Detail)
		api.GET("/categories", categoryHandler.List)
		api.GET("/stats/dashboard", statsHandler.Dashboard)
		api.POST("/reader/login", authHandler.ReaderLogin)

		// 需要登录的接口
		auth := api.Group("").Use(middleware.Auth(authService))
		{
			auth.POST("/books", bookHandler.Create)
			auth.PUT("/books/:id", bookHandler.Update)
			auth.DELETE("/books/:id", bookHandler.Delete)

			auth.GET("/readers", readerHandler.List)
			auth.GET("/readers/:id", readerHandler.Detail)
			auth.POST("/readers", readerHandler.Create)
			auth.PUT("/readers/:id", readerHandler.Update)
			auth.PUT("/readers/:id/status", readerHandler.UpdateStatus)
			auth.DELETE("/readers/:id", readerHandler.Delete)

			auth.POST("/borrow/borrow", borrowHandler.Borrow)
			auth.POST("/borrow/return", borrowHandler.Return)
			auth.POST("/borrow/renew", borrowHandler.Renew)
			auth.GET("/borrow/records", borrowHandler.Records)
			auth.GET("/borrow/overdue", borrowHandler.Overdue)
			auth.GET("/borrow/all", borrowHandler.All)
			auth.GET("/borrow/reminders", borrowHandler.Reminders)

			auth.POST("/categories", categoryHandler.Create)

			auth.GET("/admins", authHandler.ListAdmins)
			auth.PUT("/admins/:id/role", authHandler.UpdateRole)

			auth.PUT("/categories/:id", categoryHandler.Update)
			auth.DELETE("/categories/:id", categoryHandler.Delete)

			auth.POST("/auth/change-password", authHandler.ChangePassword)
			auth.POST("/auth/logout", authHandler.Logout)

			// Redis 缓存管理（管理员清空缓存）
			auth.POST("/admin/cache/clear", cacheHandler.ClearCache)
		}

		reader := api.Group("").Use(middleware.ReaderAuth(authService))
		{
			reader.GET("/reader/records", borrowHandler.ReaderRecords)
			reader.POST("/reader/borrow", borrowHandler.ReaderBorrow)
			reader.POST("/reader/return", borrowHandler.ReaderReturn)
			reader.POST("/reader/change-password", authHandler.ReaderChangePassword)
			reader.POST("/reader/logout", authHandler.Logout)
		}
	}

	return r
}
