<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries\DataTables\View;

use Illuminate\Database\Eloquent\Model;

class DictionaryView extends Model
{
    protected $table = 'dictionaries_view';

    public $timestamps = false;

    protected $guarded = [];
}
