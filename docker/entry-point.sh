#!/usr/bin/env bash
cat ./docker/ascii-art.txt

set -e # exit script if any command fails (non-zero value)

echo "alias ll='ls -l'" >> ~/.bashrc

echo Run composer;
composer config --no-plugins allow-plugins.cweagans/composer-patches true;
composer install;

cd web/;

#echo Clear database;
#../vendor/bin/drush sql-drop -y;
#
#echo Import database;
#../vendor/bin/drush sql-cli < ../backup/starter.sql;

echo Import configurations;
../vendor/bin/drush cim -y;

echo Clear cache;
../vendor/bin/drush cr;

echo Update database;
../vendor/bin/drush updb -y;

chmod ug+w sites/default

exec "$@"
