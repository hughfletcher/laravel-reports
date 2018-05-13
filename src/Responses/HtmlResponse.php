<?php namespace Reports\Responses;

use Reports\Contracts\Response;
use Reports\Report;
use Reports\Contracts\SupportsHtml;

class HtmlResponse implements Response
{
    public function make(Report $report)
    {
        foreach ($report->headers as $header) {
            if ($header instanceof SupportsHtml) {
                $header->provide();
            }

        }
        return response()->view('reports::html.report', compact('report'));
    }
}
