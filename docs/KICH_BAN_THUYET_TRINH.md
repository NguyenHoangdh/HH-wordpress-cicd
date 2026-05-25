# KỊCH BẢN THUYẾT TRÌNH BÁO CÁO CUỐI KỲ DEVOPS
## ĐỀ TÀI: THIẾT KẾ VÀ TRIỂN KHAI HỆ THỐNG CI/CD CHO WEBSITE WORDPRESS (THỰC TẾ DOANH NGHIỆP)

---

## 🎙️ PHẦN 1: MỞ ĐẦU & ĐẶT VẤN ĐỀ (Thời gian dự kiến: 2 - 3 phút)

### **Người nói (MC / Đại diện Nhóm):**
> *"Kính chào thầy cô và các bạn trong Hội đồng chấm thi. Hôm nay, nhóm chúng em xin phép trình bày đề tài cuối kỳ môn Nhập môn DevOps: **'Thiết kế và triển khai hệ thống CI/CD cho website WordPress trong môi trường doanh nghiệp thực tế'**.*
> 
> *Như chúng ta đã biết, WordPress là CMS (Content Management System) phổ biến nhất hiện nay. Tuy nhiên, việc vận hành WordPress trong doanh nghiệp thường gặp 3 thách thức lớn:*
> 1. **Triển khai thủ công (Manual Deployment)**: Việc upload code qua FTP hoặc kéo thủ công trên server rất dễ xảy ra lỗi con người, không lưu lại lịch sử thay đổi và gây gián đoạn dịch vụ.
> 2. **Rủi ro lỗi hệ thống (White Screen of Death - WSOD)**: Thiếu quy trình kiểm thử tự động trước khi deploy dẫn đến việc code lỗi làm sập toàn bộ trang web.
> 3. **Mất kiểm soát giám sát (Blind Operation)**: Khi có lỗi phát sinh, quản trị viên gặp khó khăn trong việc tìm kiếm log lỗi nằm rải rác trong container và không giám sát được hiệu năng phần cứng theo thời gian thực.
> 
> *Để giải quyết triệt để vấn đề này, nhóm chúng em đã ứng dụng tư duy DevOps và xây dựng một hệ thống tích hợp tự động hóa hoàn toàn từ mã nguồn đến giám sát vận hành."*

---

## 🖥️ PHẦN 2: KIẾN TRÚC HỆ THỐNG & CÔNG NGHỆ SỬ DỤNG (Thời gian dự kiến: 3 phút)

### **Người nói (Kỹ sư DevOps / Hạ tầng):**
> *"Về kiến trúc hạ tầng và các công nghệ cốt lõi, nhóm chúng em đã áp dụng mô hình **Containerization (Đóng gói container)** và **Infrastructure as Code (Hạ tầng dạng mã nguồn)**:*
> 
> * **Docker & Docker Compose**: Đóng gói độc lập ứng dụng WordPress (Apache + PHP) và MySQL Database. Chúng em thiết lập hai môi trường hoàn toàn tách biệt:
>   * **Staging (Môi trường thử nghiệm)**: Chạy trên cổng `8082`, phục vụ việc test tính năng mới.
>   * **Production (Môi trường chạy thật)**: Chạy trên cổng `8081`, tích hợp công cụ tự động cập nhật CD.
> * **GitHub Actions (CI Engine)**: Hệ thống Tích hợp liên tục đóng vai trò là 'người gác cổng', tự động chạy các kịch bản kiểm thử khi có thay đổi mã nguồn.
> * **Watchtower (CD Agent)**: Cơ chế triển khai liên tục dạng **Pull-based CD** chạy trực tiếp trên server để giám sát và tự động hóa cập nhật.
> * **Prometheus & Grafana**: Hệ thống giám sát (Monitoring) tài nguyên phần cứng (CPU, RAM) và lưu lượng HTTP Requests.
> * **ELK Stack (Elasticsearch, Logstash, Kibana + Filebeat)**: Hệ thống quản lý nhật ký tập trung (Centralized Logging) thu thập toàn bộ log Apache và PHP từ container."*

---

## ⚙️ PHẦN 3: LUỒNG CI/CD PIPELINE CHI TIẾT (Thời gian dự kiến: 3 phút)

### **Người nói (Kỹ sư CI/CD):**
> *"Chúng em xin phép đi sâu vào quy trình hoạt động của **CI/CD Pipeline** thông qua GitHub Actions:*
> 
> * **Giai đoạn 1 - Kiểm thử tự động (Unit & IaC Testing)**: Khi lập trình viên push code hoặc tạo Pull Request, hệ thống sẽ chạy **PHPUnit** để kiểm thử logic code (như kiểm tra bộ lọc dữ liệu đầu vào chống XSS) và kiểm tra cấu hình tệp `docker-compose` nhằm phát hiện sớm lỗi kết nối cơ sở dữ liệu.
> * **Giai đoạn 2 - Phân tích mã nguồn tĩnh (Static Code Analysis)**: Sử dụng **PHPCS (PHP Code Sniffer)** để đối chiếu mã nguồn với chuẩn PSR2 của PHP, đảm bảo tính nhất quán của code trước khi đóng gói.
> * **Giai đoạn 3 - Đóng gói và Lưu trữ (Build & Push)**: Code vượt qua tất cả các bài test sẽ được tự động đóng gói thành một Docker Image với tag duy nhất (dựa trên SHA của Commit) và đẩy lên **Docker Hub (Container Registry)**.
> * **Giai đoạn 4 - Triển khai tự động (Continuous Deployment)**: Watchtower container chạy trên server kiểm tra Docker Hub định kỳ. Khi phát hiện Image mới, nó sẽ tự động kéo (pull) về, dừng container cũ và khởi động container mới một cách mượt mà mà không làm mất mát dữ liệu cơ sở dữ liệu nhờ cơ chế **Docker Volumes**."*

