package service

import (
	"library-api/internal/model"

	"gorm.io/gorm"
)

type FineService struct {
	db *gorm.DB
}

func NewFineService(db *gorm.DB) *FineService {
	return &FineService{db: db}
}

func (s *FineService) CreateFine(tx *gorm.DB, readerID uint, borrowID uint, amount float64, reason string) error {
	fine := model.Fine{
		ReaderID: readerID,
		BorrowID: &borrowID,
		Amount:   amount,
		Reason:   reason,
		IsPaid:   0,
	}
	return tx.Create(&fine).Error
}
