package service

import (
	"fmt"
	"library-api/internal/model"
	"library-api/internal/repository"
	"time"

	"golang.org/x/crypto/bcrypt"
)

type ReaderService struct {
	repo *repository.ReaderRepo
}

func NewReaderService(repo *repository.ReaderRepo) *ReaderService {
	return &ReaderService{repo: repo}
}

func (s *ReaderService) ListReaders(page, pageSize int) ([]model.Reader, int64, error) {
	if page < 1 {
		page = 1
	}
	if pageSize < 1 || pageSize > 100 {
		pageSize = 10
	}
	return s.repo.FindAll(page, pageSize)
}

func (s *ReaderService) GetReader(id uint) (*model.Reader, error) {
	return s.repo.FindByID(id)
}

func (s *ReaderService) CreateReader(reader *model.Reader) error {
	reader.CardNo = fmt.Sprintf("R%s%04d", time.Now().Format("20060102"), time.Now().Unix()%10000)
	reader.Status = 1
	hash, err := bcrypt.GenerateFromPassword([]byte("123456"), bcrypt.DefaultCost)
	if err != nil {
		return err
	}
	reader.PasswordHash = string(hash)
	return s.repo.Create(reader)
}

func (s *ReaderService) RegisterReader(reader *model.Reader, password string) error {
	reader.CardNo = fmt.Sprintf("R%s%04d", time.Now().Format("20060102"), time.Now().Unix()%10000)
	reader.Status = 0     // 0 = 待审核，管理员启用后变成 1
	reader.MaxBorrow = 10 // 默认最多借 10 本
	reader.ReaderType = 1 // 默认读者类型：学生

	// 把用户设置的密码加密后存库（绝不明文存）
	hash, err := bcrypt.GenerateFromPassword([]byte(password), bcrypt.DefaultCost)
	if err != nil {
		return err
	}
	reader.PasswordHash = string(hash)

	return s.repo.Create(reader)
}

func (s *ReaderService) UpdateReader(id uint, reader *model.Reader) error {
	existing, err := s.repo.FindByID(id)
	if err != nil {
		return err
	}
	reader.ID = existing.ID
	reader.CardNo = existing.CardNo
	reader.CreatedAt = existing.CreatedAt
	return s.repo.Update(reader)
}

func (s *ReaderService) SetReaderStatus(id uint, status int8) error {
	return s.repo.UpdateStatus(id, status)
}

func (s *ReaderService) DeleteReader(id uint) error {
	return s.repo.Delete(id)
}
