# Nhật ký Lỗi hệ thống (ERRORS.md)

Mọi lỗi phát sinh trong quá trình xây dựng, kiểm thử và vận hành hệ thống được ghi lại tại đây để phục vụ cho việc học tập và cải tiến liên tục.

---

## [2026-05-21 18:35] - Lỗi Static Code Analysis (PHPCS) trên GitHub Actions Pipeline

- **Type**: Process & Test Failure (PHPCS Violations)
- **Severity**: High
- **File**: `.github/workflows/ci.yml` (bước tạo file `wp-content/themes/my-theme/functions.php`)
- **Agent**: Sunless
- **Root Cause**: Tệp tin `functions.php` mẫu được tạo tự động trong pipeline vi phạm nghiêm trọng các quy tắc định dạng code của chuẩn PSR2 (thiếu dấu ngoặc xuống dòng cho hàm, có khoảng trắng dư thừa trong dấu ngoặc đơn của các lệnh gọi hàm, và chứa cả định nghĩa hàm lẫn side effect thực thi trực tiếp trong cùng một file). Do ta đã loại bỏ cờ `|| true`, pipeline bị dừng ngay lập tức (Exit code 3).
- **Error Message**:
  ```text
  FILE: wp-content/themes/my-theme/functions.php
  FOUND 7 ERRORS AND 1 WARNING AFFECTING 5 LINES
  8 | ERROR   | [x] Opening brace should be on a new line
  9 | ERROR   | [x] Space after opening parenthesis of function call prohibited
  9 | ERROR   | [x] Expected 0 spaces before closing parenthesis; 1 found
  ...
  Error: Process completed with exit code 3.
  ```
- **Fix Applied**: Thay thế mã nguồn của tệp `functions.php` mẫu bằng một định nghĩa Class OOP hoàn chỉnh (`ThemeSetup`) tuân thủ tuyệt đối 100% chuẩn PSR2, loại bỏ hoàn toàn các lỗi định dạng và warning side-effect.
- **Prevention**: Luôn kiểm tra định dạng code cục bộ trước khi push lên Git và không sử dụng side-effect trộn lẫn với khai báo ký hiệu trong các file mẫu.
- **Status**: Fixed

---

## [2026-05-21 18:38] - Lỗi Đăng nhập Docker Hub (Login to Docker Hub) trên các Repository Forked

- **Type**: Integration & Infrastructure Error (Missing Github Secrets)
- **Severity**: Medium (Ảnh hưởng trạng thái pipeline trên PR/Forked Repos)
- **File**: `.github/workflows/ci.yml` (job `build-and-push`, bước `Login to Docker Hub`)
- **Agent**: Sunless
- **Root Cause**: Trên repository được fork bởi các thành viên trong nhóm (ví dụ `hieuzk123/HH-wordpress-cicd`), các khóa bí mật `DOCKERHUB_USERNAME` và `DOCKERHUB_TOKEN` chưa được thiết lập trong Settings Repo, dẫn đến việc bước đăng nhập Docker Hub của pipeline thất bại với lỗi thiếu tham số đăng nhập.
- **Error Message**:
  ```text
  Run docker/login-action@v3
  Error: Username and password required
  ```
- **Fix Applied**: Cấu hình điều kiện chạy (`if: ${{ secrets.DOCKERHUB_USERNAME != '' }}`) cho bước đăng nhập. Đồng thời cấu hình tham số `push: false` và dynamic tags cho `build-push-action` nếu secrets trống. Điều này giúp pipeline vẫn chạy build để kiểm thử cú pháp Dockerfile (kiểm tra tích hợp) mà không bị lỗi/dừng đột ngột khi chạy trên các repo fork.
- **Prevention**: Sử dụng các điều kiện kiểm tra sự tồn tại của secrets trước khi chạy các hành động bên thứ ba yêu cầu xác thực trong GitHub Actions.
- **Status**: Fixed

---

## [2026-05-21 18:40] - Lỗi cú pháp GitHub Actions: Unrecognized named-value: 'secrets' trong biểu thức điều kiện

- **Type**: Process & Test Failure (Workflow Syntax Error)
- **Severity**: High (Làm lỗi file workflow và dừng toàn bộ quá trình CI)
- **File**: `.github/workflows/ci.yml` (dòng 90, cột 13)
- **Agent**: Sunless
- **Root Cause**: GitHub Actions cấm truy cập trực tiếp vào đối tượng `secrets` trong biểu thức điều kiện `if` ở cấp độ step/job để bảo mật, dẫn đến lỗi cú pháp phân tích cú pháp workflow (`Invalid workflow file`).
- **Error Message**:
  ```text
  Invalid workflow file
  Unrecognized named-value: 'secrets'. Located at position 1 within expression: secrets.DOCKERHUB_USERNAME != ''
  ```
- **Fix Applied**: Truyền các khoá bí mật `secrets.DOCKERHUB_USERNAME` và `secrets.DOCKERHUB_TOKEN` vào biến môi trường (`env`) của step, sau đó kiểm tra thông tin thông qua đối tượng `env.DOCKERHUB_USERNAME` và `env.DOCKERHUB_TOKEN` vốn được GitHub Actions cho phép trong các biểu thức điều kiện.
- **Prevention**: Luôn sử dụng môi trường `env` làm trung gian khi cần kiểm tra hoặc thao tác trên secrets trong các biểu thức logic `if` của workflow.
- **Status**: Fixed

---
