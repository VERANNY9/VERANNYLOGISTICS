#!/bin/sh
set -eu

PORT="${PORT:-8080}"

# Replace Apache's default port with Railway's runtime port.
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
