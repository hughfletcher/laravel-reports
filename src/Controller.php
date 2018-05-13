<?php

namespace Reports;

use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Http\Request;
use Reports\Facade as Reports;

class Controller extends LaravelController
{
    public function report(Request $request, $type = 'html')
    {
        // dd(request()->query('macros')['salary']);
        $report = Reports::find($request->query('report'));
        // dd($report);
        $report->macros($request->query('macros', []));

        return response()->{$type}($report);
    }
    public function reports()
    {
        // dd(Reports::all());

        return response()->view('reports::html.reports');
    }

}
