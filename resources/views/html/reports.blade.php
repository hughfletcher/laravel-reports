@extends('reports::html.layout')

@push('css')
    <link href="{{ asset('vendor/reports/css/jquery.treegrid.css') }}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ asset('vendor/reports/js/jquery.treegrid.min.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/jquery.treegrid.bootstrap3.js') }}"></script>
    <script>
    $( document ).ready( function( $ ) {
        $('#listingTable').treegrid({
            "initialState": "collasped",
            expanderExpandedClass: 'glyphicon glyphicon-folder-open',
            expanderCollapsedClass: 'glyphicon glyphicon-folder-close'
        });
        $('.treegrid-{{ request()->query('dir') }}').treegrid('expand');
    });
    </script>
@endpush

@section('content')
<h1 class="page-header">Reports<small class="hidden-sm hidden-xs">Listing</small></h1>
<div id="report-table" class="table-responsive table-striped reports">
    <table id="listingTable" class="table table-bordered">
        <thead>
            <tr>
                <td style="width:400px;">Title</td>
                <td style="width:35%;">Description</td>
                <td>Source</td>
                <td>Last Modified</td>
            </tr>
        </thead>
        <tbody>
        @include('reports::html.tree', ['children' => Reports::all()])
    </tbody>
    </table>
</div>
@endsection
