<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use MetaFramework\Accessors\Locale as LocaleAccessor;
use MetaFramework\Controllers\Controller;
use MetaFramework\Dictionnaries\Accessors\Dictionnaries;
use MetaFramework\Dictionnaries\DataTables\DictionnaryDataTable;
use MetaFramework\Dictionnaries\Enum\DictionnaryType;
use MetaFramework\Dictionnaries\Models\Dictionnary;
use MetaFramework\Dictionnaries\Support\RouteNaming;
use MetaFramework\Dictionnaries\Traits\DataTables\MassDelete;
use MetaFramework\Services\Validation\ValidationTrait;
use MetaFramework\Traits\Locale;
use Throwable;

class DictionnaryController extends Controller
{
    use Locale;
    use MassDelete;
    use ValidationTrait;

    public function index(DictionnaryDataTable $dataTable): JsonResponse|View
    {
        return $dataTable->render('mfw-dictionnaries::index');
    }

    public function create(): Renderable|RedirectResponse
    {
        return view('mfw-dictionnaries::create')->with([
            'data' => new Dictionnary,
            'route_index' => $this->routeIndex(),
            'label' => __('mfw-dictionnaries::mfw-dictionnaries.labels.dictionary_create'),
            'route' => route(RouteNaming::name('dictionnary.store')),
        ]);
    }

    public function store(): RedirectResponse
    {
        $this->basicValidation();

        try {
            $dictionnary = new Dictionnary;
            $this->fillFromRequest($dictionnary);
            $this->responseSuccess(__('mfw.record_created'));
            $this->redirect_to = route(RouteNaming::name('dictionnary.edit'), $dictionnary);
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    public function update(Dictionnary $dictionnary): RedirectResponse
    {
        $this->basicValidation();

        try {
            $previousSlug = $dictionnary->slug;
            $this->fillFromRequest($dictionnary);
            if ($previousSlug && $previousSlug !== $dictionnary->slug) {
                Dictionnaries::reset($previousSlug);
            }
            Dictionnaries::reset($dictionnary->slug);
            $this->responseSuccess(__('mfw.record_updated'));
            $this->redirect_to = route(RouteNaming::name('dictionnary.edit'), $dictionnary);
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    public function edit(Dictionnary $dictionnary): Renderable
    {
        return view('mfw-dictionnaries::create')->with([
            'data' => $dictionnary,
            'label' => "<span class='text-secondary'>" . __('mfw-dictionnaries::mfw-dictionnaries.labels.dictionary_edit') . '</span>',
            'route' => route(RouteNaming::name('dictionnary.update'), $dictionnary->id),
            'route_index' => route(RouteNaming::name('dictionnary.index')),
        ]);
    }

    public function destroy(Dictionnary $dictionnary): RedirectResponse
    {
        if (!$this->isDev()) {
            $this->responseWarning(__('mfw-dictionnaries::mfw-dictionnaries.messages.cannot_delete_dictionary'));

            return $this->sendResponse();
        }

        try {
            Dictionnaries::reset($dictionnary->slug);
            $dictionnary->delete();

            $this->redirectRoute(RouteNaming::name('dictionnary.index'))
                ->responseSuccess(__('mfw.record_deleted'));
        } catch (Throwable $e) {
            $this->responseException(
                $e,
                __('mfw-dictionnaries::mfw-dictionnaries.messages.dictionary_delete_blocked')
            );
        }

        return $this->sendResponse();
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

    private function fillFromRequest(Dictionnary $dictionnary): void
    {
        $dictionnary->type = (string) request('type', $dictionnary->type ?: DictionnaryType::default());

        $slugSource = trim((string) request('slug', ''));
        if ($slugSource === '') {
            $slugSource = $this->isMultilang()
                ? (string) data_get(request()->input('name', []), $this->defaultLocale(), '')
                : (string) request('name', '');
        }
        $dictionnary->slug = Str::snake($slugSource);

        foreach ($dictionnary->translatable as $translatable) {
            $value = request($translatable);
            if ($this->isMultilang() && is_array($value)) {
                $dictionnary->setTranslations($translatable, $value);

                continue;
            }
            if ($this->isMultilang() && !is_array($value) && $value !== null) {
                $dictionnary->setTranslation($translatable, $this->defaultLocale(), (string) $value);

                continue;
            }

            if (!$this->isMultilang()) {
                if (is_array($value)) {
                    $value = (string) data_get($value, $this->defaultLocale(), '');
                }
                $dictionnary->{$translatable} = (string) $value;
            }
        }

        $dictionnary->save();
    }

    private function isMultilang(): bool
    {
        return LocaleAccessor::multilang();
    }

    private function isDev(): bool
    {
        return auth()->check() && (bool) auth()->user()?->hasRole('dev');
    }

    private function routeIndex(): string
    {
        return route(RouteNaming::name('dictionnary.index'));
    }
}
