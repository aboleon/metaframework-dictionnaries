<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries\Traits\DataTables;

use Yajra\DataTables\Html\Builder as HtmlBuilder;

trait Common
{
    public function setHtml(string $table, array $config = []): HtmlBuilder
    {
        $minifiedAjaxUrl = $config['minifiedAjaxUrl'] ?? '';
        $params = $config['params'] ?? [];
        $orderBys = $config['orderBys'] ?? null;
        $orderBy = $config['orderBy'] ?? 1;
        $orderByDirection = $config['orderByDirection'] ?? 'desc';

        $defaultLanguage = [
            'emptyTable' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.empty_table'),
            'info' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.info'),
            'infoEmpty' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.info_empty'),
            'infoFiltered' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.info_filtered'),
            'lengthMenu' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.length_menu'),
            'loadingRecords' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.loading_records'),
            'processing' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.processing'),
            'search' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.search'),
            'zeroRecords' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.zero_records'),
            'paginate' => [
                'first' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.paginate.first'),
                'last' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.paginate.last'),
                'next' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.paginate.next'),
                'previous' => __('mfw-dictionnaries::mfw-dictionnaries.datatable.language.paginate.previous'),
            ],
        ];

        // Merge custom language settings
        $customLanguage = $config['language'] ?? [];
        $mergedLanguage = array_merge($defaultLanguage, $customLanguage);

        $res = $this->builder()
            ->setTableId($table . '-table')
            ->addTableClass('table table-hover dt')
            ->columns($this->getColumns())
            ->minifiedAjax($minifiedAjaxUrl);

        if ($orderBys) {
            foreach ($orderBys as $index => $direction) {
                $res->orderBy($index, $direction);
            }
        } else {
            $res->orderBy($orderBy, $orderByDirection);
        }

        return $res
            ->parameters(array_merge([
                'pageLength' => 25,
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, __('mfw-dictionnaries::mfw-dictionnaries.datatable.all')]],
                'language' => $mergedLanguage,
                'dom' => '<"row"<"col-sm-12 col-md-6"<"d-md-inline-block"l><"d-md-inline-block ms-md-4 mb-3"i>><"col-sm-12 col-md-6"f>>' .
                         '<"row"<"col-sm-12"tr>>' .
                         '<"row"<"col-sm-12 col-md-5"><"col-sm-12 col-md-7"<"mt-3"p>>>',
            ], $params))
            ->selectStyleSingle();
    }
}
