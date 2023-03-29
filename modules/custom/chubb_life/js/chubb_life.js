jQuery(document).ready(function($){
    var datatable_premium_list = $('table.premium_list');
    var datatable_obj = datatable_premium_list.DataTable({
        // "searching": false,
        // "order": datatable_premium_list.attr('col_sort_index') ? [ datatable_premium_list.attr('col_sort_index'), datatable_premium_list.attr('col_sort_type')] : [],
		// "pageLength": datatable_premium_list.attr('default_page_length') ? datatable_premium_list.attr('default_page_length') : 10,
		// "orderCellsTop": true,
		// "processing": true,
        dom: 'lrt',
        processing: true,
        ajax: {
            url: window.location.origin+drupalSettings.path.baseUrl+'chubb_life/data/listpremium/',
            type: 'POST'
        },
        initComplete: function () {
            this.api()
                .columns()
                .every(function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    column
                        .data()
                        .unique()
                        .sort(function(a,b){
                            // if(!isNaN(a)&&!isNaN(b)){
                                return a-b;
                            // }
                        })
                        .each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });
                });
        },
    });
    $('table.order_list thead tr')
        // .not(":eq(0)")
        .clone(true)
        .addClass('filters')
        .appendTo('table.order_list thead');
    var datatable_order_list = $('table.order_list');
    var datatable_obj = datatable_order_list.DataTable({
        "order": datatable_order_list.attr('col_sort_index') ? [ datatable_order_list.attr('col_sort_index'), datatable_order_list.attr('col_sort_type')] : [],
		"pageLength": datatable_order_list.attr('default_page_length') ? datatable_order_list.attr('default_page_length') : 10,
		"orderCellsTop": true,
		"processing": true,
        orderCellsTop: true,
        fixedHeader: true,
        initComplete: function () {
            var api = this.api();
            // For each column
            api
                .columns()
                .eq(0)
                .each(function (colIdx) {
                    // Set the header cell to contain the input element
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                    if(colIdx==0 || colIdx==9 ){
                        $(cell).html('');
                        return false;
                    }
                    var title = $(cell).text();
                    $(cell).html('<input type="text" class="datatable_filter_header" placeholder="' + title + '" />');
 
                    // On every keypress in this input
                    $(
                        'input',
                        $('.filters th').eq($(api.column(colIdx).header()).index())
                    )
                        .off('keyup change')
                        .on('change', function (e) {
                            if (api.column(colIdx).search() !== this.value) {
                                api.column(colIdx).search(this.value).draw();
                            }
                        })
                        .on('keyup', function (e) {
                            e.stopPropagation();
                            $(this).trigger('change');
                        });
                });
        },
    });
    $('.import_customer_list thead tr')
    .clone(true)
    .addClass('filters')
    .appendTo('table.import_customer_list thead');
    $(document).on('click','#customer_list_checkall',function(e) {
        console.log('customer_list_checkall is '+$('#customer_list_checkall').is(':checked'));
        if ($('#customer_list_checkall').is(':checked')) {
            $(".customer_list_row_checkbox").prop( "checked", true );
        }else{
            $(".customer_list_row_checkbox").prop( "checked", false );
        }
	});
    var datatable_customer_list = $('.import_customer_list').DataTable({
        orderCellsTop: true,
        fixedHeader: true,
		processing: true,
        serverSide: true,
        ajax: window.location.origin+drupalSettings.path.baseUrl+'chubb_life/data/list_customer2/',
        initComplete: function () {
            var api = this.api();
            api
                .columns()
                .eq(0)
                .each(function (colIdx) {
                    // Set the header cell to contain the input element
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                    if(colIdx==0){
                        $(cell).html('<input type="checkbox" id="customer_list_checkall" name="select_all" value="">');
                        return false;
                    }
                    if(colIdx==9 ){
                        $(cell).html('');
                        return false;
                    }else{

                    }
                    var title = $(cell).text();
                    $(cell).html('<input type="text" class="datatable_filter_header" placeholder="' + title + '" />');
                    // On every keypress in this input
                    $(
                        'input',
                        $('.filters th').eq($(api.column(colIdx).header()).index())
                    )
                        .off('keyup change')
                        .on('change', function (e) {
                            if (api.column(colIdx).search() !== this.value) {
                                api.column(colIdx).search(this.value).draw();
                            }
                        })
                        .on('keyup', function (e) {
                            e.stopPropagation();
                            $(this).trigger('change');
                        });
                });
        },
    });
    $('.call_list thead tr')
    .clone(true)
    .addClass('filters')
    .appendTo('.call_list thead');
    var datatable_call_list = $('.call_list').DataTable({
        orderCellsTop: true,
        fixedHeader: true,
		processing: true,
        serverSide: true,
        ajax: window.location.origin+drupalSettings.path.baseUrl+'chubb_life/data/list_call2/',
        initComplete: function () {
            var api = this.api();
            api
                .columns()
                .eq(0)
                .each(function (colIdx) {
                    // Set the header cell to contain the input element
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                    // if(colIdx==0){
                    //     $(cell).html('<input type="checkbox" id="customer_list_checkall" name="select_all" value="">');
                    //     return false;
                    // }
                    if(colIdx==10 ){
                        $(cell).html('');
                        return false;
                    }else{

                    }
                    var title = $(cell).text();
                    $(cell).html('<input type="text" class="datatable_filter_header" placeholder="' + title + '" />');
                    // On every keypress in this input
                    $(
                        'input',
                        $('.filters th').eq($(api.column(colIdx).header()).index())
                    )
                        .off('keyup change')
                        .on('change', function (e) {
                            if (api.column(colIdx).search() !== this.value) {
                                api.column(colIdx).search(this.value).draw();
                            }
                        })
                        .on('keyup', function (e) {
                            e.stopPropagation();
                            $(this).trigger('change');
                        });
                });
        },
    });
});
(function($) {
	$.fn.open_new_tab = function(data){
        // $( "#dialog_dial" ).dialog( "open" );
        let call_dialog = xdialog.create({
            title: 'Call Customer', 
            body: '<label for="mobile" style="margin-right:5px;">Mobile</label><input type="text" name="dial_mobile_no" id="dial_mobile_no" value="'+drupalSettings.mobile+'"><input type="hidden" name="dial_url" id="dial_url" value="'+drupalSettings.dial_url+'"></input>',
            buttons: {
                ok: {
                    text: 'Press me and Call',
                    // style: 'background:#ff3399;',
                    clazz: 'call_button'
                },
                cancel: 'Cancel',
            },
            // onok: console.log('mobil11e is '+$('#dial_mobile_no').val()),
        });
        call_dialog.show();
	}
    $(document).on('click','.call_button',function(e) {
        console.log('mobile is '+$('#dial_mobile_no').val());
        let dia_url = $('#dial_url').val()+$('#dial_mobile_no').val();
        console.log('url is '+dia_url);

        $.ajax({
            type: 'POST',
            crossDomain: true,
            url: dia_url, 
            success: function(result){
                console.log('success');
                console.log(result);
            }
        })
        // var win = window.open(dia_url, '_blank');
	});
})(jQuery);
