<?php

namespace App\Http\Middleware;

use App\Services\ApiService;
use Closure;
use Illuminate\Http\Request;

class WebAuth
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$this->apiService->isAuthenticated()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}