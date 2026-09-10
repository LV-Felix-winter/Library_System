package model

import "time"

type Reader struct {
	ID         uint      `gorm:"primaryKey" json:"id"`
	CardNo     string    `gorm:"size:20;not null;uniqueIndex" json:"card_no"`
	Name       string    `gorm:"size:50;not null;index" json:"name"`
	Gender     int8      `gorm:"default:0" json:"gender"`
	Phone      string    `gorm:"size:20" json:"phone"`
	Email      string    `gorm:"size:100" json:"email"`
	IDCard     string    `gorm:"size:18" json:"id_card"`
	ReaderType int8      `gorm:"default:1" json:"reader_type"`
	MaxBorrow  uint      `gorm:"default:10" json:"max_borrow"`
	PasswordHash string    `gorm:"size:255;not null;default:''" json:"-"`
	Status     int8      `gorm:"default:1" json:"status"`
	CreatedAt  time.Time `json:"created_at"`
	UpdatedAt  time.Time `json:"updated_at"`
}

func (Reader) TableName() string {
	return "readers"
}
