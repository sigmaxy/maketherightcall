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
    function dial_mobile_number() {
        console.log('mobile is '+$('#dial_mobile_no').val());
    }
    $( "#dialog_dial" ).dialog({
        autoOpen: false,
        height: 200,
        width: 300,
        modal: true,
        buttons: {
            "Press me and Call": dial_mobile_number,
            Cancel: function() {
                $(this).dialog( "close" );
            }
        },
        close: function() {
        }
    });
});
(function($) {
	$.fn.open_new_tab = function(data){
        $( "#dialog_dial" ).dialog( "open" );
	}
})(jQuery);
