<?php

namespace InetStudio\FavoritesPackage\Favorites\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;
use Packages\FavoritesPackage\Favorites\Validation\Rules\IsFavoritable;
use InetStudio\FavoritesPackage\Favorites\Contracts\Http\Requests\Front\AddRequestContract;

/**
 * Class AddRequest.
 */
class AddRequest extends FormRequest implements AddRequestContract
{
    /**
     * Определить, авторизован ли пользователь для этого запроса.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Сообщения об ошибках.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Тип материала обязателен для передачи.',
            'type.string' => 'Тип материала должен быть строкой.',
            'id.required' => 'Id материала обязателен для передачи.',
            'id.integer' => 'Id материала должен быть целочисленным значением.',
        ];
    }

    /**
     * Правила проверки запроса.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => 'required|string',
            'id' => 'required|integer',
            'item' => new IsFavoritable(),
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $type = mb_strtolower($this->route('type', ''));
        $id = (int) $this->route('id', 0);
        $item = $type.'_'.$id;

        $this->merge(compact('type','id', 'item'));
    }
}
