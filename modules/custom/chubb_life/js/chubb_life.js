jQuery(document).ready(function($){
    var datatable = $('table.table_list_data_ajax');
    datatable.DataTable({
        // serverSide: true,
        "order": [[ datatable.attr('col_sort_index') , datatable.attr('col_sort_type') ]],
        "pageLength": datatable.attr('default_page_length') ? datatable.attr('default_page_length') : 10,
        processing: true,
        ajax: {
            url: window.location.origin+drupalSettings.path.baseUrl+'purchaseorder/data/listpo/',
            type: 'POST'
        }
    });
});