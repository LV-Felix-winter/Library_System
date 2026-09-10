package service

import (
	"errors"
	"library-api/internal/model"
	"library-api/internal/repository"
	"time"

	"github.com/golang-jwt/jwt/v5"
	"golang.org/x/crypto/bcrypt"
)

var jwtSecret = []byte("library-system-secret-key-2026")

type AuthService struct {
	adminRepo  *repository.AdminRepo
	readerRepo *repository.ReaderRepo
}

func NewAuthService(adminRepo *repository.AdminRepo, readerRepo *repository.ReaderRepo) *AuthService {
	return &AuthService{adminRepo: adminRepo, readerRepo: readerRepo}
}

func (s *AuthService) Login(username, password string) (string, error) {
	admin, err := s.adminRepo.FindByUsername(username)
	if err != nil {
		return "", errors.New("用户名或密码错误")
	}

	if err := bcrypt.CompareHashAndPassword([]byte(admin.PasswordHash), []byte(password)); err != nil {
		return "", errors.New("用户名或密码错误")
	}

	token := jwt.NewWithClaims(jwt.SigningMethodHS256, jwt.MapClaims{
		"admin_id": admin.ID,
		"username": admin.Username,
		"role":     admin.Role,
		"exp":      time.Now().Add(24 * time.Hour).Unix(),
	})

	tokenString, err := token.SignedString(jwtSecret)
	return tokenString, err
}

func (s *AuthService) ValidateToken(tokenString string) (*jwt.MapClaims, error) {
	token, err := jwt.Parse(tokenString, func(t *jwt.Token) (interface{}, error) {
		return jwtSecret, nil
	})
	if err != nil {
		return nil, err
	}
	claims, ok := token.Claims.(jwt.MapClaims)
	if !ok || !token.Valid {
		return nil, errors.New("无效的 token")
	}
	return &claims, nil
}

func (s *AuthService) ChangePassword(adminID uint, oldPassword, newPassword string) error {
	admin, err := s.adminRepo.FindByID(adminID)
	if err != nil {
		return errors.New("管理员不存在")
	}
	if err := bcrypt.CompareHashAndPassword([]byte(admin.PasswordHash), []byte(oldPassword)); err != nil {
		return errors.New("旧密码错误")
	}
	hash, err := bcrypt.GenerateFromPassword([]byte(newPassword), bcrypt.DefaultCost)
	if err != nil {
		return err
	}
	return s.adminRepo.UpdatePassword(adminID, string(hash))
}

func (s *AuthService) ListAdmins() ([]model.Admin, error) {
	return s.adminRepo.FindAll()
}

func (s *AuthService) UpdateRole(id uint, role int8) error {
	return s.adminRepo.UpdateRole(id, role)
}

// ReaderLogin 读者登录（借书证号 + 密码）
func (s *AuthService) ReaderLogin(cardNo, password string) (string, error) {
	reader, err := s.readerRepo.FindByCardNo(cardNo)
	if err != nil {
		return "", errors.New("借书证号或密码错误")
	}
	if reader.Status != 1 {
		return "", errors.New("读者账号已被停用")
	}
	if reader.PasswordHash == "" {
		// 旧数据没有密码哈希，默认密码 123456
		if password != "123456" {
			return "", errors.New("借书证号或密码错误")
		}
	} else if err := bcrypt.CompareHashAndPassword([]byte(reader.PasswordHash), []byte(password)); err != nil {
		return "", errors.New("借书证号或密码错误")
	}

	token := jwt.NewWithClaims(jwt.SigningMethodHS256, jwt.MapClaims{
		"reader_id": reader.ID,
		"card_no":   reader.CardNo,
		"role":      "reader",
		"exp":       time.Now().Add(24 * time.Hour).Unix(),
	})
	return token.SignedString(jwtSecret)
}

// ReaderChangePassword 读者修改自己的密码
func (s *AuthService) ReaderChangePassword(readerID uint, oldPassword, newPassword string) error {
	reader, err := s.readerRepo.FindByID(readerID)
	if err != nil {
		return errors.New("读者不存在")
	}
	if reader.PasswordHash == "" {
		if oldPassword != "123456" {
			return errors.New("旧密码错误")
		}
	} else if err := bcrypt.CompareHashAndPassword([]byte(reader.PasswordHash), []byte(oldPassword)); err != nil {
		return errors.New("旧密码错误")
	}
	hash, err := bcrypt.GenerateFromPassword([]byte(newPassword), bcrypt.DefaultCost)
	if err != nil {
		return err
	}
	return s.readerRepo.UpdatePassword(readerID, string(hash))
}
