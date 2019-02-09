@echo off

docker exec -it --user root --workdir /app php-stmarkreformed bash -c "php %*"
