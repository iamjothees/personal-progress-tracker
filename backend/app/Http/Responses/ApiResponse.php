<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Arr;

class ApiResponse implements Responsable
{
    protected ?array $data = null;
    protected int $httpCode = 200;

    public function __construct(?array $data = null, int $httpCode = 200)
    {
        $this->httpCode = $httpCode;

        if ($data !== null) {
            if (Arr::isAssoc($data) === false){
                throw new \Exception("Data must be an associative array or null");
            }
            $this->data = $data;
        }
    }

    public function toResponse($request): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            data: $this->data,
            status: $this->httpCode,
            options: JSON_UNESCAPED_UNICODE
        );
    }
}