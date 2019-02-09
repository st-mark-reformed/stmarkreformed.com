@echo off

if "%1" == "" (
    set ENV=staging
) else (
    set ENV=%1
)

docker-compose up -d
docker exec -it --user root db-stmarkreformed bash -c "chmod +x /app/scripts/syncToLocal.sh;./app/scripts/syncToLocal.sh %ENV%"
docker exec -it --user root php-stmarkreformed bash -c "chmod +x /app/scripts/runImageTransforms.sh;./app/scripts/runImageTransforms.sh"
