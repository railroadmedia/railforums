<?php

namespace Railroad\Railforums\Middleware;

use DOMDocument;
use Illuminate\Http\Request;
use Closure;

class SignatureHTMLSyntaxCheck
{
    public function handle(Request $request, Closure $next) {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $ret = $doc->loadXML('<div>' . html_entity_decode($request->get('signature')) . '</div>');
        libxml_clear_errors();
        if (!$ret) {
           return redirect()->back()->with('error', 'Invalid signature. Please check its syntax.');
        }
        return $next($request);
    }
}