class ReportsList {
    constructor() {
        $('#listingTable').treegrid({
            "initialState": "collasped",
            expanderExpandedClass: 'glyphicon glyphicon-folder-open',
            expanderCollapsedClass: 'glyphicon glyphicon-folder-close'
        });
        var urlParams = new URLSearchParams(window.location.search);
        $('.treegrid-' + urlParams.get('dir')).treegrid('expand');
    }
}
