$tasks.Add('sync',@{
    description="Syncs prod to local";
    arguments = @()
    script = {
        Invoke-Expression 'docker-compose -f docker-composer.sync.dev.to.local.yml -p stmarkssh up -d'

        Invoke-Expression 'docker exec stmark-ssh bash -c "chmod +x /opt/project/scripts/dev/ensure-ssh-keys-working.sh;"'

        Invoke-Expression 'docker exec stmark-ssh bash -c "chmod +x /opt/project/scripts/dev/sync-to-local-01-ssh.sh; /opt/project/scripts/dev/sync-to-local-01-ssh.sh;"'

        Invoke-Expression 'docker exec stmark-db bash -c "chmod +x /opt/project/scripts/dev/sync-to-local-02-db.sh; /opt/project/scripts/dev/sync-to-local-02-db.sh;"'

        Invoke-Expression 'docker exec stmark-ssh bash -c "chmod +x /opt/project/scripts/dev/sync-to-local-03-rsync.sh; /opt/project/scripts/dev/sync-to-local-03-rsync.sh;"'

        Invoke-Expression 'docker-compose -f docker-composer.sync.dev.to.local.yml -p stmarkssh down'
    }
})
