# Di chuyển tới thư mục chứa tệp script
cd $PSScriptRoot

# Khởi chạy môi trường Staging và các dịch vụ Giám sát (Monitoring)
docker compose up -d

# Khởi chạy môi trường Production với tên dự án riêng biệt để tránh xung đột
docker compose -f docker-compose.prod.yml -p wordpress-cicd-prod up -d