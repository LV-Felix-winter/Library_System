package service

import (
	"library-api/internal/model"
	"library-api/internal/repository"
)

type BookService struct {
	repo *repository.BookRepo
}

func NewBookService(repo *repository.BookRepo) *BookService {
	return &BookService{repo: repo}
}

func (s *BookService) ListBooks(page, pageSize int, categoryID uint, keyword string) ([]model.Book, int64, error) {
	if page < 1 {
		page = 1
	}
	if pageSize < 1 || pageSize > 100 {
		pageSize = 10
	}
	return s.repo.FindAll(page, pageSize, categoryID, keyword)
}

func (s *BookService) GetBook(id uint) (*model.Book, error) {
	return s.repo.FindByID(id)
}

func (s *BookService) CreateBook(book *model.Book) error {
	book.AvailableCopies = book.TotalCopies
	book.Status = 1
	return s.repo.Create(book)
}

func (s *BookService) UpdateBook(id uint, book *model.Book) error {
	existing, err := s.repo.FindByID(id)
	if err != nil {
		return err
	}
	book.ID = existing.ID
	book.CreatedAt = existing.CreatedAt
	return s.repo.Update(book)
}

func (s *BookService) DeleteBook(id uint) error {
	return s.repo.Delete(id)
}

func (s *BookService) SearchBooks(keyword string) ([]model.Book, error) {
	books, _, err := s.repo.FindAll(1, 20, 0, keyword)
	return books, err
}
