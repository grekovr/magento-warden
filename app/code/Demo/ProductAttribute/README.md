# Product Attribute API

## Task

Описание ТЗ:

Створити власний модуль та реалізувати REST API:
POST /V1/candidate/product-attribute

Параметри запиту:
* sku - обов'язковий;
* attribute_code - необов'язковий.

Приклад запиту:
```json
{
"sku": "24-MB01",
"attribute_code": "status"
}
```

Якщо attribute_code не передано - використовувати стандартний атрибут status.
Якщо attribute_code передано - використовувати вказаний атрибут.

API має повертати (приклад):
```json
{
"sku": "24-MB01",
"attribute_code": "status",
"value": "1"
}
```

3. Store View можна використовувати будь-який.

4. Передбачити обробку помилок:
* sku не передано або передано значення некоректного типу - стандартна помилка валідації M2;
* sku має порожнє значення - HTTP 422;
* товар із переданим SKU не існує - HTTP 404;
* передано неіснуючий attribute_code - HTTP 422.

5. Додати для API окремий ACL Resource.
   Налаштувати авторизацію та права доступу до API стандартними ACL-механізмами M2:
* якщо авторизований користувач має відповідний дозвіл - API доступне;
* якщо авторизований користувач не має відповідного дозволу - доступ заборонено стандартним ACL-механізмом M2.

## Implementation

### Интерфейсы сервис контрактов

```text
└── ProductAttribute
    ├── Api
    │   ├── Data
    │   │   └── ProductAttributeInterface.php
    │   └── ProductAttributeManagementInterface.php
```
ProductAttributeManagementInterface.php содержит контракт на реализацию публичного метода getProductAttribute 

входящие параметры: 
* sku - обязательный string 
* attribute_code - необязательный string

ProductAttributeInterface.php содержит контракт на реализацию данных, которые возвращает getProductAttribute
* sku - обязательный string
* attribute_code - обязательный string
* value - nullable string; значение может быть string или null
getters контракты

### Реализация контрактов

```text
└── ProductAttribute
    ├── Model
    │   ├── Data
    │   │   └── ProductAttribute.php
    │   └── ProductAttributeManagement.php
```

ProductAttribute.php расширяет Magento\Framework\Api\AbstractSimpleObject
getters реализованы как тонкие клиенты $this->_get
sku и attribute_code с приведением к string, value без приведения к string (может быть null)

ProductAttributeManagement.php реализует метод получения аттрибута
нужны внедрение зависимостей в конструкторе

* Magento\Catalog\Api\ProductRepositoryInterface для получения product
* Demo\ProductAttribute\Model\Data\ProductAttributeFactory генерируется Magento при setup:di:compile - фабрика для возврата результата 
* Magento\Eav\Model\Config для получения аттрибута, который передали в запрос;
* Magento\Eav\Model\Entity\Attribute\Set::addSetInfo() получает связь атрибута с Attribute Set из
  eav_entity_attribute и добавляет её в объект атрибута как attribute_set_info.
  При передаче конкретного attribute_set_id (а я передаю именно attribute_set_id продукта) проверяется только один набор.

Для того чтобы DataObjectProcessor при возврате ответа не удалял поле с null 
Сделал after plugin для DataObjectProcessor (при обработке данных на нашем ProductAttributeInterface и значении в поле value = null, возвращаю поле обратно)
тут \Demo\ProductAttribute\Plugin\Webapi\DataObjectProcessorPlugin::afterBuildOutputDataArray

При возврате value сервис отбрасывает массивы и объекты, а scalar приводит к строке, null возвращает как есть. Массивы и объекты возможны, если будут кастомные backend аттрибуты.

Еще нужен был Exception с отдельным кодом 422, потому в \Magento\Framework\Webapi\Exception этот код не прописан
отнаследовался от него в UnprocessableEntityException. Чтобы можно было вернуть кастомный 422

Валидаторы, нормализаторы и ресолверы входных и выходного value оформил отдельными приватными методами в сервисе

### XML-конфигурация

#### registration.php

Регистрирует модуль Magento:

```php
ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Demo_ProductAttribute',
    __DIR__
);
```

#### etc/module.xml

Объявляет модуль Demo_ProductAttribute в Magento.

#### etc/di.xml

Тут зависимости модуля, связи интерфейсов с реализациями

#### etc/webapi.xml

Тут объявляем REST endpoint и связываем с Demo\ProductAttribute\Api\ProductAttributeManagementInterface::getProductAttribute, плюс связываем с ACL ресурсом, который отвечает за вызов этого api.

#### etc/acl.xml

Тут как раз обявляем отдельный ACL ресурс для нашего api Demo_ProductAttribute::product_attribute. Он покажется в админке при настройке ролей.

#### etc/webapi_rest/di.xml

Тут подключаем plugin только для area REST API, он нам нужен только в rest. И в after только для нашего интерфейса и value=null, возвращаем поле.

## Error handling

- отсутствующий или неверный `sku` — стандартный 400;
- пустой `sku` — 422;
- неизвестный SKU — 404;
- неизвестный или неназначенный атрибут — 422;
- отсутствие ACL — стандартный 401.

