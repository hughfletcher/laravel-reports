<?php namespace Reports\Responses;

use Reports\Contracts\Response;
use Reports\Report;
use Reports\Contracts\Headers\AltersHtmlResult;

//json with meta
class JsonhResponse implements Response
{
    public function make(Report $report)
    {
        $final = $original = $report->run();
        foreach ($report->headers as $header) {
            if ($header instanceof AltersHtmlResult) {
                $final = $header->filterHtmlResults($original, $final);
            }
        }

        return response()->json([
            'result' => $final,
            'query' => $report->query
        ]);
    }
}
