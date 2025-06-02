#!/bin/bash

# Uruchom cron
service cron start

# Uruchom supervisor
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf