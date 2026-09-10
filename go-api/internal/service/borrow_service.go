package service

import (
	"errors"
	"fmt"
	"library-api/internal/model"
	"library-api/internal/repository"
	"time"

	"gorm.io/gorm"
)

type BorrowService struct {
	borrowRepo  *repository.BorrowRepo
	bookRepo    *repository.BookRepo
	readerRepo  *repository.ReaderRepo
	fineService *FineService
}

func NewBorrowService(
	borrowRepo *repository.BorrowRepo,
	bookRepo *repository.BookRepo,
	readerRepo *repository.ReaderRepo,
	fineService *FineService,
) *BorrowService {
	return &BorrowService{
		borrowRepo:  borrowRepo,
		bookRepo:    bookRepo,
		readerRepo:  readerRepo,
		fineService: fineService,
	}
}

// BorrowBook 借书
func (s *BorrowService) BorrowBook(readerID, bookID uint) (*model.BorrowRecord, error) {
	// 1. 校验读者是否存在且正常
	reader, err := s.readerRepo.FindByID(readerID)
	if err != nil {
		return nil, errors.New("读者不存在")
	}
	if reader.Status != 1 {
		return nil, errors.New("读者账号已被停用")
	}

	// 2. 校验读者是否已达借阅上限
	activeCount, _ := s.borrowRepo.CountActiveByReader(readerID)
	if activeCount >= int64(reader.MaxBorrow) {
		return nil, fmt.Errorf("已达到最大借阅数量（%d本）", reader.MaxBorrow)
	}

	// 每天最多借 5 本
	todayCount, _ := s.borrowRepo.CountBorrowedToday(readerID)
	if todayCount >= 5 {
		return nil, errors.New("今天借书已达上限（每天最多借 5 本）")
	}

	// 3. 校验图书是否存在且可借
	book, err := s.bookRepo.FindByID(bookID)
	if err != nil {
		return nil, errors.New("图书不存在")
	}
	if book.Status != 1 {
		return nil, errors.New("图书已下架")
	}
	if book.AvailableCopies == 0 {
		return nil, errors.New("暂无库存，无法借阅")
	}

	// 4. 计算借阅日期和应还日期（30天）
	today := time.Now().Format("2006-01-02")
	dueDate := time.Now().AddDate(0, 0, 30).Format("2006-01-02")

	var record *model.BorrowRecord

	// 5. 开启事务：扣库存 + 创建借阅记录（保证数据一致性）
	err = s.borrowRepo.Transaction(func(tx *gorm.DB) error {
		result := tx.Model(&model.Book{}).
			Where("id = ? AND available_copies > 0", bookID).
			UpdateColumn("available_copies", gorm.Expr("available_copies - 1"))
		if result.Error != nil {
			return errors.New("扣减库存失败")
		}
		if result.RowsAffected == 0 {
			return errors.New("暂无库存")
		}

		record = &model.BorrowRecord{
			ReaderID:   readerID,
			BookID:     bookID,
			BorrowDate: today,
			DueDate:    dueDate,
			Status:     1, // 1=借阅中
		}
		return tx.Create(record).Error
	})

	if err != nil {
		return nil, err
	}
	return record, nil
}

// ReturnBook 还书
func (s *BorrowService) ReturnBook(borrowID uint) (*model.BorrowRecord, error) {
	record, err := s.borrowRepo.FindByID(borrowID)
	if err != nil {
		return nil, errors.New("借阅记录不存在")
	}
	if record.Status == 2 {
		return nil, errors.New("该书已归还，无需重复操作")
	}
	if record.Status == 4 {
		return nil, errors.New("该书已标记为丢失，请联系管理员处理")
	}

	today := time.Now()
	todayStr := today.Format("2006-01-02")

	err = s.borrowRepo.Transaction(func(tx *gorm.DB) error {
		// 恢复库存
		if err := tx.Model(&model.Book{}).Where("id = ?", record.BookID).
			UpdateColumn("available_copies", gorm.Expr("available_copies + 1")).Error; err != nil {
			return errors.New("恢复库存失败")
		}

		// 检查是否逾期，计算罚款（每天0.5元）
		dueDate, _ := time.Parse("2006-01-02", record.DueDate)
		if today.After(dueDate) {
			overdueDays := int(today.Sub(dueDate).Hours() / 24)
			fineAmount := float64(overdueDays) * 0.5
			if fineAmount > 0 {
				reason := fmt.Sprintf("逾期归还，逾期%d天（应还日期：%s）", overdueDays, record.DueDate)
				if err := s.fineService.CreateFine(tx, record.ReaderID, borrowID, fineAmount, reason); err != nil {
					return err
				}
				tx.Model(&model.BorrowRecord{}).Where("id = ?", borrowID).
					Update("fine_amount", fineAmount)
			}
		}

		// 更新借阅记录为"已还"
		return tx.Model(&model.BorrowRecord{}).Where("id = ?", borrowID).
			Updates(map[string]interface{}{
				"status":      2,
				"return_date": todayStr,
			}).Error
	})

	if err != nil {
		return nil, err
	}
	return record, nil
}

// RenewBook 续借（延长30天，最多续借2次）
func (s *BorrowService) RenewBook(borrowID uint) error {
	record, err := s.borrowRepo.FindByID(borrowID)
	if err != nil {
		return errors.New("借阅记录不存在")
	}
	if record.Status == 3 {
		return errors.New("已逾期的图书不能续借，请先归还")
	}
	if record.RenewCount >= 2 {
		return errors.New("续借次数已达上限（最多2次）")
	}

	newDueDate := time.Now().AddDate(0, 0, 30).Format("2006-01-02")
	return s.borrowRepo.GetDB().Model(&model.BorrowRecord{}).Where("id = ?", borrowID).
		Updates(map[string]interface{}{
			"due_date":    newDueDate,
			"renew_count": gorm.Expr("renew_count + 1"),
		}).Error
}

// ListRecords 查询某读者的借阅记录
func (s *BorrowService) ListRecords(readerID uint) ([]model.BorrowRecord, error) {
	return s.borrowRepo.FindByReader(readerID)
}

// ListOverdue 查询所有逾期未还的记录
func (s *BorrowService) ListOverdue() ([]model.BorrowRecord, error) {
	return s.borrowRepo.FindOverdue()
}

// ListAllRecords 查询所有借阅记录（分页）
func (s *BorrowService) ListAllRecords(page, pageSize int) ([]model.BorrowRecord, int64, error) {
	if page < 1 {
		page = 1
	}
	if pageSize < 1 || pageSize > 100 {
		pageSize = 10
	}
	return s.borrowRepo.FindAll(page, pageSize)
}

// ListDueSoon 查询 N 天内到期的借阅记录（提醒用）
func (s *BorrowService) ListDueSoon(days int) ([]model.BorrowRecord, error) {
	return s.borrowRepo.FindDueSoon(days)
}

// ReturnBookForReader 读者自助还书（只能还自己的记录）
func (s *BorrowService) ReturnBookForReader(readerID, borrowID uint) error {
	record, err := s.borrowRepo.FindByID(borrowID)
	if err != nil {
		return errors.New("借阅记录不存在")
	}
	if record.ReaderID != readerID {
		return errors.New("只能归还自己的借阅记录")
	}
	_, err = s.ReturnBook(borrowID)
	return err
}
