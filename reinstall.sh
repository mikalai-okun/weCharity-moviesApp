#!/usr/bin/env bash

rm -rf app/cache/*
rm -rf app/logs/*

npm install

composer install
composer dump-autoload -o
bower install
gulp --production

php bin/console assets:install
php bin/console assetic:dump
php bin/console cache:clear --no-warmup
