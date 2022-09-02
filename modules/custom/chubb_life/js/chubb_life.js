jQuery(document).ready(function($){
    $('table.premium_list').DataTable({
        // "searching": false,
        dom: 'lrt',
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
                        .sort()
                        .each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });
                });
        },
    });
    function sale_sample_data(){
        jQuery('input[name="last_name"]').val('last_name');
        jQuery('input[name="first_name"]').val('first_name');
        jQuery('input[name="chinese_name"]').val('chinese_name');
        jQuery('input[name="hkid"]').val('R123456(7)');
        jQuery('input[name="dob"]').val('1990-12-31');
        jQuery('input[name="email"]').val('email@email.com');
        jQuery('input[name="flat"]').val('flatA1');
        jQuery('input[name="floor"]').val('2');
        jQuery('input[name="block"]').val('3A');
        jQuery('input[name="building"]').val('building');
        jQuery('input[name="street"]').val('street5');
        jQuery('input[name="district"]').val('district6');
        jQuery('input[name="mobile"]').val('87654321');
        jQuery('input[name="monthly_income"]').val(3);
        jQuery('input[name="solicitation"]').val(2);
        jQuery('input[name="opt_out_reason"]').val('opt_out_reason');
        jQuery('input[name="payor_last_name"]').val('payor_last_name');
        jQuery('input[name="payor_first_name"]').val('payor_first_name');
        jQuery('input[name="payor_hkid"]').val('A654321(X)');
        jQuery('input[name="payor_dob"]').val('1991-12-31');
        jQuery('input[name="payment_mode"]').val('payment_mode');
        jQuery('input[name="pep"]').val('pep');
        jQuery('input[name="another_person"]').val('another_person');
        jQuery('input[name="plan_code"]').val('plan_code');
        jQuery('input[name="plan_level"]').val('plan_level');
        jQuery('input[name="face_amount"]').val('face_amount');
        jQuery('input[name="family_package"]').val('family_package');
        jQuery('input[name="replacement_declaration"]').val('replacement_declaration');
    }
});