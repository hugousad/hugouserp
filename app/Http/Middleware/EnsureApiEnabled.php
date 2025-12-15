<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if API access is enabled via system settings.
 * 
 * This middleware respects the 'advanced.enable_api' system setting
 * and returns a 503 Service Unavailable if API is disabled.
 */
class EnsureApiEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiEnabled = Cache::remember('api_enabled_setting', 300, function () {
            $setting = SystemSetting::where('key', 'advanced.enable_api')->first();
            
            // Default to true if setting doesn't exist
            if (!$setting) {
                return true;
            }
            
            // Handle various possible stored values
            $value = $setting->value;
            
            if (is_bool($value)) {
                return $value;
            }
            
            if (is_string($value)) {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
            
            return (bool) $value;
        });

        if (!$apiEnabled) {
            return response()->json([
                'success' => false,
                'message' => 'API access is currently disabled. Please contact the administrator.',
                'error' => 'api_disabled',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $next($request);
    }
}
