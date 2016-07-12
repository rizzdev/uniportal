php vendor/bin/doctrine-module orm:clear-cache:metadata
php vendor/bin/doctrine-module orm:clear-cache:query
php vendor/bin/doctrine-module orm:clear-cache:result
php vendor/bin/doctrine-module orm:convert:mapping --namespace='Entity\' --force --from-database annotation /ssd/app/model
php  vendor/bin/doctrine-module orm:generate:entities  /ssd/app/model
php  vendor/bin/doctrine-module orm:generate:proxies
