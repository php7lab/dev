# Конфигурация менеджера соединений

## Обявление файла конфигурации в ENV

Откройте файл `.env`.

Добавьте переменную окружения `PACKAGE_GROUP_CONFIG`

```
PACKAGE_GROUP_CONFIG=common/data/package_group.php
```

Файл конфига выглядит так:

```php
$collection = [
    [
        'name' => 'rocket-php-lab',
        'provider_name' => 'gitlab',
        'authors' => [
            [
                'name' => 'Rocket Firm',
            ],
        ],
    ],
];

$baseCollection = require_once(__DIR__ . '/../../vendor/php7lab/dev/src/Package/Domain/Data/package_group.php');
return array_merge($baseCollection, $collection);
```
