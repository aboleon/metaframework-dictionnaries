<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use MetaFramework\Accessors\Locale as LocaleAccessor;
use MetaFramework\Dictionnaries\DataTables\View\DictionaryView;
use MetaFramework\Dictionnaries\Enum\DictionnaryType;
use MetaFramework\Dictionnaries\Support\RouteNaming;
use MetaFramework\Dictionnaries\Traits\DataTables\Common;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DictionnaryDataTable extends DataTable
{
    use Common;

    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check"><input type="checkbox" class="form-check-input row-checkbox" value="' . $row->id . '"></div>';
            })
            ->addColumn('entries', function ($data) {
                return '<span class="btn btn-xs px-2 btn-' . ($data->entries_count ? 'info' : 'secondary opacity-50') . ' cursor-default">' . $data->entries_count . '</span>
                        <a class="btn btn-xs btn-default action-entries-index" title="' . e(__('mfw-dictionnaries::mfw-dictionnaries.actions.manage')) . '" href="' . route(RouteNaming::name('dictionnary.entries.index'), $data->id) . '">
                    ' . e(__('mfw-dictionnaries::mfw-dictionnaries.actions.manage')) . '
                        </a>';
            })
            ->addColumn('type', function ($data) {
                return DictionnaryType::translated($data->type);
            })
            ->addColumn('action', function ($data) {
                return view('mfw-dictionnaries::datatable.action')->with([
                    'item' => $data,
                ])->render();
            })
            ->rawColumns(['entries', 'action', 'checkbox']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(DictionaryView $model): QueryBuilder
    {
        $query = $model->newQuery();
        $driver = $query->getConnection()->getDriverName();
        $fallbackPath = $this->jsonLocalePath((string) config('app.fallback_locale', 'en'));

        if (!LocaleAccessor::multilang()) {
            if ($driver === 'sqlite') {
                return $query->selectRaw(
                    'id, slug, type, entries_count,
                    CASE
                        WHEN json_valid(name) THEN COALESCE(json_extract(name, ?), name)
                        ELSE name
                    END AS name',
                    [$fallbackPath]
                );
            }

            return $query->selectRaw(
                'id, slug, type, entries_count,
                CASE
                    WHEN JSON_VALID(name) THEN COALESCE(JSON_UNQUOTE(JSON_EXTRACT(name, ?)), name)
                    ELSE name
                END AS name',
                [$fallbackPath]
            );
        }

        $localePath = $this->jsonLocalePath((string) app()->getLocale());

        if ($driver === 'sqlite') {
            return $query->selectRaw(
                'id, slug, type, entries_count,
                CASE
                    WHEN json_valid(name) THEN COALESCE(
                        json_extract(name, ?),
                        json_extract(name, ?),
                        name
                    )
                    ELSE name
                END AS name',
                [$localePath, $fallbackPath]
            );
        }

        return $query->selectRaw(
            'id, slug, type, entries_count,
            CASE
                WHEN JSON_VALID(name) THEN COALESCE(
                    JSON_UNQUOTE(JSON_EXTRACT(name, ?)),
                    JSON_UNQUOTE(JSON_EXTRACT(name, ?)),
                    name
                )
                ELSE name
            END AS name',
            [$localePath, $fallbackPath]
        );
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('dictionnary', [
            'orderBys' => [
                1 => 'asc',
            ],
            'params' => [
                'pageLength' => -1,
                'lengthMenu' => [[10, 25, 50, -1], [10, 25, 50, __('mfw-dictionnaries::mfw-dictionnaries.datatable.all')]],
            ],
        ]);
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        $columns = [];

        if (auth()->check() && auth()->user()->hasRole('dev')) {
            $columns[] = Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->orderable(false)->searchable(false)->width('50');
        }

        $columns[] = Column::make('name')->title(__('mfw-dictionnaries::mfw-dictionnaries.table.headers.name'));
        $columns[] = Column::computed('entries')->title(__('mfw-dictionnaries::mfw-dictionnaries.table.headers.entries'));

        if (auth()->check() && auth()->user()->hasRole('dev')) {
            $columns[] = Column::computed('type')->title(__('mfw-dictionnaries::mfw-dictionnaries.table.headers.type'))->addClass('type');
            $columns[] = Column::make('slug')->title(__('mfw-dictionnaries::mfw-dictionnaries.table.headers.slug'))->addClass('slug');
        }
        $columns[] = Column::computed('action')->addClass('text-end')->title(__('mfw-dictionnaries::mfw-dictionnaries.table.headers.actions'));

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return __('mfw-dictionnaries::mfw-dictionnaries.export.filename_prefix') . date('YmdHis');
    }

    private function jsonLocalePath(string $locale): string
    {
        $safeLocale = preg_replace('/[^a-zA-Z0-9_-]/', '', $locale) ?: 'en';

        return '$.' . $safeLocale;
    }
}
