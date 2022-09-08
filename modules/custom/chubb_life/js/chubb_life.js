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
