package repository

import (
	"library-api/internal/model"

	"gorm.io/gorm"
)

type ReaderRepo struct {
	db *gorm.DB
}

func NewReaderRepo(db *gorm.DB) *ReaderRepo {
	return &ReaderRepo{db: db}
}

func (r *ReaderRepo) FindAll(page, pageSize int) ([]model.Reader, int64, error) {
	var readers []model.Reader
	var total int64

	r.db.Model(&model.Reader{}).Count(&total)
	err := r.db.Offset((page - 1) * pageSize).Limit(pageSize).Find(&readers).Error
	return readers, total, err
}

func (r *ReaderRepo) FindByID(id uint) (*model.Reader, error) {
	var reader model.Reader
	err := r.db.First(&reader, id).Error
	return &reader, err
}

func (r *ReaderRepo) FindByCardNo(cardNo string) (*model.Reader, error) {
	var reader model.Reader
	err := r.db.Where("card_no = ?", cardNo).First(&reader).Error
	return &reader, err
}

func (r *ReaderRepo) Create(reader *model.Reader) error {
	return r.db.Create(reader).Error
}

func (r *ReaderRepo) Update(reader *model.Reader) error {
	return r.db.Save(reader).Error
}

func (r *ReaderRepo) UpdateStatus(id uint, status int8) error {
	return r.db.Model(&model.Reader{}).Where("id = ?", id).Update("status", status).Error
}

func (r *ReaderRepo) Delete(id uint) error {
	return r.db.Delete(&model.Reader{}, id).Error
}

func (r *ReaderRepo) UpdatePassword(id uint, hash string) error {
	return r.db.Model(&model.Reader{}).Where("id = ?", id).Update("password_hash", hash).Error
}
