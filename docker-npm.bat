@echo off

docker exec -it --user root --workdir /app node-stmarkreformed bash -c "npm %*"
