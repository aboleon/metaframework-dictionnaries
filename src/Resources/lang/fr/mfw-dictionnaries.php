<?php

declare(strict_types=1);

return [
    'dictionnary' => [
        'label' => 'Dictionnaire|Dictionnaires',
        'entries' => 'entrée|entrées',
    ],

    'buttons' => [
        'add' => 'Ajouter',
        'back' => 'Retour',
        'new' => 'Nouveau',
        'create' => 'Créer',
        'index' => 'Index',
        'add_subentry' => '+ Sous-entrée',
    ],

    'actions' => [
        'manage' => 'Gérer',
    ],

    'labels' => [
        'dictionary_create' => 'Ajouter un dictionnaire',
        'dictionary_edit' => 'Éditer un dictionnaire',
        'entries_of_dictionary' => 'Entrées du dictionnaire',
        'entries_of_dictionaries' => 'Entrées des dictionnaires',
        'entry_create' => 'Ajouter une entrée au dictionnaire',
        'entry_edit' => 'Éditer une entrée du dictionnaire',
        'entry_add_to_category' => 'Ajouter une entrée à la catégorie',
        'of_dictionary' => 'du dictionnaire',
        'settings' => 'Paramètres',
        'type' => 'Type',
        'slug' => 'Slug',
        'title_attribute' => 'titre',
        'item' => "L'élément",
    ],

    'table' => [
        'headers' => [
            'name' => 'Intitulé',
            'entry' => 'Entrée',
            'dictionary' => 'Dictionnaire',
            'entries' => 'Entrées',
            'sub_entries' => 'Sous-entrées',
            'position' => 'Position',
            'type' => 'Type',
            'slug' => 'Slug',
            'actions' => 'Actions',
        ],
        'empty' => 'Aucune donnée.',
    ],

    'messages' => [
        'cannot_delete_dictionary' => 'Vous ne pouvez pas supprimer un dictionnaire.',
        'dictionary_delete_blocked' => 'Ce dictionnaire ne peut pas être supprimé car des entrées lui appartenant sont reliées à des contacts.',
        'entry_delete_blocked' => 'Cette entrée ne peut pas être supprimée car elle est reliée à des contacts.',
        'dictionary_not_found' => '<span class="text-danger">Dictionnaire</span> :key <span class="text-danger">introuvable</span>',
        'delete_question' => 'Supprimer :name ?',
        'mass_delete' => [
            'no_model' => "Aucun modèle n'a été fourni.",
            'no_ids' => "Aucun identifiant n'a été fourni pour suppression.",
            'invalid_model' => ':model n\'est pas une classe valide.',
            'not_eloquent' => ':model n\'est pas un modèle Eloquent.',
            'item_deleted' => ':name a été supprimé',
            'delete_attempt_failed' => 'Une erreur est survenue sur la tentative de suppression de :name. Ligne non supprimée.',
        ],
    ],

    'tooltips' => [
        'type' => 'Meta - dictionnaire à catégories',
        'slug' => 'Pas obligatoire. Utilisé pour le cache et la résolution des sous-classes d’entrées.',
    ],

    'datatable' => [
        'all' => 'Tous',
        'language' => [
            'empty_table' => 'Aucune donnée disponible dans le tableau',
            'info' => 'Affichage de _START_ à _END_ sur _TOTAL_ entrées',
            'info_empty' => 'Affichage de 0 à 0 sur 0 entrées',
            'info_filtered' => '(filtré à partir de _MAX_ entrées au total)',
            'length_menu' => 'Afficher _MENU_ entrées',
            'loading_records' => 'Chargement...',
            'processing' => 'Traitement...',
            'search' => 'Rechercher :',
            'zero_records' => 'Aucune entrée correspondante trouvée',
            'paginate' => [
                'first' => 'Premier',
                'last' => 'Dernier',
                'next' => 'Suivant',
                'previous' => 'Précédent',
            ],
        ],
    ],

    'export' => [
        'filename_prefix' => 'Dictionnaire_',
    ],

    'enum' => [
        'dictionnary_type' => [
            'simple' => 'Simple',
            'meta' => 'Meta',
            'custom' => 'Personnalisé',
        ],
    ],
];
