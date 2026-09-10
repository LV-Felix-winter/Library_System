package model

import "time"

type BorrowRecord struct {
	ID         uint      `gorm:"primaryKey" json:"id"`
	ReaderID   uint      `gorm:"not null;index" json:"reader_id"`
	BookID     uint      `gorm:"not null;index" json:"book_id"`
	BorrowDate string    `gorm:"type:date;not null" json:"borrow_date"`
	DueDate    string    `gorm:"type:date;not null;index" json:"due_date"`
	ReturnDate *string   `gorm:"type:date" json:"return_date"`
	Status     int8      `gorm:"default:1;index" json:"status"`
	RenewCount int8      `gorm:"default:0" json:"renew_count"`
	FineAmount float64   `gorm:"type:decimal(8,2)" json:"fine_amount"`
	CreatedAt  time.Time `json:"created_at"`
	UpdatedAt  time.Time `json:"updated_at"`

	Reader Reader `gorm:"foreignKey:ReaderID" json:"reader,omitempty"`
	Book   Book   `gorm:"foreignKey:BookID" json:"book,omitempty"`
}

func (BorrowRecord) TableName() string {
	return "borrow_records"
}
