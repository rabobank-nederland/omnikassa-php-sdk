#!/bin/sh
set -e

# Symfony loads environment variables directly from .env and .env.local files
# No need to dump to PHP array format

# Start PHP-FPM
exec php-fpm
