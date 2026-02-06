@php
    use MetaFramework\Dictionnaries\Enum\DictionnaryType;
    use MetaFramework\Dictionnaries\Models\Dictionnary;
    use MetaFramework\Dictionnaries\Models\DictionnaryEntry;
    use MetaFramework\Dictionnaries\Support\RouteNaming;

    $isEntry = $data instanceof DictionnaryEntry;
    $isDictionnary = $data instanceof Dictionnary;
    $isMultilang = \MetaFramework\Accessors\Locale::multilang();
    $defaultLocale = config('app.fallback_locale', app()->getLocale());
    $layoutComponent = config('mfw-dictionnaries.layout_component', 'mfw-dictionnaries::layout');
    $route_create =
        $isEntry && isset($dictionnary)
            ? route(RouteNaming::name('dictionnary.entries.create'), ['dictionnary' => $dictionnary->id])
            : route(RouteNaming::name('dictionnary.create'));

    $locales = $isMultilang
        ? config('mfw.translatable.locales', config('translatable.locales', [$defaultLocale]))
        : [$defaultLocale];
    $custom_translatables =
        $isEntry && $subclass && method_exists($subclass, 'translatables') ? $subclass->translatables() : [];
    $custom_fillables = $isEntry && $subclass && method_exists($subclass, 'customData') ? $subclass->customData() : [];
@endphp

<x-dynamic-component :component="$layoutComponent">
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2>{{ trans_choice('mfw-dictionnaries::mfw-dictionnaries.dictionnary.label', 2) }}</h2>
            <div class="d-flex gap-2">
                <a class="btn btn-sm btn-secondary"
                    href="{{ $route_index }}">{{ __('mfw-dictionnaries::mfw-dictionnaries.buttons.back') }}</a>
                @if ($data->id)
                    <a class="btn btn-sm btn-primary"
                        href="{{ $route_create }}">{{ __('mfw-dictionnaries::mfw-dictionnaries.buttons.new') }}</a>
                @endif
            </div>
        </div>
    </x-slot>

    <x-mfw-support::validation-errors />

    <div class="bg-body-tertiary rounded p-4 shadow">
        <h2 class="legend">{!! $label ?? '' !!}</h2>
        <form method="post" action="{{ $route }}" id="mfw-dictionnaries-form">
            @csrf
            @if ($data->id)
                @method('put')
            @endif

            @if (isset($subentry))
                <input type="hidden" name="subentry" value="{{ $subentry->id }}" />
            @endif

            @if ($isMultilang)
                <ul id="tab_translatable_tabs" class="nav nav-tabs admintabs" role="tablist">
                    @foreach ($locales as $locale)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {!! $locale == app()->getLocale() ? 'active' : '' !!}"
                                id="tab_translatable_btn_{{ $locale }}" data-bs-toggle="tab"
                                data-bs-target="#tab_translatable_{{ $locale }}" type="button" role="tab"
                                aria-controls="tab_translatable_{{ $locale }}" aria-selected="true">
                                <img src="{!! asset('vendor/flags/4x3/' . $locale . '.svg') !!}" alt="{{ trans('mfw-lang.' . $locale . '.label') }}"
                                    class="d-inline-block" />
                                {!! trans('mfw-lang.' . $locale . '.label') !!}
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="row my-4">
                <div class="col-xxl-8">
                    <div class="tab-content base">
                        @foreach ($locales as $locale)
                            <div class="tab-pane fade {!! !$isMultilang || $locale == app()->getLocale() ? 'show active' : '' !!}"
                                id="tab_translatable_{{ $locale }}" role="tabpanel"
                                aria-labelledby="tab_link_content_{{ $locale }}">
                                <fieldset>
                                    <div class="row mb-4">
                                        @foreach ($data->fillables as $key => $value)
                                            @php
                                                $fieldName = $isMultilang ? "{$key}[{$locale}]" : $key;
                                                $fieldValue = $data->translation(
                                                    $key,
                                                    $isMultilang ? $locale : $defaultLocale,
                                                );
                                            @endphp
                                            @switch($value['type'])
                                                @case('textarea')
                                                @case('textarea_extended')
                                                    <div class="col-12 mb-4">
                                                        <x-mfw::textarea name="{{ $fieldName }}" :className="$value['type'] . ' ' . ($value['class'] ?? '')"
                                                            value="{!! $fieldValue !!}"
                                                            label="{{ __($value['label']) . (!empty($value['required']) ? ' *' : '') }}" />
                                                    </div>
                                                @break

                                                @default
                                                    <div class="{{ $value['class'] ?? 'col-12' }} mb-4">
                                                        <x-mfw-inputable::input name="{{ $fieldName }}"
                                                            value="{!! $fieldValue !!}"
                                                            label="{{ __($value['label']) . (!empty($value['required']) ? ' *' : '') }}" />
                                                    </div>
                                            @endswitch
                                        @endforeach

                                        @if ($custom_translatables)
                                            <x-mfw::custom-translatables :values="$custom_translatables" :model="$data"
                                                :locale="$locale" />
                                        @endif
                                    </div>
                                </fieldset>
                            </div>
                        @endforeach
                    </div>

                    @if ($custom_fillables)
                        <x-mfw::custom-fillables :values="$custom_fillables" :model="$data" />
                    @endif

                    @if ($isDictionnary)
                        <fieldset class="{{ !auth()->check() || !auth()->user()->hasRole('dev') ? 'd-none' : '' }}">
                            <legend>{{ __('mfw-dictionnaries::mfw-dictionnaries.labels.settings') }}</legend>
                            <div class="row">
                                <div class="col-6 col">
                                    <x-mfw-inputable::select name="type" :label="__('mfw-dictionnaries::mfw-dictionnaries.labels.type')" :values="DictionnaryType::translations()"
                                        :affected="$data->type ?: DictionnaryType::default()" :nullable="false" />
                                </div>
                                <div class="col-6">
                                    <x-mfw-inputable::input name="slug" :value="$data->slug" :label="__('mfw-dictionnaries::mfw-dictionnaries.labels.slug')" />
                                </div>
                            </div>
                        </fieldset>
                    @endif

                </div>
            </div>
        </form>
    </div>

    @include('mfw::lib.tinymce')
</x-dynamic-component>
