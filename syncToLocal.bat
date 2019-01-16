@echo off

if "%1" == "" (
    set ENV=staging
) else (
    set ENV=%1
)

docker exec -it --user root db-stmarkreformed bash -c "chmod +x /app/scripts/syncToLocal.sh;./app/scripts/syncToLocal.sh %ENV%"
