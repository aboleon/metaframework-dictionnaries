@php
    use MetaFramework\Dictionnaries\Support\RouteNaming;

    $layoutComponent = config('mfw-dictionnaries.layout_component', 'mfw-dictionnaries::layout');
@endphp

<x-dynamic-component :component="$layoutComponent">
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2>{{ trans_choice('mfw-dictionnaries::mfw-dictionnaries.dictionnary.label', 2) }}</h2>
            @if (auth()->check() && auth()->user()->hasRole('dev'))
                <a class="btn btn-sm btn-primary" href="{{ route(RouteNaming::name('dictionnary.create')) }}">
                    {{ __('mfw-dictionnaries::mfw-dictionnaries.buttons.add') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="bg-body-tertiary rounded p-4 shadow">
        {!! $dataTable->table() !!}
    </div>

    @push('js')
        {{ $dataTable->scripts() }}
        <script>
            const dicoTypeTooltip = @json(__('mfw-dictionnaries::mfw-dictionnaries.tooltips.type'));
            const dicoSlugTooltip = @json(__('mfw-dictionnaries::mfw-dictionnaries.tooltips.slug'));

            setTimeout(function() {
                let dt = $('#dictionnary-table'),
                    th = dt.find('th.type, th.slug');
                if (!th.length) {
                    return;
                }

                th.attr('data-bs-toggle', 'tooltip')
                    .attr('data-bs-placement', 'left');
                dt.find('th.type').attr('data-bs-title', dicoTypeTooltip);
                dt.find('th.slug')
                    .attr('data-bs-html', 'true')
                    .attr('data-bs-title', dicoSlugTooltip);

                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            }, 500);

            function DTclickableRow() {
                setTimeout(function() {
                    $('.dt.dataTable tbody > tr > td:not(:nth-child(0)):not(:nth-child(1)):not(:last-of-type):not(.unclickable)')
                        .css('cursor', 'pointer')
                        .click(function() {
                            window.location.assign($(this).parent().find('a.action-entries-index').attr('href'));
                        });
                }, 1000);
            }

            DTclickableRow();
            $('.dt').on('draw.dt', DTclickableRow);
        </script>
    @endpush
</x-dynamic-component>
