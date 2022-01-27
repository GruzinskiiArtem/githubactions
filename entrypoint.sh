#!/bin/bash

MAGENTO_ROOT=/tmp/m2
PROJECT_PATH=$GITHUB_WORKSPACE
REPOSITORY_URL="https://repo-magento-mirror.fooman.co.nz/"
INPUT_PHPUNIT_FILE=/tools/phpunit/phpunit.xml

echo "Setup Magento credentials"
composer global config http-basic.repo.magento.com $MAGENTO_MARKETPLACE_USERNAME $MAGENTO_MARKETPLACE_PASSWORD

echo "Prepare composer installation"
composer create-project --repository=$REPOSITORY_URL magento/project-community-edition:${MAGENTO_VERSION} $MAGENTO_ROOT --no-install --no-interaction --no-progress

echo "Run installation"
COMPOSER_MEMORY_LIMIT=-1 composer install --prefer-dist --no-interaction --no-progress --no-suggest

echo "Prepare for unit tests"
echo $MAGENTO_ROOT
cd $MAGENTO_ROOT
ls
sed $INPUT_PHPUNIT_FILE > dev/tests/unit/phpunit.xml

echo "Run the unit tests"
cd $MAGENTO_ROOT/dev/tests/unit && ../../../vendor/bin/phpunit -c phpunit.xml