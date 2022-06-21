<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;

class GamePlacePieceRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'x' => 'required|numeric|min:0|max:2',
            'y' => 'required|numeric|min:0|max:2',
        ];
    }
}
