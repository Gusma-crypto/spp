<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class BreadcrumbMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $segments = array_values(array_filter($request->segments(), function ($segment) {
            return !preg_match('/^[a-f0-9]{8}-?[a-f0-9]{4}-?[a-f0-9]{4}-?[a-f0-9]{4}-?[a-f0-9]{12}$/i', $segment);
        }));

        $breadcrumbs = [];
        $url = '';

        foreach ($segments as $key => $segment) {
            $url .= '/' . $segment;
            $label = ucwords(str_replace('-', ' ', $segment));

            $breadcrumbs[] = [
                'label' => $label,
                'url' => $key === count($segments) - 1 ? null : url($url)
            ];
        }

        View::share('breadcrumbs', $breadcrumbs);

        return $next($request);
    }
}
