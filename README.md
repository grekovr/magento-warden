# Setup

## Requirements

- Docker
- Warden
- Git

Warden должен быть установлен заранее.

## Clone repository

```bash
git clone \
  git@github.com:grekovr/magento-warden.git \
  ~/Projects/magento-review

cd ~/Projects/magento-review
```

## Up containers

```bash
cp .env.sample .env
warden env up
```

## Dependencies

```bash
warden shell -c "composer install"
```

Sample Data устанавливаются автоматически во время `setup:install`, так как они уже подключены к проекту через Composer.
Если Sample Data не подключены к проекту, перед `setup:install` нужно выполнить:

```bash
warden shell -c "bin/magento sampledata:deploy"
```

## Setup Install + Sample Data

В проекте есть также Demo_PickupDelivery (не относится к тестовому)

```bash
warden shell -c "bin/magento setup:install \
--base-url=https://app.magento-review.test/ \
--base-url-secure=https://app.magento-review.test/ \
--db-host=db \
--db-name=magento \
--db-user=magento \
--db-password=magento \
--admin-firstname=Admin \
--admin-lastname=User \
--admin-email=admin@magento-review.test \
--admin-user=admin \
--admin-password=Test123#Test \
--language=en_US \
--currency=UAH \
--timezone=Europe/Kyiv \
--use-rewrites=1 \
--backend-frontname=backend \
--search-engine=opensearch \
--opensearch-host=opensearch \
--opensearch-port=9200"
```

## Compile and clear cache

```bash
warden shell -c "bin/magento setup:di:compile"
warden shell -c "bin/magento cache:flush"
```

## Check Demo_ProductAttribute

```bash
warden shell -c "bin/magento module:status Demo_ProductAttribute"
```

## Product Attribute API

Подробное описание реализации и проверки:

[Product Attribute API README](app/code/Demo/ProductAttribute/README.md)
