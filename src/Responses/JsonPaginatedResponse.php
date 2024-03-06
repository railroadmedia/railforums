<?php

namespace Railroad\Railforums\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class JsonPaginatedResponse implements Responsable
{
    public $results;
    public $totalResults;
    public $filterOptions;
    public $code;

    /**
     * JsonPaginatedResponse constructor.
     *
     * @param $results
     * @param $totalResults
     * @param $filterOptions
     * @param $code
     */
    public function __construct($results, $totalResults, $filterOptions, $code)
    {
        $this->results = $results;
        $this->totalResults = $totalResults;
        $this->filterOptions = $filterOptions;
        $this->code = $code;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function toResponse($request)
    {
        return response()->json(
            $this->transformResult($request),
            $this->code
        );
    }

    public function transformResult(Request $request)
    {
        return [
            'status' => 'ok',
            'code' => $this->code,
            'page' => $request->get('page', 1),
            'limit' => $request->get('amount', 10),
            'total_results' => $this->totalResults,
            'data' =>
                $this->results,
                'meta' => [
                    'totalResults' => $this->totalResults,
                    'page' => $request->get('page', 1),
                    'limit' => $request->get('amount', 10),
                    'filterOptions' => [],
                ],
            'results' => $this->results,
            'filter_options' => $this->filterOptions,
        ];
    }
}
