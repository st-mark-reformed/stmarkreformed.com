@echo off

docker kill node-stmarkreformed
docker-compose up -d
docker exec -it --user root --workdir /app node-stmarkreformed bash -c "npm run fab"
