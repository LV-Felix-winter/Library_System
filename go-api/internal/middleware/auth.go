package middleware

import (
	"library-api/internal/service"
	"net/http"
	"strings"

	"github.com/gin-gonic/gin"
)

func Auth(authService *service.AuthService) gin.HandlerFunc {
	return func(c *gin.Context) {
		authHeader := c.GetHeader("Authorization")
		if authHeader == "" {
			c.JSON(http.StatusUnauthorized, gin.H{"code": 401, "message": "请先登录"})
			c.Abort()
			return
		}

		tokenString := strings.TrimPrefix(authHeader, "Bearer ")
		claims, err := authService.ValidateToken(tokenString)
		if err != nil {
			c.JSON(http.StatusUnauthorized, gin.H{"code": 401, "message": "登录已过期，请重新登录"})
			c.Abort()
			return
		}

		c.Set("admin_id", (*claims)["admin_id"])
		c.Set("username", (*claims)["username"])
		c.Set("role", (*claims)["role"])
		if (*claims)["role"] == "reader" {
			c.JSON(http.StatusForbidden, gin.H{"code": 403, "message": "无权限"})
			c.Abort()
			return
		}
		c.Next()
	}
}

func ReaderAuth(authService *service.AuthService) gin.HandlerFunc {
	return func(c *gin.Context) {
		authHeader := c.GetHeader("Authorization")
		if authHeader == "" {
			c.JSON(http.StatusUnauthorized, gin.H{"code": 401, "message": "请先登录"})
			c.Abort()
			return
		}
		tokenString := strings.TrimPrefix(authHeader, "Bearer ")
		claims, err := authService.ValidateToken(tokenString)
		if err != nil {
			c.JSON(http.StatusUnauthorized, gin.H{"code": 401, "message": "登录已过期，请重新登录"})
			c.Abort()
			return
		}
		if (*claims)["role"] != "reader" {
			c.JSON(http.StatusForbidden, gin.H{"code": 403, "message": "无权限"})
			c.Abort()
			return
		}
		c.Set("reader_id", (*claims)["reader_id"])
		c.Set("card_no", (*claims)["card_no"])
		c.Next()
	}
}
