**Kịch bản Demo CI/CD WordPress**

**Chuẩn bị trước khi lên (làm ở nhà)**

powershell

\# Bật Docker Desktop

\# Bật staging + monitoring

cd D:\\wordpress-cicd

docker compose up -d

docker compose -f docker-compose.prod.yml up -d

**\# Bật self-hosted runner (giữ cửa sổ này mở suốt) với quyền admin**

cd C:\\Windows\\System32\\actions-runner

.\\run.cmd

Mở sẵn các tab trình duyệt theo thứ tự:

- Tab 1: github.com/NguyenHoangdh/HH-wordpress-cicd/actions
- Tab 2: localhost:8082 (Staging WordPress) => cài đặt
- Tab 3: localhost:8081 (Production WordPress)
- Tab 4: localhost:9090 (Prometheus)
- Tab 5: localhost:3000 (Grafana)
- Tab 6: VS Code mở D:\\wordpress-cicd

**Phần 1 - Giới thiệu tổng quan (2 phút)**

**Thao tác:** Mở GitHub repo, chỉ vào cấu trúc file

**Nói:**

_"Đây là dự án CI/CD cho website WordPress. Toàn bộ pipeline chạy tự động mỗi khi chúng em push code lên GitHub - không cần thao tác thủ công nào."_

**Thao tác:** Chỉ vào các file trong repo - Dockerfile, docker-compose.yml, .github/workflows/ci.yml

**Nói:**

_"Hệ thống gồm 3 thành phần chính: Dockerfile để đóng gói WordPress thành container, Docker Compose để chạy các service, và file ci.yml định nghĩa toàn bộ pipeline tự động."_

**Phần 2 - Demo pipeline chạy live (5 phút)**

**Bước 2.1 - Trigger pipeline**

**Thao tác:** Mở VS Code, vào file tests/SampleTest.php, sửa comment bất kỳ:

php

// Demo trigger pipeline - 24/05/2026

**Thao tác:** Mở PowerShell, chạy:

powershell

cd D:\\wordpress-cicd

git add .

git commit -m "demo: trigger CI/CD pipeline"

git push

**Nói (trong lúc chạy):**

_"Em vừa push một thay đổi nhỏ lên GitHub. Ngay lập tức GitHub Actions sẽ tự động trigger pipeline."_

**Bước 2.2 - Quan sát pipeline chạy**

**Thao tác:** Chuyển sang Tab 1 (GitHub Actions), bấm F5 refresh

**Nói:**

_"Chúng ta thấy pipeline vừa xuất hiện, đang ở trạng thái đang chạy. Pipeline của chúng em có 5 job chạy tuần tự."_

**Thao tác:** Chờ và chỉ vào từng job khi nó chạy:

Unit Test → Static Code Analysis → Build Docker Image → E2E Test → Deploy Staging

**Nói khi Unit Test chạy:**

_"Job đầu tiên là Unit Test - PHPUnit tự động chạy 10 test case kiểm tra logic của code. Nếu có test nào fail, toàn bộ pipeline dừng lại, code không được đưa lên production."_

**Nói khi PHPCS chạy:**

_"Job thứ hai là Static Code Analysis - PHPCS kiểm tra coding standard PSR-2. Đây là lớp bảo vệ thứ hai, đảm bảo code sạch trước khi build."_

**Nói khi Build Docker chạy:**

_"Job thứ ba build Docker image và tự động push lên Docker Hub tại hoang2204/hh-wordpress. Mỗi commit tạo ra một image riêng với tag là commit SHA - có thể rollback bất kỳ lúc nào."_

**Nói khi E2E Test chạy:**

_"Job thứ tư là Automated E2E Test bằng Playwright. Nó tự động mở trình duyệt Chromium, vào trang WordPress, kiểm tra trang load được, trang login hiển thị đúng, và thử đăng nhập thật sự vào admin. Đây là automated test - không cần người thao tác."_

**Nói khi Deploy Staging chạy:**

_"Job cuối cùng là Deploy Staging - self-hosted runner trên máy chúng em tự động kéo image mới về và restart container WordPress staging."_

**Bước 2.3 - Khi pipeline xanh hết**

**Thao tác:** Bấm vào run vừa hoàn thành, chỉ vào sơ đồ 5 job đều xanh

**Nói:**

_"Pipeline hoàn thành thành công. Từ lúc push code đến lúc deploy xong mất khoảng 2-3 phút, toàn bộ tự động, không cần ai ngồi chờ."_

**Phần 3 - Minh chứng Automated Testing (3 phút)**

**3.1 Chứng minh Unit Test + Integration Test**

**Thao tác:** Bấm vào job **Unit Test**, cuộn xuống phần output

**Nói:**

_"Đây là kết quả Unit Test. PHPUnit chạy 10 test case - 4 unit test kiểm tra logic PHP như sanitize XSS, phép tính, đếm mảng - và 6 integration test kiểm tra các file cấu hình hệ thống tồn tại đúng."_

**Chỉ vào dòng output:**

Tests: 10, Assertions: 17, 1 skipped

**Nói:**

_"1 skipped là test HTTP đến staging server - được skip tự động khi chạy trên CI vì localhost:8082 không có trên server GitHub. Đây là behavior đúng - test vẫn chạy được ở local."_

**3.2 Chứng minh E2E Automated Test**

**Thao tác:** Bấm vào job **E2E Test**, cuộn xuống output

**Nói:**

_"Đây là kết quả Playwright E2E Test. 9 tests passed - Playwright tự động mở Chromium headless, vào localhost:8082, kiểm tra trang chủ load được, kiểm tra trang login có đủ các field, rồi tự động điền username password và verify đăng nhập thành công vào wp-admin."_

**Chỉ vào dòng:**

