#!/bin/sh
set -e

# Dump the current envvars to a file for better performance
if [ ! -f .env.local.php ]; then
  composer symfony:dump-env prod
fi

# Start PHP-FPM
exec php-fpm
