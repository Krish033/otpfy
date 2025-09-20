<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class RequestInterseptor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);

            // Apply formatting only if the request expects JSON
            if ($request->expectsJson()) {
                $status = $response->getStatusCode();

                // Normalize original content
                if ($response instanceof JsonResponse) {
                    $original = $response->getData(true);
                } else {
                    $original = $this->decodeJsonSafely($response->getContent());
                }

                // Extract message
                $message = $this->extractMessage($original, $status);

                // Extract data only if success
                $data = $status >= 200 && $status < 300
                    ? ($original['data'] ?? $original)
                    : null;

                return response()->json([
                    'status'  => $status,
                    'message' => $message,
                    'data'    => $data,
                ], $status);
            }

            return $response;
        } catch (\Throwable $e) {
            // Catch unexpected errors gracefully
            return response()->json([
                'status'  => 500,
                'message' => 'Internal Server Error',
                'error'   => app()->environment('local') ? $e->getMessage() : 'Something went wrong.',
                'data'    => null,
            ], 500);
        }
    }

    /**
     * Safely decode JSON content, fallback to string if invalid.
     *
     * @param mixed $content
     * @return array|string
     */
    protected function decodeJsonSafely($content)
    {
        $decoded = json_decode($content, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : (string) $content;
    }

    /**
     * Extract message based on response type and status.
     *
     * @param mixed $original
     * @param int   $status
     * @return string
     */
    protected function extractMessage($original, int $status): string
    {
        if (is_array($original) && isset($original['message'])) {
            return $original['message'];
        }

        if ($status >= 200 && $status < 300) {
            return 'success';
        }

        return 'error';
    }
}
