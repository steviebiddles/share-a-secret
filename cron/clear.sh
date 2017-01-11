#!/bin/sh
export SYMFONY_ENV=prod

cd /var/www/secret.octaldynamics.com/web/current && php app/console app:secrets:clear
