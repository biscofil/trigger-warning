<?php

namespace App\Http\Requests;

use App\Card;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class NewCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['required', 'numeric', Rule::in([Card::TypeCartToFill, Card::TypeFillingCart])],
            'content' => ['required', 'max:255'],
        ];
    }

    /**
     * @param User $me
     * @return Card
     * @throws ValidationException
     */
    public function store(User $me): Card
    {

        $validatedData = $this->validated();

        $type = intval($validatedData['type']);

        if ($type == Card::TypeCartToFill) {

            $count = substr_count($validatedData['content'], '@');

            if ($count === 0 || $count > 2) {

                throw ValidationException::withMessages([
                    'content' => 'Puoi mettere uno o due spazi da riempire!'
                ]);

            }

        }

        return Card::create([
            'content' => $validatedData['content'],
            'original_content' => strpos($validatedData['content'], Card::NAME_PLACEHOLDER) !== false ? $validatedData['content'] : null,
            'type' => $type,
            'creator_user_id' => $me->id
        ]);

    }
}
