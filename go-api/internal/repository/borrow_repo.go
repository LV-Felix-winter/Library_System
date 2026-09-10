package repository

import (
	"library-api/internal/model"
	"time"

	"gorm.io/gorm"
)

type BorrowRepo struct {
	db *gorm.DB
}

func NewBorrowRepo(db *gorm.DB) *BorrowRepo {
	return &BorrowRepo{db: db}
}

func (r *BorrowRepo) FindByID(id uint) (*model.BorrowRecord, error) {
	var record model.BorrowRecord
	err := r.db.Preload("Reader").Preload("Book").First(&record, id).Error
	return &record, err
}

func (r *BorrowRepo) FindByReader(readerID uint) ([]model.BorrowRecord, error) {
	var records []model.BorrowRecord
	err := r.db.Preload("Book").Where("reader_id = ?", readerID).
		Order("borrow_date DESC").Find(&records).Error
	return records, err
}

func (r *BorrowRepo) FindActiveByReader(readerID uint) ([]model.BorrowRecord, error) {
	var records []model.BorrowRecord
	err := r.db.Where("reader_id = ? AND status IN (1, 3)", readerID).Find(&records).Error
	return records, err
}

func (r *BorrowRepo) CountActiveByReader(readerID uint) (int64, error) {
	var count int64
	err := r.db.Model(&model.BorrowRecord{}).
		Where("reader_id = ? AND status IN (1, 3)", readerID).Count(&count).Error
	return count, err
}

func (r *BorrowRepo) FindOverdue() ([]model.BorrowRecord, error) {
	var records []model.BorrowRecord
	err := r.db.Preload("Reader").Preload("Book").
		Where("status = 3").
		Order("due_date ASC").Find(&records).Error
	return records, err
}

func (r *BorrowRepo) Create(record *model.BorrowRecord) error {
	return r.db.Create(record).Error
}

func (r *BorrowRepo) Update(record *model.BorrowRecord) error {
	return r.db.Save(record).Error
}

func (r *BorrowRepo) Transaction(fn func(tx *gorm.DB) error) error {
	return r.db.Transaction(fn)
}

func (r *BorrowRepo) GetDB() *gorm.DB {
	return r.db
}

func (r *BorrowRepo) FindAll(page, pageSize int) ([]model.BorrowRecord, int64, error) {
	var records []model.BorrowRecord
	var total int64
	r.db.Model(&model.BorrowRecord{}).Count(&total)
	err := r.db.Preload("Reader").Preload("Book").
		Order("borrow_date DESC").
		Offset((page - 1) * pageSize).Limit(pageSize).Find(&records).Error
	return records, total, err
}

func (r *BorrowRepo) FindDueSoon(days int) ([]model.BorrowRecord, error) {
	var records []model.BorrowRecord
	today := time.Now().Format("2006-01-02")
	soon := time.Now().AddDate(0, 0, days).Format("2006-01-02")
	err := r.db.Preload("Reader").Preload("Book").
		Where("status = 1 AND due_date >= ? AND due_date <= ?", today, soon).
		Order("due_date ASC").Find(&records).Error
	return records, err
}

func (r *BorrowRepo) CountBorrowedToday(readerID uint) (int64, error) {
	var count int64
	err := r.db.Model(&model.BorrowRecord{}).
		Where("reader_id = ? AND borrow_date = ?", readerID, time.Now().Format("2006-01-02")).
		Count(&count).Error
	return count, err
}
