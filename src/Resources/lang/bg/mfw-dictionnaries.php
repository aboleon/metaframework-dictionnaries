<?php

declare(strict_types=1);

return [
    'dictionnary' => [
        'label' => 'Речник|Речници',
        'entries' => 'запис|записи',
    ],

    'buttons' => [
        'add' => 'Добави',
        'back' => 'Назад',
        'new' => 'Нов',
        'create' => 'Създай',
        'index' => 'Индекс',
        'add_subentry' => '+ Подзапис',
    ],

    'actions' => [
        'manage' => 'Управление',
    ],

    'labels' => [
        'dictionary_create' => 'Добави речник',
        'dictionary_edit' => 'Редактирай речник',
        'entries_of_dictionary' => 'Записи на речника',
        'entries_of_dictionaries' => 'Записи на речниците',
        'entry_create' => 'Добави запис в речника',
        'entry_edit' => 'Редактирай запис на речника',
        'entry_add_to_category' => 'Добави запис към категорията',
        'of_dictionary' => 'от речника',
        'settings' => 'Настройки',
        'type' => 'Тип',
        'slug' => 'Slug',
        'title_attribute' => 'заглавие',
        'item' => 'Елементът',
    ],

    'table' => [
        'headers' => [
            'name' => 'Заглавие',
            'entry' => 'Запис',
            'dictionary' => 'Речник',
            'entries' => 'Записи',
            'sub_entries' => 'Подзаписи',
            'position' => 'Позиция',
            'type' => 'Тип',
            'slug' => 'Slug',
            'actions' => 'Действия',
        ],
        'empty' => 'Няма данни.',
    ],

    'messages' => [
        'cannot_delete_dictionary' => 'Нямате право да изтриете речник.',
        'dictionary_delete_blocked' => 'Този речник не може да бъде изтрит, защото свързани записи са обвързани с контакти.',
        'entry_delete_blocked' => 'Този запис не може да бъде изтрит, защото е свързан с контакти.',
        'dictionary_not_found' => '<span class="text-danger">Речник</span> :key <span class="text-danger">не е намерен</span>',
        'delete_question' => 'Изтриване на :name?',
        'mass_delete' => [
            'no_model' => 'Не е подаден модел.',
            'no_ids' => 'Не са подадени идентификатори за изтриване.',
            'invalid_model' => ':model не е валиден клас.',
            'not_eloquent' => ':model не е Eloquent модел.',
            'item_deleted' => ':name беше изтрит',
            'delete_attempt_failed' => 'Възникна грешка при опит за изтриване на :name. Редът не е изтрит.',
        ],
    ],

    'tooltips' => [
        'type' => 'Meta - речник с категории',
        'slug' => 'Не е задължително. Използва се за кеш и резолвиране на подкласа на записите.',
    ],

    'datatable' => [
        'all' => 'Всички',
        'language' => [
            'empty_table' => 'Няма налични данни в таблицата',
            'info' => 'Показани _START_ до _END_ от общо _TOTAL_ записа',
            'info_empty' => 'Показани 0 до 0 от 0 записа',
            'info_filtered' => '(филтрирани от общо _MAX_ записа)',
            'length_menu' => 'Покажи _MENU_ записа',
            'loading_records' => 'Зареждане...',
            'processing' => 'Обработка...',
            'search' => 'Търсене:',
            'zero_records' => 'Няма намерени съвпадения',
            'paginate' => [
                'first' => 'Първа',
                'last' => 'Последна',
                'next' => 'Следваща',
                'previous' => 'Предишна',
            ],
        ],
    ],

    'export' => [
        'filename_prefix' => 'Dictionnary_',
    ],

    'enum' => [
        'dictionnary_type' => [
            'simple' => 'Прост',
            'meta' => 'Мета',
            'custom' => 'Персонализиран',
        ],
    ],
];