### Authentication

Заменить token placeholders для воспроизведения токенами полученными через:

```bash
curl --location 'https://app.magento-review.test/rest/default/V1/tfa/provider/google/authenticate' \
--header 'Content-Type: application/json' \
--data '{
  "username": "Change me",
  "password": "Change me",
  "otp": "Change me"
}' -k -i
```

- `<valid-token>` — token пользователя с `Product Attribute API` ACL permission;
- `<restricted-token>` — token пользователя без этого доступа.

## Verification

HTC 1. Отримання стандартного атрибута
Умова:
Авторизований користувач має необхідний дозвіл. Існує товар із SKU "24-MB01". У запиті attribute_code не передано.
ОР:
HTTP 200.
У відповіді повертаються sku, attribute_code = "status" та значення атрибута status.

### Request
```bash
curl --location 'https://app.magento-review.test/rest/default/V1/candidate/product-attribute' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <valid-token>' \
--data '{                                                      
 "sku": "24-MB01"
}' -k -i
```

### HTTP Status
```text
HTTP/2 200 
```

### Response
```json
{"sku":"24-MB01","attribute_code":"status","value":"1"}
```

HTC 2. Отримання переданого атрибута
Умова:
Авторизований користувач має необхідний дозвіл. Передано SKU існуючого товару та існуючий attribute_code.
ОР:
HTTP 200.
У відповіді повертаються передані sku, attribute_code та відповідне значення атрибута.

### Request
```bash
curl --location 'https://app.magento-review.test/rest/default/V1/candidate/product-attribute' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <valid-token>' \
--data '{                                                      
 "sku": "24-MB01",
 "attribute_code": "price"
}' -k -i
```

### HTTP Status
```text
HTTP/2 200 
```

### Response
```json
{"sku":"24-MB01","attribute_code":"price","value":"34.000000"}
```

HTC 3. Товар не існує
Умова:
Авторизований користувач має необхідний дозвіл. Передано SKU неіснуючого товару.
ОР:
HTTP 404.

### Request
```bash
curl --location 'https://app.magento-review.test/rest/default/V1/candidate/product-attribute' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <valid-token>' \
--data '{                                                      
 "sku": "24-MB01-test",
 "attribute_code": "price"
}' -k -i
```

### HTTP Status
```text
HTTP/2 404  
```

### Response
```json
{"message":"The product that was requested doesn't exist. Verify the product and try again."}
```

HTC 4. Атрибут не існує або не входить до Attribute Set товару
Умова:
Авторизований користувач має необхідний дозвіл. Товар існує, але переданий attribute_code не існує або не входить до Attribute Set цього товару.
ОР:
HTTP 422.

### Request
```bash
curl --location 'https://app.magento-review.test/rest/default/V1/candidate/product-attribute' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <valid-token>' \
--data '{                                                      
 "sku": "24-MB01",
 "attribute_code": "gender"
}' -k -i
```

### HTTP Status
```text
HTTP/2 422  
```

### Response
```json
{"message":"Attribute \"gender\" does not exist."}
```

HTC 5. SKU не передано або передано значення некоректного типу
Умова: У тілі запиту відсутній sku або передано значення некоректного типу.
ОР: Запит відхиляється стандартним механізмом валідації M2.

### Request
```bash
curl --location 'https://app.magento-review.test/rest/default/V1/candidate/product-attribute' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <valid-token>' \
--data '{
}' -k -i
```

### HTTP Status
```text
HTTP/2 400 
```

### Response
```json
{"message":"\"%fieldName\" is required. Enter and try again.","parameters":{"fieldName":"sku"}}
```

### Request
```bash
curl --location 'https://app.magento-review.test/rest/default/V1/candidate/product-attribute' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <valid-token>' \
--data '{
 "sku": []
}' -k -i
```

### HTTP Status
```text
HTTP/2 400 
```

### Response
```json
{"message":"The \"array\" value's type is invalid. The \"string\" type was expected. Verify and try again."}
```

HTC 6. SKU має порожнє значення
Умова: Передано порожнє значення sku.
ОР: HTTP 422.

### Request
```bash
curl --location 'https://app.magento-review.test/rest/default/V1/candidate/product-attribute' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <valid-token>' \
--data '{
 "sku": ""
}' -k -i
```

### HTTP Status
```text
HTTP/2 422 
```

### Response
```json
{"message":"The \"sku\" field cannot be empty."}
```

HTC 7. Немає дозволу
Умова: Користувач авторизований, але не має необхідного ACL-дозволу для цього API.
ОР: Доступ заборонено стандартним ACL-механізмом M2.

### Request
```bash
curl --location 'https://app.magento-review.test/rest/default/V1/candidate/product-attribute' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <restricted-token>' \
--data '{                                                      
 "sku": "24-MB01"
}' -k -i
```

### HTTP Status
```text
HTTP/2 401 
```

### Response
```json
{"message":"The consumer isn't authorized to access %resources.","parameters":{"resources":"Demo_ProductAttribute::product_attribute"}}
```
