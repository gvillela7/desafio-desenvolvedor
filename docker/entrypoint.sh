#!/bin/bash
set -e
    echo "Starting Laravel..."
    cd /var/www/html/oltrust/
        php artisan config:cache
        php artisan migrate
        php artisan storage:link
        php artisan consumer-upload &
        php artisan serve --host 0.0.0.0 --port 8000
