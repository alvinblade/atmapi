<?php

namespace App\Handler;

use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
use Illuminate\Http\JsonResponse;

class GlobalExceptionHandler extends Handler
{
    use HttpResponses;

    protected function formatErrors(ValidationException $exception): array
    {
        $errors = [];
        foreach ($exception->errors() as $field => $messages) {

            foreach ($messages as $message) {
                $errors[$field] = [
                    'message' => $message
                ];
            }
        }

        return $errors;
    }

    public function render($request, Throwable $e): Response|Throwable|JsonResponse
    {
        return match ($request->expectsJson()) {
            $e instanceof ValidationException => $this->validationError(
                message: "Yoxlama xətası",
                code: 422,
                errors: $this->formatErrors($e)
            ),
            $e instanceof ModelNotFoundException,
                $e instanceof NotFoundHttpException => $this->error(
                message: $e->getMessage(),
                code: 404
            ),
            default => parent::render($request, $e)
        };
    }
}
