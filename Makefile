fresh-seed:
		composer dump-autoload
		php artisan migrate:fresh --seed

route-clear:
		php artisan route:clear
		php artisan config:cache
		php artisan cache:clear
		php artisan config:clear
		php artisan route:cache

cache:
		php artisan optimize:clear
		php artisan icons:cache
		php artisan icon:cache
		php artisan filament:cache-components

