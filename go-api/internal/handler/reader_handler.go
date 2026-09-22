package handler

import (
	"library-api/internal/model"
	"library-api/internal/service"
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
)

type ReaderHandler struct {
	svc *service.ReaderService
}

func NewReaderHandler(svc *service.ReaderService) *ReaderHandler {
	return &ReaderHandler{svc: svc}
}

func (h *ReaderHandler) List(c *gin.Context) {
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	pageSize, _ := strconv.Atoi(c.DefaultQuery("page_size", "10"))

	readers, total, err := h.svc.ListReaders(page, pageSize)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{
		"code":    200,
		"message": "success",
		"data":    readers,
		"total":   total,
	})
}

func (h *ReaderHandler) Detail(c *gin.Context) {
	id, _ := strconv.ParseUint(c.Param("id"), 10, 64)
	reader, err := h.svc.GetReader(uint(id))
	if err != nil {
		c.JSON(http.StatusNotFound, gin.H{"code": 404, "message": "读者不存在"})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "success", "data": reader})
}

func (h *ReaderHandler) Create(c *gin.Context) {
	var reader model.Reader
	if err := c.ShouldBindJSON(&reader); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": "参数错误"})
		return
	}
	if err := h.svc.CreateReader(&reader); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "注册成功", "data": reader})
}

func (h *ReaderHandler) Update(c *gin.Context) {
	id, _ := strconv.ParseUint(c.Param("id"), 10, 64)
	var reader model.Reader
	if err := c.ShouldBindJSON(&reader); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": "参数错误"})
		return
	}
	if err := h.svc.UpdateReader(uint(id), &reader); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "更新成功"})
}

func (h *ReaderHandler) UpdateStatus(c *gin.Context) {
	id, _ := strconv.ParseUint(c.Param("id"), 10, 64)
	var req struct {
		Status int8 `json:"status"`
	}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": "参数错误"})
		return
	}
	if err := h.svc.SetReaderStatus(uint(id), req.Status); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "操作成功"})
}

func (h *ReaderHandler) Delete(c *gin.Context) {
	id, _ := strconv.ParseUint(c.Param("id"), 10, 64)
	if err := h.svc.DeleteReader(uint(id)); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": "删除失败：" + err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "删除成功"})
}

// readerRegisterReq 读者自助注册的请求参数
type readerRegisterReq struct {
	Name     string `json:"name" binding:"required"`
	Password string `json:"password" binding:"required,min=6"`
	Gender   int8   `json:"gender"`
	Phone    string `json:"phone"`
	Email    string `json:"email"`
	IDCard   string `json:"id_card"`
}

// Register 读者自助注册（公开接口，注册后状态为待审核）
func (h *ReaderHandler) Register(c *gin.Context) {
	var req readerRegisterReq
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": "请填写姓名和密码（密码至少 6 位）"})
		return
	}

	reader := &model.Reader{
		Name:   req.Name,
		Gender: req.Gender,
		Phone:  req.Phone,
		Email:  req.Email,
		IDCard: req.IDCard,
	}

	if err := h.svc.RegisterReader(reader, req.Password); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": "注册失败：" + err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "注册成功，请等待管理员审核", "data": reader})
}
