#!/bin/bash

MAGENTO_ROOT=/magento2
PROJECT_PATH=$GITHUB_WORKSPACE
REPOSITORY_URL="https://repo.magento.com/"
INPUT_PHPUNIT_FILE=/tools/phpunit/phpunit.xml

test -z "${MAGENTO_VERSION}" && MAGENTO_VERSION=$INPUT_MAGENTO_VERSION
test -z "${PROJECT_NAME}" && (echo "'project_name' is not set" && exit 1)
test -z "${MAGENTO_VERSION}" && (echo "'magento_version' is not set" && exit 1)
test -z "${MAGENTO_MARKETPLACE_USERNAME}" && (echo "'MAGENTO_MARKETPLACE_USERNAME' is not set" && exit 1)
test -z "${MAGENTO_MARKETPLACE_PASSWORD}" && (echo "'MAGENTO_MARKETPLACE_PASSWORD' is not set" && exit 1)

echo "Setup Magento credentials"
composer global config http-basic.repo.magento.com $MAGENTO_MARKETPLACE_USERNAME $MAGENTO_MARKETPLACE_PASSWORD

echo "Prepare composer installation"
composer create-project --repository=https://repo.magento.com/ magento/project-community-edition:${MAGENTO_VERSION} $MAGENTO_ROOT --no-install --no-interaction --no-progress

cd $MAGENTO_ROOT

echo "Run installation"
COMPOSER_MEMORY_LIMIT=-1 composer install --prefer-dist --no-interaction --no-progress --no-suggest

mv /tools $MAGENTO_ROOT/tools

echo "Determine which phpunit.xml file to use"
if [[ -z "$INPUT_PHPUNIT_FILE" || ! -f "$INPUT_PHPUNIT_FILE" ]] ; then
    INPUT_PHPUNIT_FILE=$MAGENTO_ROOT/tools/phpunit/phpunit.xml
fi

mkdir -p $MAGENTO_ROOT/app/code/Accord
mv /temp/* $MAGENTO_ROOT/app/code/Accord

echo "/tools/phpunit/phpunit.xml"
cat $INPUT_PHPUNIT_FILE

echo "Prepare for unit tests"
sed "s#%PROJECT_NAME%#$PROJECT_NAME#g" $INPUT_PHPUNIT_FILE > $INPUT_PHPUNIT_FILE

echo "ROOT"
ls -la /
cat $INPUT_PHPUNIT_FILE

#echo "Run the unit tests"
#cd $MAGENTO_ROOT/tools/phpunit && ../../vendor/bin/phpunit -c phpunit.xml