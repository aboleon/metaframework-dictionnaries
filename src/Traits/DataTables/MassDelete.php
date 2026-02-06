<?php

declare(strict_types=1);

namespace MetaFramework\Dictionnaries\Traits\DataTables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use MetaFramework\Support\Traits\Responses;
use ReflectionClass;
use Throwable;

trait MassDelete
{
    use Responses;

    public function massDelete(Request $request, string $name = 'name'): array
    {
        $modelPath = trim((string) $request->get('model_path', $request->get('model', '')));
        $deletedMessage = $request->get('deleted_message');

        $this->enableAjaxMode();

        if ($modelPath === '') {
            $this->responseWarning(__('mfw-dictionnaries::mfw-dictionnaries.messages.mass_delete.no_model'));

            return $this->fetchResponse();
        }

        if (!str_contains($modelPath, '\\')) {
            $modelPath = 'MetaFramework\\Dictionnaries\\Models\\' . $modelPath;
        }

        if (!$request->filled('ids')) {
            $this->responseWarning(__('mfw-dictionnaries::mfw-dictionnaries.messages.mass_delete.no_ids'));

            return $this->fetchResponse();
        }

        try {
            $model = (new ReflectionClass($modelPath))->newInstance();
            if (!$model instanceof Model) {
                throw new \InvalidArgumentException(__('mfw-dictionnaries::mfw-dictionnaries.messages.mass_delete.not_eloquent', ['model' => $modelPath]));
            }

        } catch (Throwable $e) {
            $this->responseException(
                $e,
                __('mfw-dictionnaries::mfw-dictionnaries.messages.mass_delete.invalid_model', ['model' => $request->get('model')])
            );

            return $this->fetchResponse();
        }

        $ids = explode(',', $request->get('ids'));

        $items = $model->query()->whereIn('id', $ids)->get()->pluck($name, 'id')->toArray();
        foreach ($ids as $item) {
            try {
                $model->query()->where('id', $item)->delete();
                $this->responseSuccess($deletedMessage ?? __('mfw-dictionnaries::mfw-dictionnaries.messages.mass_delete.item_deleted', [
                    'name' => $items[$item] ?? __('mfw-dictionnaries::mfw-dictionnaries.labels.item'),
                ]));
            } catch (Throwable $e) {
                $this->responseException($e, __('mfw-dictionnaries::mfw-dictionnaries.messages.mass_delete.delete_attempt_failed', [
                    'name' => $items[$item] ?? ('#' . $item),
                ]));
            }
        }

        return $this->fetchResponse();
    }
}
