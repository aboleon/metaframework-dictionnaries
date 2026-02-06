@php
    use MetaFramework\Dictionnaries\Support\RouteNaming;

    $layoutComponent = config('mfw-dictionnaries.layout_component', 'mfw-dictionnaries::layout');
@endphp

<x-dynamic-component :component="$layoutComponent">
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center pe-5">
            <h2>{!! $label !!}</h2>
            <div class="d-flex gap-2">
                @if ($dictionnary->id)
                    <a class="btn btn-sm btn-primary"
                        href="{{ route(RouteNaming::name('dictionnary.entries.create'), ['dictionnary' => $dictionnary]) }}">
                        {{ __('mfw-dictionnaries::mfw-dictionnaries.buttons.create') }}
                    </a>
                    <a class="btn btn-sm btn-secondary" href="{{ route(RouteNaming::name('dictionnary.index')) }}">
                        {{ __('mfw-dictionnaries::mfw-dictionnaries.buttons.index') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    @if ($dictionnary->id)
        @php $counter = $entries->total(); @endphp
        <div class="counter d-flex justify-content-center align-items-center mb-3">
            <div class="border-dark-subtle me-3 rounded border px-3 py-1 text-center">
                <span style="font-size: 40px;color: var(--ab-blue-grey);"
                    class="d-block fw-bold">{{ $counter }}</span>
                <small class="d-block" style="margin-top: -10px">
                    {{ trans_choice('mfw-dictionnaries::mfw-dictionnaries.dictionnary.entries', $counter) }}
                </small>
            </div>
        </div>
    @endif

    <div class="mb-4 bg-white px-4 py-2 shadow-xl sm:rounded-lg" style="margin: 0 -12px">
        <table class="table">
            <tr>
                <th>{{ __('mfw-dictionnaries::mfw-dictionnaries.table.headers.entry') }}</th>
                @if (!$dictionnary->id)
                    <th>{{ __('mfw-dictionnaries::mfw-dictionnaries.table.headers.dictionary') }}</th>
                @endif
                <th>{{ __('mfw-dictionnaries::mfw-dictionnaries.table.headers.position') }}</th>
                <th style="width: 100px">{{ __('mfw-dictionnaries::mfw-dictionnaries.table.headers.actions') }}</th>
            </tr>
            <tbody>
                @forelse($entries as $item)
                    <tr>
                        <td>{{ $item->name }} {{ $item->deleted_at }}</td>
                        @if (!$dictionnary->id)
                            <td>
                                <a class="btn btn-xs btn-default" href="{!! route(RouteNaming::name('dictionnary.entries.index'), $item->dictionnary) !!}">
                                    {{ $item->dictionnary->name }}
                                </a>
                            </td>
                        @endif
                        <td>{{ $item->position }}</td>
                        <td>
                            <ul class="mfw-actions">
                                <x-mfw::edit-link :route="route(RouteNaming::name('dictionnaryentry.edit'), $item)" />
                                <x-mfw::delete-modal-link reference="{{ $item->id }}" />
                            </ul>
                            <x-mfw::modal :route="route(RouteNaming::name('dictionnaryentry.destroy'), $item)" :question="__('mfw-dictionnaries::mfw-dictionnaries.messages.delete_question', [
                                'name' => $item->name,
                            ])"
                                reference="destroy_{{ $item->id }}" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">{{ __('mfw-dictionnaries::mfw-dictionnaries.table.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <x-mfw::pagination :object="$entries" />
    </div>
</x-dynamic-component>
