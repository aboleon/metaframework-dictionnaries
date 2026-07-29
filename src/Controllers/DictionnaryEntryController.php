<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use MetaFramework\Accessors\Locale as LocaleAccessor;
use MetaFramework\Controllers\Controller;
use MetaFramework\Dictionnaries\Accessors\Dictionnaries;
use MetaFramework\Dictionnaries\Enum\DictionnaryType;
use MetaFramework\Dictionnaries\Models\Dictionnary;
use MetaFramework\Dictionnaries\Models\DictionnaryEntry;
use MetaFramework\Dictionnaries\Support\RouteNaming;
use MetaFramework\Services\Validation\ValidationTrait;
use MetaFramework\Traits\Locale;
use Throwable;

class DictionnaryEntryController extends Controller
{
    use Locale;
    use ValidationTrait;

    private string $view;

    public function __construct()
    {
        $this->view = 'mfw-dictionnaries::entries.index';
    }

    public function index(?Dictionnary $dictionnary = null): Renderable
    {
        $dictionnary ??= new Dictionnary;
        [$localizedOrderSql, $localizedOrderBindings] = $this->localizedNameOrderSql();

        $entries = $dictionnary->id
            ? $dictionnary->entries()->orderByRaw($localizedOrderSql . ' asc', $localizedOrderBindings)->paginate()
            : DictionnaryEntry::query()->with('dictionnary')->orderBy('id', 'desc')->paginate();

        if ($dictionnary->id && $dictionnary->type === DictionnaryType::META->value) {
            $dictionnary->setRelation(
                'entries',
                $dictionnary->entries()->with('entries')->orderByRaw($localizedOrderSql . ' asc', $localizedOrderBindings)->get()
            );
        }

        return view($this->view($dictionnary))->with([
            'entries' => $entries,
            'label' => $dictionnary->id
                ? '<span class="text-secondary">' . __('mfw-dictionnaries::mfw-dictionnaries.labels.entries_of_dictionary') . '</span> ' . $dictionnary->name
                : __('mfw-dictionnaries::mfw-dictionnaries.labels.entries_of_dictionaries'),
            'dictionnary' => $dictionnary,
        ]);
    }

    public function create(Dictionnary $dictionnary): Renderable
    {
        return view('mfw-dictionnaries::create')->with([
            'data' => new DictionnaryEntry,
            'subclass' => $dictionnary->entrySubClass(),
            'route_index' => $this->routeIndex($dictionnary),
            'dictionnary' => $dictionnary,
            'label' => '<span class="text-secondary">' . __('mfw-dictionnaries::mfw-dictionnaries.labels.entry_create') . '</span> ' . $dictionnary->name,
            'route' => route(RouteNaming::name('dictionnary.entries.store'), $dictionnary),
        ]);
    }

    public function edit(DictionnaryEntry $dictionnaryentry): Renderable
    {
        return view('mfw-dictionnaries::create')->with([
            'data' => $dictionnaryentry,
            'subclass' => $dictionnaryentry->dictionnary->entrySubClass(),
            'route_index' => $this->routeIndex($dictionnaryentry->dictionnary),
            'dictionnary' => $dictionnaryentry->dictionnary,
            'label' => '<span class="text-secondary">' . __('mfw-dictionnaries::mfw-dictionnaries.labels.entry_edit') . '</span> ' . $dictionnaryentry->dictionnary->name,
            'route' => route(RouteNaming::name('dictionnaryentry.update'), $dictionnaryentry),
        ]);
    }