---

## 🔴 PHẦN 4: KỊCH BẢN DEMO THỰC TẾ (Thời gian dự kiến: 5 - 6 phút)

*(Đây là thứ tự demo trực quan nhất để thuyết phục hội đồng chấm thi)*

### **Bước 1: Trình diễn trạng thái hiện tại của hệ thống**
* **Nói**: *"Trước hết, em xin trình diễn trạng thái hệ thống đang chạy ổn định."*
* **Hành động**:
  1. Mở trang WordPress Staging (`http://localhost:8082`) và Production (`http://localhost:8081`).
  2. Mở Grafana Dashboard (`http://localhost:3000`) để thấy biểu đồ tài nguyên phần cứng đang được Prometheus thu thập.
  3. Mở Kibana (`http://localhost:5601`) để chứng minh logs của container đang được đẩy về Elasticsearch theo thời gian thực.

### **Bước 2: Tạo thay đổi trong code và đẩy lên GitHub (Kích hoạt CI)**
* **Nói**: *"Bây giờ, lập trình viên của nhóm sẽ thực hiện một thay đổi nhỏ trong mã nguồn (ví dụ: tối ưu hàm hoặc viết thêm một test case mới) và thực hiện push lên nhánh `feature/clo-upgrade`."*
* **Hành động**: 
  1. Mở code, thực hiện thay đổi nhỏ (như thay đổi một dòng text trong giao diện hoặc cập nhật test case).
  2. Thực hiện lệnh `git commit` và `git push`.

### **Bước 3: Trình diễn GitHub Actions tự động kiểm thử và đóng gói**
* **Nói**: *"Ngay lập tức, GitHub Actions phát hiện sự thay đổi và kích hoạt Pipeline CI. Em xin mời thầy cô xem màn hình tab Actions."*
* **Hành động**: 
  1. Mở tab **Actions** trên GitHub.
  2. Chỉ ra các job đang chạy: chạy PHPUnit test ➔ chạy PHPCS quét chuẩn PSR2 ➔ Build Docker Image thành công và đẩy lên Docker Hub.

### **Bước 4: Trình diễn Watchtower CD cập nhật tự động trên Production**
* **Nói**: *"Sau khi Docker Hub nhận được Image mới, Watchtower chạy trên máy chủ Production phát hiện bản cập nhật, tự động thực hiện tải về và cập nhật container."*
* **Hành động**:
  1. Mở terminal, chạy lệnh xem log của Watchtower: `docker logs hh-wordpress-production-watchtower-1`.
  2. Chỉ ra dòng log thông báo phát hiện image mới, đang pull và khởi động lại WordPress container.
  3. F5 (tải lại trang) trang Production (`http://localhost:8081`) để chứng minh giao diện mới đã được cập nhật tự động thành công (Zero-touch deployment).

### **Bước 5: Xem biểu đồ giám sát (Grafana) và tìm kiếm log lỗi (Kibana)**
* **Nói**: *"Trong suốt quá trình triển khai, mọi biến động về tài nguyên máy chủ và nhật ký hoạt động đều được ghi lại."*
* **Hành động**:
  1. Mở Grafana, chỉ ra sự biến động nhỏ của CPU/RAM khi container khởi động lại.
  2. Mở Kibana, tìm kiếm các log Apache access log hoặc PHP log phát sinh khi ta truy cập trang web để chứng minh hệ thống Centralized Logging hoạt động hiệu quả.

---

## 🏁 PHẦN 5: ĐÁNH GIÁ & KẾT LUẬN (Thời gian dự kiến: 2 phút)

### **Người nói (Đại diện Nhóm):**
> *"Qua quá trình thực hiện đề tài, nhóm chúng em đã đạt được các kết quả sau:*
> * **Về mặt vận hành**: Tự động hóa hoàn toàn quy trình đóng gói và triển khai (CD), loại bỏ rủi ro thao tác thủ công.
> * **Về mặt chất lượng**: Đảm bảo 100% code tích hợp đều vượt qua bài kiểm thử tự động, kiểm soát lỗi chặt chẽ.
> * **Về mặt giám sát**: Quản trị viên luôn có cái nhìn trực quan về sức khỏe hệ thống và log lỗi tập trung để sẵn sàng ứng cứu khi có sự cố.
> 
> * **Hạn chế hiện tại**: Cơ chế quét của Watchtower có độ trễ nhỏ (ví dụ 30 giây hoặc 5 phút tùy cấu hình).
> * **Hướng phát triển tiếp theo**: Tích hợp thêm công cụ quét bảo mật tĩnh (SAST) trong CI và chuyển dịch hạ tầng từ Docker Compose lên cụm **Kubernetes (K8s)** để tự động mở rộng (auto-scaling) hệ thống khi lượng tải tăng cao.
> 
> *Nhóm chúng em xin chân thành cảm ơn thầy cô đã lắng nghe. Chúng em rất mong nhận được câu hỏi góp ý từ Hội đồng chấm thi."*
