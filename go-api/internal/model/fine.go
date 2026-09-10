package model

import "time"

type Fine struct {
	ID        uint       `gorm:"primaryKey" json:"id"`
	ReaderID  uint       `gorm:"not null" json:"reader_id"`
	BorrowID  *uint      `json:"borrow_id"`
	Amount    float64    `gorm:"type:decimal(8,2);not null" json:"amount"`
	Reason    string     `gorm:"size:200;not null" json:"reason"`
	IsPaid    int8       `gorm:"default:0" json:"is_paid"`
	PaidAt    *time.Time `json:"paid_at"`
	CreatedAt time.Time  `json:"created_at"`

	Reader Reader        `gorm:"foreignKey:ReaderID" json:"reader,omitempty"`
	Borrow *BorrowRecord `gorm:"foreignKey:BorrowID" json:"borrow,omitempty"`
}

func (Fine) TableName() string {
	return "fines"
}
