<?php namespace Reports\Responses;

use Reports\Report;
use Reports\Contracts\Headers\{AltersHtmlResult, AltersResult};

// TODO: Remove this and move to Report
abstract class AbstractResponse
{
    protected function filter(Report $report, $html = false)
    {
        $final = $original = $report->run();


            foreach ($report->headers as $header) {
                if (!$html && $header instanceof AltersResult) {
                    $final = $header->filterResults($original, $final);
                } elseif ($header instanceof AltersHtmlResult) {
                    $final = $header->filterHtmlResults($original, $final);
                }
            }
            return $final;

    }
}
