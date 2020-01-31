<?php

namespace InetStudio\FavoritesPackage\Favorites\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class IsFavoritable.
 */
class IsFavoritable implements Rule
{
    /**
     * @var string
     */
    protected $message;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     *
     * @throws BindingResolutionException
     */
    public function passes($attribute, $value)
    {
        [$type, $id] = explode('_', $value);
        $availableTypes = config('favorites_package_favorites.favoritable', []);

        if (! isset($availableTypes[$type])) {
            $this->message = trans('favorites_package_favorites::errors.materialIncorrectType');

            return false;
        }

        $model = app()->make($availableTypes[$type]);

        if (! (! is_null($id) && $id > 0 && $item = $model::find($id))) {
            $this->message = trans('favorites_package_favorites::errors.materialNotFound');

            return false;
        }

        request()->merge(compact('item'));

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
