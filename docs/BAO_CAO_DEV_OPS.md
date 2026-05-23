# BÁO CÁO ĐỀ TÀI: THIẾT KẾ VÀ TRIỂN KHAI HỆ THỐNG CI/CD CHO WEBSITE WORDPRESS (THỰC TẾ DOANH NGHIỆP)

---

## 📝 THÔNG TIN CHUNG
- **Môn học**: Nhập môn DevOps (COMP1704)
- **Học kỳ**: 1 - Năm học: 2025 - 2026
- **Nhóm thực hiện**: [Tên nhóm] (2 - 5 thành viên)

---

## 1. GIỚI THIỆU & PHÂN TÍCH BÀI TOÁN

### 1.1. Mô tả bài toán
- WordPress là hệ quản trị nội dung (CMS) phổ biến nhất thế giới. Tuy nhiên, việc quản lý và phát triển WordPress trong môi trường doanh nghiệp thường gặp các vấn đề:
  - Triển khai thủ công qua FTP dễ lỗi và mất thời gian.
  - Thiếu quy trình kiểm thử trước khi cập nhật mã nguồn (gây lỗi trang trắng - WSOD).
  - Không có hệ thống theo dõi logs lỗi của người dùng và giám sát hiệu năng server.
- **Mục tiêu**: Xây dựng một quy trình DevOps hoàn chỉnh bao gồm Source Control, CI/CD Pipeline (Build, Test, Deploy tự động) và hệ thống Monitoring/Logging (Prometheus, Grafana, ELK) cho website WordPress.

### 1.2. Lý do lựa chọn công nghệ
- **Git/GitHub**: Quản lý phiên bản mã nguồn, phối hợp làm việc nhóm hiệu quả.
- **GitHub Actions**: Pipeline CI/CD tự động, tích hợp sẵn với hệ sinh thái GitHub.
- **Docker & Docker Compose**: Đóng gói ứng dụng WordPress và Database tách biệt, đảm bảo tính nhất quán giữa môi trường Staging và Production.
- **Watchtower**: Tự động hóa quá trình CD trên Production bằng cách tự động cập nhật container khi phát hiện image mới trên Docker Hub.
- **Prometheus & Grafana**: Giám sát tài nguyên, lượng request của ứng dụng WordPress.
- **ELK Stack (Elasticsearch, Logstash, Kibana, Filebeat)**: Tập trung hóa dữ liệu logs của Apache và PHP từ container WordPress.

---

## 2. THIẾT KẾ HỆ THỐNG

### 2.1. Kiến trúc hệ thống
*(Vẽ sơ đồ kiến trúc bằng công cụ draw.io hoặc sử dụng diagram trong báo cáo)*

```
Developer (Code Push) ──> GitHub (Source Control)
                            │
                            ▼
                     GitHub Actions (CI/CD)
                       ├── Run Tests (PHPUnit)
                       ├── Linting (PHPCS)
                       └── Build & Push Image ──> Docker Hub
                                                    │
                                                    ▼
                                           Deploy (Watchtower CD)
                                                    │
                                                    ▼
                                            Production Server
                                          (WordPress + MySQL)
```

### 2.2. Quy trình CI/CD Pipeline
- **CI (Tích hợp liên tục)**: Khi push/PR lên branch `main`, hệ thống tự động:
  - Chạy bộ kiểm thử đơn vị & tích hợp (PHPUnit).
  - Kiểm tra tiêu chuẩn viết code (Linter với PHPCS).
  - Đóng gói mã nguồn thành Docker Image và đẩy lên Docker Hub.
- **CD (Triển khai liên tục)**: Watchtower định kỳ kiểm tra Docker Hub, tự động kéo (pull) image mới nhất và tái khởi động container Production một cách an toàn.

---

## 3. TRIỂN KHAI THỰC TẾ

### 3.1. Cấu trúc mã nguồn & Dockerization
- Phân tích tệp `Dockerfile` kế thừa từ `wordpress:6.4-php8.2-apache`.
- Phân tích tệp `docker-compose.yml` (Staging + Monitoring) và `docker-compose.prod.yml` (Production).

### 3.2. Cấu hình Pipeline (GitHub Actions)
- Trình bày chi tiết nội dung file `.github/workflows/ci.yml`.
- Giải thích các Job: `unit-test`, `code-quality`, `build-and-push`.

---

## 4. KIỂM THỰ VÀ ĐẢM BẢO CHẤT LƯỢNG

### 4.1. Kiểm thử tự động (PHPUnit)
- Trình bày các test case trong file `tests/WpTest.php`:
  - `testDatabaseConnection`: Kiểm thử tích hợp kết nối Database.
  - `testWpConfigExists`: Kiểm thử cấu hình môi trường WordPress.

### 4.2. Phân tích mã nguồn tĩnh (Static Code Analysis)
- Sử dụng PHPCS để kiểm tra định dạng code chuẩn PSR2.
- Bảo đảm chất lượng mã nguồn trước khi đóng gói.

---

## 5. TRIỂN KHAI & VẬN HÀNH

### 5.1. Môi trường Staging và Production
- Cách khởi chạy song song 2 môi trường bằng tham số `-p` (Project Name) trong Docker Compose để tránh xung đột cổng và tài nguyên.
- Kiểm tra các cổng dịch vụ (WordPress Staging: 8082, Production: 80).

### 5.2. Hệ thống Giám sát & Ghi log (Monitoring & Logging)
- **Giám sát**: Prometheus thu thập metrics từ container WordPress, hiển thị biểu đồ trực quan qua Grafana Dashboard.
- **Ghi log**: Filebeat đọc log Apache từ container WordPress, đẩy về Logstash/Elasticsearch và hiển thị trên Kibana.

---

## 6. ĐÁNH GIÁ & THẢO LUẬN
- **Ưu điểm**:
  - Tự động hóa hoàn toàn từ code đến deploy (Zero-touch deployment).
  - Đảm bảo an toàn nhờ kiểm thử tự động trước khi build.
  - Khả năng giám sát và xử lý sự cố nhanh chóng nhờ ELK và Prometheus/Grafana.
- **Hạn chế**: Watchtower kéo image định kỳ có độ trễ nhỏ (có thể tối ưu bằng Webhook).
- **Hướng phát triển**: Triển khai lên cụm Kubernetes (K8s) để tự động scale ứng dụng khi lượng truy cập tăng đột biến.
