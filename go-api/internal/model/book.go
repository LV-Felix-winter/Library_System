package model

import "time"

type Book struct {
	ID              uint      `gorm:"primaryKey" json:"id"`
	ISBN            string    `gorm:"size:20;not null;uniqueIndex" json:"isbn"`
	Title           string    `gorm:"size:200;not null;index" json:"title"`
	Author          string    `gorm:"size:100;not null;index" json:"author"`
	Publisher       string    `gorm:"size:100" json:"publisher"`
	PublishYear     uint      `json:"publish_year"`
	CategoryID      uint      `gorm:"default:0;index" json:"category_id"`
	TotalCopies     uint      `gorm:"default:1" json:"total_copies"`
	AvailableCopies uint      `gorm:"default:1" json:"available_copies"`
	Price           float64   `gorm:"type:decimal(10,2)" json:"price"`
	ShelfLocation   string    `gorm:"size:50" json:"shelf_location"`
	Description     string    `gorm:"type:text" json:"description"`
	CoverURL        string    `gorm:"size:255" json:"cover_url"`
	Status          int8      `gorm:"default:1" json:"status"`
	IsDeleted       int8      `gorm:"default:0" json:"is_deleted"`
	CreatedAt       time.Time `json:"created_at"`
	UpdatedAt       time.Time `json:"updated_at"`
}

func (Book) TableName() string {
	return "books"
}
