#!/bin/sh
set -eu

PORT="${PORT:-8080}"

# Ensure Apache has exactly one MPM enabled, even if the base image changes.
find /etc/apache2/mods-enabled -maxdepth 1 -type l -name 'mpm_*.load' -delete
find /etc/apache2/mods-enabled -maxdepth 1 -type l -name 'mpm_*.conf' -delete
a2enmod mpm_prefork >/dev/null

# Replace Apache's default port with Railway's runtime port.
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

apache2ctl configtest
exec apache2-foreground
