<?php

namespace Railroad\Railforums\Middleware;

use Closure;
use Illuminate\Http\Request;
use Railroad\Railforums\Services\HTMLPurifierService;

class HTMLSanitization
{
    public function handle(Request $request, Closure $next)
    {
        $purifier_service = new HTMLPurifierService();
        $filtered_html = $purifier_service->clean(
            preg_replace(
                '/[oO][nN](.*?)=[\'\"](.*?)[\'\"]/',
                '',
                $request->get('signature')
            )
        );

        $request->merge(['signature' => $filtered_html]);
        return $next($request);
    }
}