<?php namespace Reports\Responses;

use Illuminate\Support\Facades\Response;
use Reports\Contracts\Response as ReportResponse;
use Reports\Contracts\Headers\AltersResult;
use Reports\Report;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class CsvResponse implements ReportResponse
{
    public function make(Report $report)
    {
        if (!$report->ready) {
            return $report->toArray()['message'];
        }

        $final = $original = $report->run();
        foreach ($report->headers as $header) {
            if ($header instanceof AltersResult) {
                $final = $header->filterResults($original, $final);
            }

        }

        $final->prepend(array_keys($final->first()));
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($final->toArray());
        $temp_name = md5(time() . $report->name);
        $writer = (new Csv($spreadsheet))->save(storage_path() . '/app/' . $temp_name . '.csv');

        return Response::download(
            storage_path() . '/app/' . $temp_name . '.csv',
            snake_case($report->name) . '.csv'
        )->deleteFileAfterSend(true);
    }
}
