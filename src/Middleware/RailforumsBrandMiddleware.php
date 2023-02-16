<?php

namespace Railroad\Railforums\Middlware;

use Closure;
use Railroad\Railforums\Services\ConfigService;
use Illuminate\Http\Request;


class RailforumsBrandMiddleware
{
    /**
     * Set brand in case it is present in the request
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if ($request->has('brand') && ConfigService::$dataMode == 'host') {
            $brand = $request->get('brand');
            $railforumsConnectionName = config('railforums.brand_database_connection_names')[$brand];
            \Railroad\Railforums\Services\ConfigService::$databaseConnectionName = $railforumsConnectionName;
            config()->set('railforums.database_connection', $railforumsConnectionName);
            config()->set('railforums.database_connection_name', $railforumsConnectionName);
            config()->set('railforums.brand', $brand);
        }

        return $next($request);
    }

}