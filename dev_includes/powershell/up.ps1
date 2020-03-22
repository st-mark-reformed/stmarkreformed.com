$tasks.Add('up',@{
    description="Starts docker containers";
    arguments = @()
    script = {
        Invoke-Expression "docker-compose $composeFiles -p stmark up -d"
        Invoke-Expression 'docker exec --user root stmark-php bash -c "chmod -R 0777 /opt/project/storage;"'
    }
})
