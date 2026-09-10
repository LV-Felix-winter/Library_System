package repository

import (
	"library-api/internal/model"

	"gorm.io/gorm"
)

type AdminRepo struct {
	db *gorm.DB
}

func NewAdminRepo(db *gorm.DB) *AdminRepo {
	return &AdminRepo{db: db}
}

func (r *AdminRepo) FindByUsername(username string) (*model.Admin, error) {
	var admin model.Admin
	err := r.db.Where("username = ? AND status = 1", username).First(&admin).Error
	return &admin, err
}

func (r *AdminRepo) FindByID(id uint) (*model.Admin, error) {
	var admin model.Admin
	err := r.db.First(&admin, id).Error
	return &admin, err
}

func (r *AdminRepo) UpdatePassword(id uint, hash string) error {
	return r.db.Model(&model.Admin{}).Where("id = ?", id).Update("password_hash", hash).Error
}

func (r *AdminRepo) FindAll() ([]model.Admin, error) {
	var admins []model.Admin
	err := r.db.Order("id ASC").Find(&admins).Error
	return admins, err
}

func (r *AdminRepo) UpdateRole(id uint, role int8) error {
	return r.db.Model(&model.Admin{}).Where("id = ?", id).Update("role", role).Error
}
