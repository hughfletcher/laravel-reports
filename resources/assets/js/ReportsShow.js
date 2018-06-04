class ReportsShow {
    constructor() {
        this.initDate();
        this.initDateRange();
        if (Report.ready) {
            console.log(Report)
            $.ajax({
                "url": Report.report_url + '/jsonh',
                "data": {
                    'report': Report.report_path,
                    'macros': Report.macros
                },
                "success": this.ajaxSucess,
                "dataType": "json"
            });
        }
    }
    ajaxSucess(json) {


        // if (json.message) {
        //     $("#async-notice").hide();
        //     // $("#need-info").empty().append(json.message).removeClass('hide');
        //     return;
        // }



        $("#async-notice").hide();
        $("#report-table").empty();
        var columns = ReportsShow.renderTable(json.result);
        ReportsShow.initDataTable(columns, json.result);

        $('#displayTable').stickyTableHeaders({
            fixedOffset: ($('.sticky-table-header-offset').length >= 1 ? $('.sticky-table-header-offset') : 0)
        });
        $('.box-header button').removeClass('disabled');
        $("#query_holder").append('<pre class="prettyprint">' + json.query + '</pre>');
        PR.prettyPrint();
    }
    static renderTable(data) {
        var tableHeaders = "";
        var columns = [];
        $.each(data[0], function(i, val){
            if (i != 'children') {
                tableHeaders += "<th>" + i + "</th>";
                columns.push({data: i})
            }
        });
        $("#report-table").append('<table id="displayTable" class="display table table-bordered"><thead><tr>' + tableHeaders + '</tr></thead></table>');
        return columns;
    }
    static initDataTable(columns, data) {
        $('#displayTable').dataTable({
            data: data,
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
    }
    initDate() {
        $('input.date').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'),10),
            locale: { format: "YYYY-MM-DD" }
        });
    }
    initDateRange() {
        $('input.daterange').daterangepicker({
            showDropdowns: true,
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'),10),
            locale: { format: "YYYY-MM-DD" }
        });
    }
}
