<?php

declare(strict_types=1);

namespace App\Http\Requests\Task;

use App\Models\Task;
use App\Rules\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', [Task::class, Task::find($this->task)]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string'
            ],
            'description' => [
                'required',
                'string'
            ],
            'status' => [
                'required',
                'string',
                new TaskStatus()
            ],
        ];
    }
}