9 passed (15.6s)

**Nói:**

_"Đây là automated test thật sự - không có người ngồi click, trình duyệt tự thao tác và tự kiểm tra kết quả."_

**Phần 4 - Minh chứng Deploy tự động (3 phút)**

**4.1 Deploy Staging tự động**

**Thao tác:** Chỉ vào job **Deploy Staging** trong pipeline vừa chạy

**Nói:**

_"Staging được deploy tự động qua self-hosted runner - đây là một runner cài trên máy chúng em, GitHub Actions kết nối vào và tự chạy lệnh docker compose pull, restart container."_

**Thao tác:** Chuyển sang Tab 2 localhost:8082

**Nói:**

_"Đây là môi trường Staging đang chạy sau khi deploy. WordPress hoàn toàn hoạt động bình thường."_

**4.2 Deploy Production tự động - Watchtower**

**Thao tác:** Mở PowerShell, chạy:

powershell

docker ps --format "table {{.Names}}\\t{{.Status}}\\t{{.Ports}}"

**Nói:**

_"Môi trường Production chạy tại port 8081. Do giới hạn môi trường local, việc tự động cập nhật production được thực hiện qua Watchtower - một container chạy ngầm theo dõi Docker Hub. Trong môi trường thực tế với server thật, Watchtower sẽ tự động pull image mới và restart container mà không cần can thiệp thủ công."_

**Thao tác:** Chuyển sang localhost:8081

**Nói:**

_"Đây là môi trường Production đang chạy tại port 8081, tách biệt hoàn toàn với Staging ở port 8082."_

**4.3 Chứng minh bằng Docker**

**Thao tác:** Mở PowerShell, chạy:

powershell

docker ps --format "table {{.Names}}\\t{{.Status}}\\t{{.Ports}}"

**Nói:**

_"Chúng ta thấy toàn bộ container đang chạy: WordPress staging port 8082, WordPress production port 8081, MySQL database, Prometheus, Grafana, và Watchtower."_

**Phần 5 - Demo Monitoring (2 phút)**

**Thao tác:** Chuyển sang Tab 4 localhost:9090

**Nói:**

_"Đây là Prometheus - công cụ thu thập metrics hệ thống mỗi 15 giây."_

**Thao tác:** Gõ vào ô Expression: up → nhấn **Execute**

**Nói:**

_"Metric up cho biết service nào đang hoạt động. Giá trị 1 nghĩa là đang chạy bình thường. Chúng ta thấy tất cả service đều online."_

"Metric up cho thấy trạng thái các service. Prometheus đang theo dõi 2 target: bản thân Prometheus tại localhost:9090 đang có giá trị 1 - tức là đang hoạt động bình thường. WordPress tại wordpress:80 có giá trị 0 - đang không phản hồi metrics, vì WordPress chưa cài Node Exporter. Đây là minh chứng Prometheus đang thu thập và lưu trữ metrics real-time từ hệ thống."

**Thao tác:** Chuyển sang Tab 5 localhost:3000, đăng nhập admin/admin123

**Thao tác:** Vào **Dashboards → Node Exporter Full**

**Nói:**

_"Grafana kết nối với Prometheus để hiển thị dashboard trực quan. Chúng ta có thể thấy CPU, Memory, Network, Disk đang được theo dõi real-time. Trong môi trường thực tế, Grafana còn có thể gửi cảnh báo qua email hay Slack khi CPU vượt ngưỡng."_

Dashboard Node Exporter Full đã có rồi! Đang hiện "No data" vì chưa có Node Exporter thu thập metrics từ máy.

Khi demo thầy, cứ vào dashboard này và nói:

_"Đây là dashboard Node Exporter Full - hiển thị CPU, Memory, Network, Disk của hệ thống. Hiện tại đang No data vì Node Exporter chưa được cài trên máy local - trong môi trường server thật, các biểu đồ này sẽ hiển thị đầy đủ real-time. Grafana đã kết nối thành công với Prometheus làm data source."_

**Phần 6 - Kết luận (1 phút)**

**Nói:**

_"Tóm lại, hệ thống đã đạt được mục tiêu đề ra: pipeline CI/CD đầy đủ Build → Test → Deploy, có 3 loại test tự động - unit, integration và E2E, deploy tự động cả staging lẫn production, và monitoring real-time với Prometheus và Grafana. Toàn bộ chạy trên công cụ miễn phí - GitHub Actions, Docker Hub free tier, không tốn chi phí hạ tầng."_

**Câu hỏi thường gặp từ thầy**

| **Câu hỏi**                                  | **Trả lời**                                                                                                                                                       |
| -------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| _"E2E test khác unit test chỗ nào?"_         | Unit test kiểm tra từng hàm riêng lẻ trong code. E2E test mô phỏng người dùng thật - mở browser, click, điền form - kiểm tra toàn bộ luồng từ đầu đến cuối.       |
| _"Watchtower có đảm bảo không bị downtime?"_ | Watchtower restart container khoảng 1-2 giây. Trong dự án học tập này chấp nhận được. Production thật sẽ dùng rolling update của Kubernetes.                      |
| _"Nếu E2E test fail thì sao?"_               | Pipeline dừng lại ở job E2E, không chạy Deploy Staging - code lỗi không được deploy.                                                                              |
| _"Self-hosted runner là gì?"_                | Là một chương trình cài trên máy chúng em, kết nối với GitHub. Khi pipeline cần chạy lệnh Docker trên máy local, GitHub Actions gửi lệnh đến runner này thực thi. |
| _"Tại sao không dùng Jenkins?"_              | GitHub Actions tích hợp sẵn với GitHub, miễn phí, không cần server riêng - phù hợp với quy mô dự án nhỏ và nhóm học tập                                           |