<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $maxLength = $this->getPlatformMaxLength();

        return [
            'title' => 'required|string|max:255',
            'content' => ['required', 'string', "max:{$maxLength}"],
            'image' => 'nullable|sometimes|image|max:5120', // 5MB max
            'scheduled_time' => 'required|date|after:now',
            'platform_ids' => 'required|array|min:1',
            'platform_ids.*' => 'exists:platforms,id',
            'draft' => 'boolean'
        ];
    }

    private function getPlatformMaxLength()
    {
        $platformIds = $this->get('platform_ids', []);
        $maxLength = 280; // Default Twitter length

        // If LinkedIn is selected, use its limit
        if (in_array(3, $platformIds)) { // Assuming 3 is LinkedIn's ID
            $maxLength = 3000;
        }

        return $maxLength;
    }

    public function messages()
    {
        return [
            'content.max' => 'The content length exceeds the maximum allowed for the selected platforms.',
            'scheduled_time.after' => 'The scheduled time must be in the future.',
            'platform_ids.required' => 'At least one platform must be selected.',
        ];
    }
}