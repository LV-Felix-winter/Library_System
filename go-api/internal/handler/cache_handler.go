package handler

import (
	"library-api/internal/cache"
	"net/http"

	"github.com/gin-gonic/gin"
)

type CacheHandler struct {
	cache *cache.Cache
}

func NewCacheHandler(c *cache.Cache) *CacheHandler {
	return &CacheHandler{cache: c}
}

// ClearCache 管理员清空所有缓存
func (h *CacheHandler) ClearCache(c *gin.Context) {
	h.cache.FlushAll()
	c.JSON(http.StatusOK, gin.H{"code": 200, "message": "缓存已清空"})
}
