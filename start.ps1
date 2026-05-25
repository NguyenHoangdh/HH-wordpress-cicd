cd D:\wordpress-cicd
docker compose up -d
docker compose -f docker-compose.prod.yml -p wordpress-cicd-prod up -d