<?php

declare(strict_types=1);

return [
    'dictionnary' => [
        'label' => 'Dictionary|Dictionaries',
        'entries' => 'entry|entries',
    ],

    'buttons' => [
        'add' => 'Add',
        'back' => 'Back',
        'new' => 'New',
        'create' => 'Create',
        'index' => 'Index',
        'add_subentry' => '+ Sub-entry',
    ],

    'actions' => [
        'manage' => 'Manage',
    ],

    'labels' => [
        'dictionary_create' => 'Add a dictionary',
        'dictionary_edit' => 'Edit a dictionary',
        'entries_of_dictionary' => 'Dictionary entries',
        'entries_of_dictionaries' => 'Dictionary entries',
        'entry_create' => 'Add an entry to dictionary',
        'entry_edit' => 'Edit a dictionary entry',
        'entry_add_to_category' => 'Add an entry to category',
        'of_dictionary' => 'of dictionary',
        'settings' => 'Settings',
        'type' => 'Type',
        'slug' => 'Slug',
        'title_attribute' => 'title',
        'item' => 'Item',
    ],

    'table' => [
        'headers' => [
            'name' => 'Title',
            'entry' => 'Entry',
            'dictionary' => 'Dictionary',
            'entries' => 'Entries',
            'sub_entries' => 'Sub-entries',
            'position' => 'Position',
            'type' => 'Type',
            'slug' => 'Slug',
            'actions' => 'Actions',
        ],
        'empty' => 'No data.',
    ],

    'messages' => [
        'cannot_delete_dictionary' => 'You cannot delete a dictionary.',
        'dictionary_delete_blocked' => 'This dictionary cannot be deleted because related entries are linked to contacts.',
        'entry_delete_blocked' => 'This entry cannot be deleted because it is linked to contacts.',
        'dictionary_not_found' => '<span class="text-danger">Dictionary</span> :key <span class="text-danger">not found</span>',
        'delete_question' => 'Delete :name?',
        'mass_delete' => [
            'no_model' => 'No model was provided.',
            'no_ids' => 'No identifier was provided for deletion.',
            'invalid_model' => ':model is not a valid class.',
            'not_eloquent' => ':model is not an Eloquent model.',
            'item_deleted' => ':name has been deleted',
            'delete_attempt_failed' => 'An error occurred while deleting :name. Row not deleted.',
        ],
    ],

    'tooltips' => [
        'type' => 'Meta - dictionary with categories',
        'slug' => 'Not required. Used for cache and entry subclass resolution.',
    ],

    'datatable' => [
        'all' => 'All',
        'language' => [
            'empty_table' => 'No data available in table',
            'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
            'info_empty' => 'Showing 0 to 0 of 0 entries',
            'info_filtered' => '(filtered from _MAX_ total entries)',
            'length_menu' => 'Show _MENU_ entries',
            'loading_records' => 'Loading...',
            'processing' => 'Processing...',
            'search' => 'Search:',
            'zero_records' => 'No matching records found',
            'paginate' => [
                'first' => 'First',
                'last' => 'Last',
                'next' => 'Next',
                'previous' => 'Previous',
            ],
        ],
    ],

    'export' => [
        'filename_prefix' => 'Dictionary_',
    ],

    'enum' => [
        'dictionnary_type' => [
            'simple' => 'Simple',
            'meta' => 'Meta',
            'custom' => 'Custom',
        ],
    ],
];
