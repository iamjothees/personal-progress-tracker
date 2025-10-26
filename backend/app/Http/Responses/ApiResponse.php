<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Cookie;

class ApiResponse implements Responsable
{
    protected ?array $data = null;
    protected int $httpCode = 200;
    protected ?Cookie $cookie = null;

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

    public function withCookie(Cookie $cookie): self
    {
        $this->cookie = $cookie;
        return $this;
    }

    public function toResponse($request): \Illuminate\Http\JsonResponse
    {
        $response = response()->json(
            data: $this->data,
            status: $this->httpCode,
            options: JSON_UNESCAPED_UNICODE
        );

        if ($this->cookie !== null) {
            $response = $response->withCookie(cookie: $this->cookie);
        }

        return $response;
    }
}