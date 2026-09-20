package handler

import (
	"library-api/internal/cache"
	"net/http"
	"time"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

type StatsHandler struct {
	db    *gorm.DB
	cache *cache.Cache
}

func NewStatsHandler(db *gorm.DB, c *cache.Cache) *StatsHandler {
	return &StatsHandler{db: db, cache: c}
}

func (h *StatsHandler) Dashboard(c *gin.Context) {
	const key = "stats:dashboard"

	var data gin.H
	if h.cache.GetJSON(key, &data) {
		c.JSON(http.StatusOK, gin.H{"code": 200, "message": "success", "data": data})
		return
	}

	var totalBooks, availableBooks, activeBorrows, overdueBorrows int64
	h.db.Table("books").Where("status = 1").Count(&totalBooks)
	h.db.Table("books").Where("status = 1").Select("COALESCE(SUM(available_copies), 0)").Scan(&availableBooks)
	h.db.Table("borrow_records").Where("status = 1").Count(&activeBorrows)
	h.db.Table("borrow_records").Where("status = 3").Count(&overdueBorrows)

	data = gin.H{
		"total_books":     totalBooks,
		"available_books": availableBooks,
		"active_borrows":  activeBorrows,
		"overdue_borrows": overdueBorrows,
	}
	h.cache.SetJSON(key, data, 1*time.Minute)
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "success", "data": data})
}
