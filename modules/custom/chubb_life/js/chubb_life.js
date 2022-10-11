jQuery(document).ready(function($){
    $('table.premium_list').DataTable({
        // "searching": false,
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
    var table = $('table.order_list').DataTable({
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
                            // Get the search value
                            $(this).attr('title', $(this).val());
                            var regexr = '({search})'; //$(this).parents('th').find('select').val();
 
                            var cursorPosition = this.selectionStart;
                            // Search the column for that value
                            api
                                .column(colIdx)
                                .search(
                                    this.value != ''
                                        ? regexr.replace('{search}', '(((' + this.value + ')))')
                                        : '',
                                    this.value != '',
                                    this.value == ''
                                )
                                .draw();
                        })
                        .on('keyup', function (e) {
                            e.stopPropagation();
 
                            $(this).trigger('change');
                            $(this)
                                .focus()[0]
                                .setSelectionRange(cursorPosition, cursorPosition);
                        });
                });
        },
    });
    $('table.import_customer_list thead tr')
        // .not(":eq(0)")
        .clone(true)
        .addClass('filters')
        .appendTo('table.import_customer_list thead');
    var table = $('table.import_customer_list').DataTable({
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
                            // Get the search value
                            $(this).attr('title', $(this).val());
                            var regexr = '({search})'; //$(this).parents('th').find('select').val();
 
                            var cursorPosition = this.selectionStart;
                            // Search the column for that value
                            api
                                .column(colIdx)
                                .search(
                                    this.value != ''
                                        ? regexr.replace('{search}', '(((' + this.value + ')))')
                                        : '',
                                    this.value != '',
                                    this.value == ''
                                )
                                .draw();
                        })
                        .on('keyup', function (e) {
                            e.stopPropagation();
 
                            $(this).trigger('change');
                            $(this)
                                .focus()[0]
                                .setSelectionRange(cursorPosition, cursorPosition);
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
            body: '<label for="mobile" style="margin-right:5px;">Mobile</label><input type="text" name="dial_mobile_no" id="dial_mobile_no" value="'+drupalSettings.mobile+'">',
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
	});
})(jQuery);
