<?php

namespace App\Services\Observability;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CrashReporter
{
    public function report(Throwable $exception, ?Request $request = null): void
    {
        Log::channel('stack')->error('Application exception', [
            'exception' => $exception::class,
            'message' => $this->redact($exception->getMessage()),
            'route' => $request?->route()?->uri(),
            'method' => $request?->method(),
            'status' => method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : null,
        ]);
    }

    private function redact(string $message): string
    {
        return (string) preg_replace(
            ['/Bearer\s+[A-Za-z0-9._-]+/i', '/(password|token|secret|api[_-]?key)\s*[=:]\s*[^\s,]+/i'],
            ['Bearer [redacted]', '$1=[redacted]'],
            substr($message, 0, 500),
        );
    }
}
