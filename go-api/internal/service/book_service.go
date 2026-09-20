package service

import (
	"fmt"
	"library-api/internal/cache"
	"library-api/internal/model"
	"library-api/internal/repository"
	"time"
)

type BookService struct {
	repo  *repository.BookRepo
	cache *cache.Cache
}

func NewBookService(repo *repository.BookRepo, c *cache.Cache) *BookService {
	return &BookService{repo: repo, cache: c}
}

// bookListResult 用于把「图书列表 + 总数」一起缓存
type bookListResult struct {
	Books []model.Book `json:"books"`
	Total int64        `json:"total"`
}

func (s *BookService) ListBooks(page, pageSize int, categoryID uint, keyword string) ([]model.Book, int64, error) {
	if page < 1 {
		page = 1
	}
	if pageSize < 1 || pageSize > 100 {
		pageSize = 10
	}

	// 1. 先查缓存
	key := fmt.Sprintf("book:list:%d:%d:%d:%s", page, pageSize, categoryID, keyword)
	var cached bookListResult
	if s.cache.GetJSON(key, &cached) {
		return cached.Books, cached.Total, nil
	}

	// 2. 缓存没命中，查数据库
	books, total, err := s.repo.FindAll(page, pageSize, categoryID, keyword)
	if err != nil {
		return nil, 0, err
	}

	// 3. 写入缓存
	s.cache.SetJSON(key, bookListResult{Books: books, Total: total}, 5*time.Minute)
	return books, total, nil
}

func (s *BookService) GetBook(id uint) (*model.Book, error) {
	key := fmt.Sprintf("book:detail:%d", id)

	var book model.Book
	if s.cache.GetJSON(key, &book) {
		return &book, nil
	}

	bookPtr, err := s.repo.FindByID(id)
	if err != nil {
		return nil, err
	}
	s.cache.SetJSON(key, bookPtr, 5*time.Minute)
	return bookPtr, nil
}

func (s *BookService) CreateBook(book *model.Book) error {
	book.AvailableCopies = book.TotalCopies
	book.Status = 1
	if err := s.repo.Create(book); err != nil {
		return err
	}
	s.invalidate(book.ID)
	return nil
}

func (s *BookService) UpdateBook(id uint, book *model.Book) error {
	existing, err := s.repo.FindByID(id)
	if err != nil {
		return err
	}
	book.ID = existing.ID
	book.CreatedAt = existing.CreatedAt
	if err := s.repo.Update(book); err != nil {
		return err
	}
	s.invalidate(id)
	return nil
}

func (s *BookService) DeleteBook(id uint) error {
	if err := s.repo.Delete(id); err != nil {
		return err
	}
	s.invalidate(id)
	return nil
}

func (s *BookService) SearchBooks(keyword string) ([]model.Book, error) {
	books, _, err := s.repo.FindAll(1, 20, 0, keyword)
	return books, err
}

// invalidate 清除与图书相关的缓存
func (s *BookService) invalidate(id uint) {
	s.cache.Del(fmt.Sprintf("book:detail:%d", id))
	s.cache.DelByPrefix("book:list:")
	s.cache.Del("stats:dashboard")
}
