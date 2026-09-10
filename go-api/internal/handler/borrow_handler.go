package handler

import (
	"library-api/internal/service"
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
)

type BorrowHandler struct {
	svc *service.BorrowService
}

func NewBorrowHandler(svc *service.BorrowService) *BorrowHandler {
	return &BorrowHandler{svc: svc}
}

type borrowReq struct {
	ReaderID uint `json:"reader_id" binding:"required"`
	BookID   uint `json:"book_id" binding:"required"`
}

type returnReq struct {
	BorrowID uint `json:"borrow_id" binding:"required"`
}

type renewReq struct {
	BorrowID uint `json:"borrow_id" binding:"required"`
}

func (h *BorrowHandler) Borrow(c *gin.Context) {
	var req borrowReq
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": "参数错误：请提供 reader_id 和 book_id"})
		return
	}

	record, err := h.svc.BorrowBook(req.ReaderID, req.BookID)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "借阅成功", "data": record})
}

func (h *BorrowHandler) Return(c *gin.Context) {
	var req returnReq
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": "参数错误：请提供 borrow_id"})
		return
	}

	_, err := h.svc.ReturnBook(req.BorrowID)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "归还成功"})
}

func (h *BorrowHandler) Renew(c *gin.Context) {
	var req renewReq
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": "参数错误：请提供 borrow_id"})
		return
	}

	if err := h.svc.RenewBook(req.BorrowID); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "续借成功"})
}

func (h *BorrowHandler) Records(c *gin.Context) {
	readerID, _ := strconv.ParseUint(c.Query("reader_id"), 10, 64)
	if readerID == 0 {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": "请提供 reader_id"})
		return
	}

	records, err := h.svc.ListRecords(uint(readerID))
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "success", "data": records})
}

func (h *BorrowHandler) Overdue(c *gin.Context) {
	records, err := h.svc.ListOverdue()
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "success", "data": records})
}

func (h *BorrowHandler) All(c *gin.Context) {
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	pageSize, _ := strconv.Atoi(c.DefaultQuery("page_size", "10"))

	records, total, err := h.svc.ListAllRecords(page, pageSize)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "success", "data": records, "total": total})
}

func (h *BorrowHandler) Reminders(c *gin.Context) {
	records, err := h.svc.ListDueSoon(3)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "success", "data": records})
}

func (h *BorrowHandler) ReaderRecords(c *gin.Context) {
	readerID := uint(c.GetFloat64("reader_id"))
	records, err := h.svc.ListRecords(readerID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "success", "data": records})
}

func (h *BorrowHandler) ReaderBorrow(c *gin.Context) {
	var req struct {
		BookID uint `json:"book_id" binding:"required"`
	}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": "请提供 book_id"})
		return
	}
	readerID := uint(c.GetFloat64("reader_id"))
	record, err := h.svc.BorrowBook(readerID, req.BookID)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "借阅成功", "data": record})
}

func (h *BorrowHandler) ReaderReturn(c *gin.Context) {
	var req struct {
		BorrowID uint `json:"borrow_id" binding:"required"`
	}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": "请提供 borrow_id"})
		return
	}
	readerID := uint(c.GetFloat64("reader_id"))
	if err := h.svc.ReturnBookForReader(readerID, req.BorrowID); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "归还成功"})
}
