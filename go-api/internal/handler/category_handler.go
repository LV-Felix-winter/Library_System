package handler

import (
	"library-api/internal/cache"
	"library-api/internal/model"
	"library-api/internal/repository"
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
)

type CategoryHandler struct {
	repo  *repository.CategoryRepo
	cache *cache.Cache
}

func NewCategoryHandler(repo *repository.CategoryRepo, c *cache.Cache) *CategoryHandler {
	return &CategoryHandler{repo: repo, cache: c}
}

func (h *CategoryHandler) List(c *gin.Context) {
	const key = "category:list"

	var categories []model.Category
	if h.cache.GetJSON(key, &categories) {
		if categories == nil {
			categories = []model.Category{}
		}
		c.JSON(http.StatusOK, gin.H{"code": 200, "message": "success", "data": categories})
		return
	}

	categories, err := h.repo.FindAll()
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	if categories == nil {
		categories = []model.Category{}
	}
	h.cache.SetJSON(key, categories, 10*time.Minute)
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "success", "data": categories})
}

func (h *CategoryHandler) Create(c *gin.Context) {
	var category model.Category
	if err := c.ShouldBindJSON(&category); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": "参数错误"})
		return
	}
	if err := h.repo.Create(&category); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	h.cache.Del("category:list")
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "添加成功", "data": category})
}

func (h *CategoryHandler) Update(c *gin.Context) {
	id, _ := strconv.ParseUint(c.Param("id"), 10, 64)
	var category model.Category
	if err := c.ShouldBindJSON(&category); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "message": "参数错误"})
		return
	}
	category.ID = uint(id)
	if err := h.repo.Update(&category); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	h.cache.Del("category:list")
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "更新成功"})
}

func (h *CategoryHandler) Delete(c *gin.Context) {
	id, _ := strconv.ParseUint(c.Param("id"), 10, 64)
	if err := h.repo.Delete(uint(id)); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "message": err.Error()})
		return
	}
	h.cache.Del("category:list")
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "删除成功"})
}
