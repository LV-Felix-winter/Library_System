package handler

import (
	"net/http"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

type StatsHandler struct {
	db *gorm.DB
}

func NewStatsHandler(db *gorm.DB) *StatsHandler {
	return &StatsHandler{db: db}
}

func (h *StatsHandler) Dashboard(c *gin.Context) {
	var totalBooks, availableBooks, activeBorrows, overdueBorrows int64

	h.db.Table("books").Where("status = 1").Count(&totalBooks)
	h.db.Table("books").Where("status = 1").Select("COALESCE(SUM(available_copies), 0)").Scan(&availableBooks)
	h.db.Table("borrow_records").Where("status = 1").Count(&activeBorrows)
	h.db.Table("borrow_records").Where("status = 3").Count(&overdueBorrows)

	c.JSON(http.StatusOK, gin.H{
		"code":    200,
		"message": "success",
		"data": gin.H{
			"total_books":     totalBooks,
			"available_books": availableBooks,
			"active_borrows":  activeBorrows,
			"overdue_borrows": overdueBorrows,
		},
	})
}
