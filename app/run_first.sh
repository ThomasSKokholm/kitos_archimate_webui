#!/bin/bash
#RUN
mkdir -p /var/www/html/uploads
mkdir -p /tmp/uploads
chown -R www-data:www-data /var/www/html/uploads/
chown -R www-data:www-data /tmp/uploads/
chown -R www-data:www-data /opt/kitos_tools/settings/settings.json