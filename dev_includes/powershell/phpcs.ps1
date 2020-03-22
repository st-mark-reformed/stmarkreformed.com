$tasks.Add('phpcs',@{
    description="Runs phpcs";
    arguments = @()
    script = {
        # Run locally
        Invoke-Expression 'vendor/bin/phpcs --config-set installed_paths ../../doctrine/coding-standard/lib,../../slevomat/coding-standard; vendor/bin/phpcs dev public/index.php config; vendor/bin/php-cs-fixer fix --verbose --dry-run --using-cache=no'
    }
})

$tasks.Add('phpcbf',@{
    description="Runs phpcbf";
    arguments = @()
    script = {
        # Run locally
        Invoke-Expression 'vendor/bin/phpcbf --config-set installed_paths ../../doctrine/coding-standard/lib,../../slevomat/coding-standard; vendor/bin/phpcbf dev public/index.php config; vendor/bin/php-cs-fixer fix --verbose --using-cache=no'
    }
})