    public function store(Dictionnary $dictionnary): RedirectResponse
    {
        $this->basicValidation();

        try {
            $entry = new DictionnaryEntry;
            $entry->dictionnary_id = $dictionnary->id;
            $entry->parent = request()->integer('subentry') ?: null;
            $entry->position = (int) request('position', 0);

            if (request()->has('custom')) {
                $entry->custom = (array) request('custom');
            }

            $this->fillTranslatablesFromRequest($entry, $dictionnary->entrySubClass());
            $entry->save();

            Dictionnaries::reset($dictionnary->slug);

            $this->responseSuccess(__('mfw::mfw.record_created'));
            $this->redirectTo(route(RouteNaming::name('dictionnary.entries.index'), $dictionnary));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    public function update(DictionnaryEntry $dictionnaryentry): RedirectResponse
    {
        $this->basicValidation();

        try {
            if (request()->has('custom')) {
                $dictionnaryentry->custom = (array) request('custom');
            }
            $dictionnaryentry->position = (int) request('position', $dictionnaryentry->position ?? 0);

            $this->fillTranslatablesFromRequest($dictionnaryentry, $dictionnaryentry->dictionnary->entrySubClass());
            $dictionnaryentry->save();

            Dictionnaries::reset($dictionnaryentry->dictionnary->slug);

            $this->responseSuccess(__('mfw::mfw.record_updated'));
            $this->redirectTo(route(RouteNaming::name('dictionnary.entries.index'), $dictionnaryentry->dictionnary));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    public function destroy(DictionnaryEntry $dictionnaryentry): RedirectResponse
    {
        try {
            $dictionnary = $dictionnaryentry->dictionnary;
            $dictionnaryentry->delete();

            Dictionnaries::reset($dictionnaryentry->dictionnary->slug);

            $this->redirectTo(route(RouteNaming::name('dictionnary.entries.index'), $dictionnary))
                ->responseSuccess(__('mfw::mfw.record_deleted'))
                ->whitout('object');
        } catch (Throwable $e) {
            $this->responseException($e, __('mfw-dictionnaries::mfw-dictionnaries.messages.entry_delete_blocked'));
        }

        return $this->sendResponse();
    }

    public function subentry(DictionnaryEntry $dictionnaryentry): Renderable
    {
        return view('mfw-dictionnaries::create')->with([
            'data' => new DictionnaryEntry,
            'subclass' => $dictionnaryentry->dictionnary->entrySubClass(),
            'route_index' => $this->routeIndex($dictionnaryentry->dictionnary),
            'dictionnary' => $dictionnaryentry->dictionnary,
            'label' => '<span class="text-secondary">' . __('mfw-dictionnaries::mfw-dictionnaries.labels.entry_add_to_category') . '</span> ' . $dictionnaryentry->name .
                '<span class="text-secondary"> ' . __('mfw-dictionnaries::mfw-dictionnaries.labels.of_dictionary') . ' </span> ' . $dictionnaryentry->dictionnary->name,
            'route' => route(RouteNaming::name('dictionnary.entries.store'), $dictionnaryentry->dictionnary),
            'subentry' => $dictionnaryentry,
        ]);
    }

    public function basicValidation(): void
    {
        if ($this->isMultilang()) {
            $this->validation_rules = [
                'name.' . $this->defaultLocale() => 'required|string',
            ];

            $this->validation_messages = [
                'name.' . $this->defaultLocale() . '.required' => __('validation.required', ['attribute' => __('mfw-dictionnaries::mfw-dictionnaries.labels.title_attribute')]),
            ];
        } else {
            $this->validation_rules = [
                'name' => 'required|string',
            ];

            $this->validation_messages = [
                'name.required' => __('validation.required', ['attribute' => __('mfw-dictionnaries::mfw-dictionnaries.labels.title_attribute')]),
            ];
        }

        $this->validation();
    }

    private function fillTranslatablesFromRequest(DictionnaryEntry $entry, object|bool $subclass = false): void
    {
        $keys = array_keys($entry->fillables);

        if ($subclass && method_exists($subclass, 'translatables')) {
            $translatables = (array) $subclass->translatables();
            $subclassKeys = array_is_list($translatables) ? $translatables : array_keys($translatables);
            $keys = array_unique([...$keys, ...$subclassKeys]);
        }

        foreach ($keys as $key) {
            $value = request($key);
            if ($this->isMultilang() && is_array($value)) {
                $entry->setTranslations((string) $key, $value);

                continue;
            }
            if ($this->isMultilang() && !is_array($value) && $value !== null) {
                $entry->setTranslation((string) $key, $this->defaultLocale(), (string) $value);

                continue;
            }

            if (!$this->isMultilang()) {
                if (is_array($value)) {
                    $value = (string) data_get($value, $this->defaultLocale(), '');
                }
                $entry->{$key} = (string) $value;
            }
        }
    }

    private function routeIndex(?Dictionnary $dictionnary = null): string
    {
        if ($dictionnary?->id) {
            return route(RouteNaming::name('dictionnary.entries.index'), $dictionnary);
        }

        return route(RouteNaming::name('dictionnaryentry.index'));
    }

    private function view(?Dictionnary $dictionnary): string
    {
        if ($dictionnary?->id) {
            return match ($dictionnary->type) {
                DictionnaryType::META->value => 'mfw-dictionnaries::entries.index_meta',
                default => $this->view,
            };
        }

        return $this->view;
    }

    /**
     * @return array{0: string, 1: array<int, string>}
     */
    private function localizedNameOrderSql(): array
    {
        if (!$this->isMultilang()) {
            return ['name', []];
        }

        $locale = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) app()->getLocale()) ?: 'en';

        return ['JSON_UNQUOTE(JSON_EXTRACT(name, ?))', ['$.' . $locale]];
    }

    private function isMultilang(): bool
    {
        return LocaleAccessor::multilang();
    }
}
