# Команды консоли

## Управление пакетами

Переходим в папку `bin`:

    cd vendor/php7lab/dev/bin

Незакомиченные изменения пакетов composer:

    php console package:git:changed

Пакеты, нуждающиеся в выпуске релиза:

    php console package:git:need-release

Стянуть все пакеты:

    php console package:git:pull

## Генератор

Переходим в папку `bin`:

    cd vendor/php7lab/dev/bin

Генерация домена (service, repository, entity, migration, interface):

    php console generator:domain

Генерация модуля (API, Web, Console):

    php console generator:module
