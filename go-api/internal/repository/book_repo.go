package repository

import (
	"library-api/internal/model"

	"gorm.io/gorm"
)

type BookRepo struct {
	db *gorm.DB
}

func NewBookRepo(db *gorm.DB) *BookRepo {
	return &BookRepo{db: db}
}

func (r *BookRepo) FindAll(page, pageSize int, categoryID uint, keyword string) ([]model.Book, int64, error) {
	var books []model.Book
	var total int64

	query := r.db.Model(&model.Book{}).Where("status = 1 AND is_deleted = 0")
	if categoryID > 0 {
		query = query.Where("category_id = ?", categoryID)
	}
	if keyword != "" {
		query = query.Where("title LIKE ? OR author LIKE ? OR isbn LIKE ?",
			"%"+keyword+"%", "%"+keyword+"%", "%"+keyword+"%")
	}

	query.Count(&total)
	err := query.Offset((page - 1) * pageSize).Limit(pageSize).Find(&books).Error
	return books, total, err
}

func (r *BookRepo) FindByID(id uint) (*model.Book, error) {
	var book model.Book
	err := r.db.Where("id = ? AND is_deleted = 0", id).First(&book).Error
	return &book, err
}

func (r *BookRepo) Create(book *model.Book) error {
	return r.db.Create(book).Error
}

func (r *BookRepo) Update(book *model.Book) error {
	return r.db.Save(book).Error
}

func (r *BookRepo) Delete(id uint) error {
	return r.db.Model(&model.Book{}).Where("id = ?", id).Update("is_deleted", 1).Error
}

func (r *BookRepo) UpdateAvailableCopies(id uint, delta int) error {
	return r.db.Model(&model.Book{}).Where("id = ?", id).
		UpdateColumn("available_copies", gorm.Expr("available_copies + ?", delta)).Error
}
