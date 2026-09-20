package cache

import (
	"context"
	"encoding/json"
	"time"

	"github.com/redis/go-redis/v9"
)

var ctx = context.Background()

// Cache 封装了 Redis 客户端，提供缓存读写方法
type Cache struct {
	client *redis.Client
}

// New 创建缓存客户端。addr 例如 "localhost:6379"
func New(addr, password string, db int) *Cache {
	client := redis.NewClient(&redis.Options{
		Addr:     addr,
		Password: password,
		DB:       db,
	})
	return &Cache{client: client}
}

// Ping 检查 Redis 是否连通
func (c *Cache) Ping() error {
	return c.client.Ping(ctx).Err()
}

// GetJSON 从缓存读取 JSON 并反序列化到 dest，读不到返回 false
func (c *Cache) GetJSON(key string, dest interface{}) bool {
	val, err := c.client.Get(ctx, key).Result()
	if err != nil {
		return false
	}
	if err := json.Unmarshal([]byte(val), dest); err != nil {
		return false
	}
	return true
}

// SetJSON 把 value 序列化成 JSON 写入缓存，ttl 为过期时间
func (c *Cache) SetJSON(key string, value interface{}, ttl time.Duration) {
	data, err := json.Marshal(value)
	if err != nil {
		return
	}
	c.client.Set(ctx, key, data, ttl)
}

// Del 删除一个或多个缓存 key
func (c *Cache) Del(keys ...string) {
	c.client.Del(ctx, keys...)
}

// DelByPrefix 删除某个前缀开头的所有 key（用于批量清缓存）
func (c *Cache) DelByPrefix(prefix string) {
	iter := c.client.Scan(ctx, 0, prefix+"*", 0).Iterator()
	var keys []string
	for iter.Next(ctx) {
		keys = append(keys, iter.Val())
	}
	if len(keys) > 0 {
		c.client.Del(ctx, keys...)
	}
}

// Set 写入一个字符串值，ttl 为过期时间（用于登录会话等简单标记）
func (c *Cache) Set(key, value string, ttl time.Duration) {
	c.client.Set(ctx, key, value, ttl)
}

// Exists 判断某个 key 是否存在；Redis 异常时返回 true（放行，降级为仅靠 JWT 校验）
func (c *Cache) Exists(key string) bool {
	n, err := c.client.Exists(ctx, key).Result()
	if err != nil {
		return true
	}
	return n > 0
}

// FlushAll 清空所有缓存（管理员操作）
func (c *Cache) FlushAll() {
	c.client.FlushAll(ctx)
}
