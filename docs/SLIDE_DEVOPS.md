# SLIDE THUYẾT TRÌNH BÁO CÁO CUỐI KỲ DEVOPS

---

## 🖥️ SLIDE 1: GIỚI THIỆU ĐỀ TÀI & THÀNH VIÊN NHÓM
- **Tiêu đề**: Xây dựng hệ thống CI/CD cho ứng dụng WordPress thực tế doanh nghiệp.
- **Môn học**: Nhập môn DevOps - COMP1704.
- **Thành viên**:
  1. [Thành viên A] - Vị trí: DevOps Engineer (Cấu hình CI/CD, Docker)
  2. [Thành viên B] - Vị trí: Tester & Analyst (Viết tests, Cấu hình Logging/Monitoring)
  3. [Thành viên C] - Vị trí: System Administrator (Quản trị hạ tầng, Production)

---

## 🖥️ SLIDE 2: ĐẶT VẤN ĐỀ & MỤC TIÊU ĐỀ TÀI
- **Thực trạng**: WordPress thường được deploy thủ công qua FTP, dễ lỗi, khó rollback và thiếu kiểm thử.
- **Mục tiêu**:
  - Tự động hóa quy trình tích hợp và triển khai (CI/CD).
  - Đảm bảo chất lượng bằng Kiểm thử tự động (PHPUnit, PHPCS).
  - Giám sát toàn diện tài nguyên (Prometheus/Grafana) và quản lý log tập trung (ELK Stack).

---

## 🖥️ SLIDE 3: KIẾN TRÚC HỆ THỐNG (SYSTEM ARCHITECTURE)
- **Sơ đồ luồng**:
  - Code (GitHub) ➔ CI Pipeline (GitHub Actions) ➔ Container Registry (Docker Hub) ➔ CD (Watchtower) ➔ Production (Docker Host).
- **Phân chia môi trường**:
  - Môi trường Staging: WordPress (Port 8082) + Prometheus/Grafana.
  - Môi trường Production: WordPress (Port 80) + Watchtower.

---

## 🖥️ SLIDE 4: ĐÓNG GÓI ỨNG DỤNG BẰNG DOCKER
- **Dockerfile**: Sử dụng base image `wordpress:6.4-php8.2-apache`. Đóng gói mã nguồn và thiết lập quyền `www-data`.
- **Docker Compose**:
  - Quản lý độc lập WordPress container và MySQL container.
  - Sử dụng volumes để lưu trữ dữ liệu bền vững (database data, wp-content uploads).

---

## 🖥️ SLIDE 5: PIPELINE CI (CONTINUOUS INTEGRATION)
- **Công cụ**: GitHub Actions.
- **Các giai đoạn chính**:
  1. **Checkout Code**: Kéo mã nguồn từ GitHub.
  2. **Unit & Integration Test**: Chạy PHPUnit để phát hiện lỗi logic sớm.
  3. **Static Code Analysis**: Chạy PHPCS kiểm tra chuẩn viết code PSR2.
  4. **Build & Push**: Tự động build Docker Image và đẩy lên Docker Hub.

---

## 🖥️ SLIDE 6: KIỂM THỬ TỰ ĐỘNG & ĐẢM BẢO CHẤT LƯỢNG (CLO3)
- **PHPUnit Tests**:
  - `testDatabaseConnection`: Kiểm tra kết nối từ WordPress đến MySQL Database.
  - `testWpConfigExists`: Đảm bảo file cấu hình được tạo đúng.
- **PHPCS Check**:
  - Đảm bảo toàn bộ code tuân thủ chuẩn PSR2.
  - Ngăn ngừa lỗi định dạng code và nâng cao khả năng bảo trì.

---

## 🖥️ SLIDE 7: PIPELINE CD & TRIỂN KHAI TỰ ĐỘNG (CLO4 & CLO5)
- **Thách thức**: Triển khai tự động mà không cần lưu SSH key nhạy cảm của Production server trên GitHub (nếu muốn tối ưu bảo mật).
- **Giải pháp**: **Watchtower CD**.
- **Cơ chế hoạt động**:
  - Watchtower container chạy ngầm trên Production server.
  - Quét Docker Hub định kỳ (ví dụ: mỗi 5 phút hoặc qua webhook).
  - Tự động pull image mới, stop container cũ và khởi chạy container mới một cách mượt mà.

---

## 🖥️ SLIDE 8: HỆ THỐNG GIÁM SÁT HIỆU NĂNG (MONITORING)
- **Prometheus**: Thu thập các thông số tài nguyên hệ thống (CPU, RAM, Network) và HTTP requests.
- **Grafana**:
  - Kết nối dữ liệu từ Prometheus.
  - Trực quan hóa qua Dashboard (lượng request thành công/thất bại, thời gian phản hồi, trạng thái server).

---

## 🖥️ SLIDE 9: HỆ THỐNG QUẢN LÝ LOG TẬP TRUNG (LOGGING)
- **Vấn đề**: Log của Apache và PHP nằm rải rác trong container, khó debug khi có sự cố lớn.
- **Giải pháp ELK**:
  - **Filebeat**: Đóng vai trò agent thu thập log Apache từ container WordPress.
  - **Elasticsearch**: Lưu trữ và đánh chỉ mục logs.
  - **Kibana**: Dashboard trực quan để tìm kiếm và lọc logs (ví dụ: tìm log lỗi 500, lỗi kết nối DB).

---

## 🖥️ SLIDE 10: DEMO HỆ THỐNG
- Trình diễn thực tế:
  1. Thực hiện một thay đổi nhỏ trong code WordPress ➔ Push lên GitHub.
  2. GitHub Actions tự động chạy tests ➔ Build thành công ➔ Đẩy image lên Docker Hub.
  3. Watchtower trên server phát hiện bản cập nhật ➔ Tự động cập nhật WordPress Production lên bản mới mà không cần can thiệp thủ công.
  4. Xem biểu đồ Monitor trên Grafana & Kibana logs.

---

## 🖥️ SLIDE 11: ƯU ĐIỂM - HẠN CHẾ & HƯỚNG PHÁT TRIỂN
- **Ưu điểm**: Hoàn toàn tự động, phát hiện lỗi sớm, bảo mật cao, giám sát tốt.
- **Hạn chế**: Watchtower cập nhật tự động có thể gặp rủi ro nếu khâu test CI chưa bao phủ hết các edge cases.
- **Hướng phát triển**:
  - Tích hợp thêm khâu kiểm thử bảo mật tự động (SAST/DAST).
  - Chuyển đổi hạ tầng lên Kubernetes (K8s) cho các website tải cao.

---

## 🖥️ SLIDE 12: Q&A - CẢM ƠN
- Tóm tắt kết quả đạt được.
- Lời cảm ơn thầy cô và các bạn đã lắng nghe.
- Nhận câu hỏi từ Hội đồng chấm thi.
