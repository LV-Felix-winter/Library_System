package repository

import (
	"library-api/internal/model"

	"gorm.io/gorm"
)

type CategoryRepo struct {
	db *gorm.DB
}

func NewCategoryRepo(db *gorm.DB) *CategoryRepo {
	return &CategoryRepo{db: db}
}

func (r *CategoryRepo) FindAll() ([]model.Category, error) {
	var categories []model.Category
	err := r.db.Order("sort_order ASC").Find(&categories).Error
	return categories, err
}

func (r *CategoryRepo) FindByID(id uint) (*model.Category, error) {
	var category model.Category
	err := r.db.First(&category, id).Error
	return &category, err
}

func (r *CategoryRepo) Create(category *model.Category) error {
	return r.db.Create(category).Error
}

func (r *CategoryRepo) Update(category *model.Category) error {
	return r.db.Save(category).Error
}

func (r *CategoryRepo) Delete(id uint) error {
	return r.db.Delete(&model.Category{}, id).Error
}
