$tasks.Add('cli',@{
    description="Invokes application CLI";
    arguments = @()
    script = {
        Invoke-Expression 'docker exec -it --user root --workdir /opt/project stmark-php bash -c "php craft $commandArgs"'
    }
})
