package model

import "time"

type Admin struct {
	ID           uint       `gorm:"primaryKey" json:"id"`
	Username     string     `gorm:"size:50;not null;uniqueIndex" json:"username"`
	PasswordHash string     `gorm:"size:255;not null" json:"-"`
	RealName     string     `gorm:"size:50" json:"real_name"`
	Role         int8       `gorm:"default:2" json:"role"`
	Status       int8       `gorm:"default:1" json:"status"`
	LastLoginAt  *time.Time `json:"last_login_at"`
	CreatedAt    time.Time  `json:"created_at"`
}

func (Admin) TableName() string {
	return "admins"
}
