<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\Traits\CustomErrorMessage;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateOfferRequest
 * @package App\Http\Requests
 */
class CreateOfferRequest extends FormRequest
{
    use CustomErrorMessage;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'region_id'     => 'required|exists:regions,id',
            'price_from'    => 'required|numeric',
            'price_to'      => 'required|numeric',
            'rooms'         => 'required|int',
        ];
    }
}
