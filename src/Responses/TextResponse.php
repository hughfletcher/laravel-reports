<?php namespace Reports\Responses;

use Reports\Contracts\Response;
use Reports\Report;
use dekor\ArrayToTextTable;

class TextResponse extends AbstractResponse implements Response
{
    public function make(Report $report)
    {
        if (!$report->ready) {
            return $report->toArray()['message'];
        }
        return response((new ArrayToTextTable($this->filter($report)->toArray()))->render(), 200)
            ->header('Content-Type', 'text/plain');
    }
}
