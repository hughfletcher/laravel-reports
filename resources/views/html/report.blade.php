@extends(config('reports.layout'))

@push('css')
    <link href="{{ asset('vendor/reports/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/reports/css/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/reports/css/prettify.css') }}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ asset('vendor/reports/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/jquery.stickytableheaders.min.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/prettify.js') }}"></script>
    <script>window.Report = {!! json_encode($report->config()) !!}</script>
    <script>
    $( document ).ready( function( $ ) {
        if (!Report.ready) {
            $("#async-notice").hide();
        }
        if (Report.ready) {
            console.log(Report)
            $.ajax({
                "url": Report.report_url + '/jsonh',
                "data": {
                    'report': Report.report_path,
                    'macros': Report.macros
                },
                "success": function(json) {
                    var tableHeaders = "";
                    var columns = [];

                    if (json.message) {
                        $("#async-notice").hide();
                        $("#need-info").empty().append(json.message).removeClass('hide');
                        return;
                    }

                    $.each(json.result[0], function(i, val){
                        if (i != 'children') {
                            tableHeaders += "<th>" + i + "</th>";
                            columns.push({data: i})
                        }
                    });

                    $("#async-notice").hide();
                    $("#report-table").empty();
                    $("#report-table").append('<table id="displayTable" class="display table table-bordered"><thead><tr>' + tableHeaders + '</tr></thead></table>');
                    $('#displayTable').dataTable({
                        data: json.result,
                        columns: columns,
                        lengthChange: false,
                        paginate: false,
                        searching: !Report.vertical,
                        info: !Report.vertical,
                        ordering: !Report.vertical,
                        dom: "<'row'<'col-md-12'fi>r>t",
                        "language": {
                            "emptyTable": "No data available in table"
                        }
                    });
                    $('#displayTable').stickyTableHeaders({fixedOffset: $('.navbar')});
                    $("#query_holder").append('<pre class="prettyprint">' + json.query + '</pre>');
                    PR.prettyPrint();
                },
                "dataType": "json"
            });
        }
        $('input.date').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'),10),
            locale: { format: "YYYY-MM-DD" }
        });
        $('input.daterange').daterangepicker({
            showDropdowns: true,
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'),10),
            locale: { format: "YYYY-MM-DD" }
        });
    });
    </script>
@endpush

@section('content')
<h1 class="page-header">{{ !is_null($report->name) ? $report->name : $report->report_path }}<small class="hidden-sm hidden-xs">{{ $report->description }}</small></h1>
<div class="alert alert-{{ $report->header_errors || $report->macro_errors ? 'danger' : 'info' }}{{ $report->ready ? ' hide' : '' }}" id="need-info">
    @if ($report->header_errors)
    <h4>There are issues with your file configuration.</h4>
        @foreach ($report->header_errors as $reporter => $error)
            <dl> <dt>{{ $reporter }}</dt>
            @foreach ($error->all() as $message)
                <dd>{{ $message }}</dd>
            @endforeach
            </dl>
        @endforeach
    @elseif($report->macro_errors)
        <h4>There are issues with your report configuration.</h4>
        <ul>
        @foreach ($report->macro_errors->all() as $error)
                <li>{{ $error }}</li>
        @endforeach
        </ul>
    @else
    This report needs more information before running.
    @endif
</div>
@includeWhen((isset($report->headers['variables'])), 'reports::html.variables')
<div class='alert alert-block alert-info{{ !$report->ready ? 'display: none;' : null }}' id='async-notice' style='text-align: center;'>
    <div>Your report is running.</div>
    <div class="progress progress-striped active" style="width: 50%; margin: 20px auto 0;">
        <div class="progress-bar"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
            <span class="sr-only">Working</span>
        </div>
    </div>
</div>
<div class="row mb-10{{ !$report->ready ? ' hide' : '' }}">
    <div class="col-xs-12">
        <div class="btn-group">
            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Download/show as <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="{{ route('reports.report', ['format' => 'xlsx', 'report' => $report->report_path, 'macros' => $report->macros]) }}">Download Excel</a></li>
                @if(class_exists('dekor\ArrayToTextTable'))<li><a href="{{ route('reports.report', ['format' => 'text', 'report' => $report->report_path, 'macros' => $report->macros]) }}">Text</a></li>@endif
                <li role="separator" class="divider"></li>
                <li><a href="{{ route('reports.report', ['format' => 'json', 'report' => $report->report_path, 'macros' => $report->macros]) }}">JSON</a></li>
            </ul>
        </div>
        {{-- <button type="button" class="btn btn-primary btn-sm">Email</button> --}}
    </div>
</div>
<div id="report-table" class="table-responsive table-striped reports"></div>
<a data-role="button" data-toggle="collapse" data-target="#query_holder" href="#query_holder">show query</a>
<div id='query_holder' class='collapse' style='padding-left: 20px;'></div>
@endsection
