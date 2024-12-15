<?php

namespace App\Traits;

use \Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

trait HttpResponses
{
    protected function formatErrors(ValidationException $exception): array
    {
        $errors = [];
        foreach ($exception->errors() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'field' => $field,
                    'message' => $message
                ];
            }
        }

        return $errors;
    }

    protected function success($data = [], string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'payload' => $data,
            'message' => $message
        ], $code);
    }

    protected function error(string|array $message, int $code, $data = []): JsonResponse|Throwable
    {
        return response()->json([
            'status' => false,
            'status_code' => $code,
            'message' => $message,
            'payload' => $data,
        ], $code);
    }

    protected function validationError(string|array $message, int $code, $errors = []): JsonResponse|Throwable
    {
        return response()->json([
            'status' => false,
            'status_code' => $code,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}
