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
                        .sort()
                        .each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });
                });
        },
    });
    $(document).on('change','#edit-same-as-owner',function(e) {
        toggle_customer_insured_same_as_owner();
	});
    toggle_customer_insured_same_as_owner();
    function toggle_customer_insured_same_as_owner(){
        if($('#edit-same-as-owner').val()=='Y'){
            $('#edit-customer-insured').hide();
        }else{
            $('#edit-customer-insured').show();
        }
    }
});




(function($) {
	$.fn.open_new_tab = function(data){
		open(data, '_blank')
	}
})(jQuery);
function sale_sample_data(){
    jQuery('input[name="aeonRefNumber"]').val('aeonRefNumber');
    jQuery('input[name="same_as_owner"]').val('Y');
    jQuery('input[name="surname"]').val('surname');
    jQuery('input[name="givenName"]').val('givenName');
    jQuery('input[name="chineseName"]').val('中文名');
    jQuery('select[name="relationship"]').val('FAT');
    jQuery('select[name="identityType"]').val('I');
    jQuery('input[name="identityNumber"]').val('A123456(7)');
    jQuery('select[name="issueCountry"]').val('HK');
    jQuery('select[name="gender"]').val('F');
    jQuery('select[name="isPermanentHkid"]').val('Y');
    jQuery('input[name="birthDate"]').val('1990-12-31');
    jQuery('select[name="marital_status"]').val('2');
    jQuery('select[name="nationality"]').val('HK');
    jQuery('select[name="taxResidency1"]').val('CP');
    jQuery('input[name="taxResidencyTin1"]').val('OWTX01');
    jQuery('select[name="taxResidency2"]').val('CP');
    jQuery('input[name="taxResidencyTin2"]').val('OWTX02');
    jQuery('select[name="taxResidency3"]').val('CP');
    jQuery('input[name="taxResidencyTin3"]').val('OWTX03');
    jQuery('input[name="email"]').val('email@email.com');
    jQuery('input[name="residence_address1"]').val('residence_address1');
    jQuery('input[name="residence_address2"]').val('residence_address2');
    jQuery('input[name="residence_address3"]').val('residence_address3');
    jQuery('input[name="residence_city"]').val('Hong Kong');
    jQuery('select[name="residence_country"]').val('HK');
    jQuery('input[name="mailing_address1"]').val('mailing_address1');
    jQuery('input[name="mailing_address2"]').val('mailing_address2');
    jQuery('input[name="mailing_address3"]').val('mailing_address3');
    jQuery('input[name="mailing_city"]').val('mailing_city');
    jQuery('select[name="mailing_country"]').val('HK');
    jQuery('select[name="occupationCode"]').val('A020101');
    jQuery('input[name="mobile"]').val('87654321');
    jQuery('select[name="smoker"]').val('Y');
    jQuery('select[name="monthly_income"]').val('10,000');
    jQuery('select[name="solicitation"]').val('Y');
    jQuery('select[name="opt_out_reason"]').val('OFTA');
    jQuery('input[name="customer_insured_surname"]').val('customer_insured_surname');
    jQuery('input[name="customer_insured_givenName"]').val('customer_insured_givenName');
    jQuery('input[name="customer_insured_chineseName"]').val('customer_insured_chineseName');
    jQuery('select[name="customer_insured_identityType"]').val('P');
    jQuery('input[name="customer_insured_identityNumber"]').val('S232323(4)');
    jQuery('select[name="customer_insured_issueCountry"]').val('HK');
    jQuery('select[name="customer_insured_gender"]').val('M');
    jQuery('select[name="customer_insured_isPermanentHkid"]').val('Y');
    jQuery('input[name="customer_insured_birthDate"]').val('1991-10-11');
    jQuery('select[name="customer_insured_marital"]').val('2');
    jQuery('select[name="customer_insured_nationality"]').val('HK');
    jQuery('select[name="customer_insured_taxResidency1"]').val('MC');
    jQuery('input[name="customer_insured_taxResidencyTin1"]').val('B123443(1)');
    jQuery('select[name="customer_insured_taxResidency2"]').val('MC');
    jQuery('input[name="customer_insured_taxResidencyTin2"]').val('B123443(2)');
    jQuery('select[name="customer_insured_taxResidency3"]').val('MC');
    jQuery('input[name="customer_insured_taxResidencyTin3"]').val('B123443(3)');
    jQuery('input[name="customer_insured_email"]').val('email@email.com');
    jQuery('input[name="customer_insured_residence_address1"]').val('customer_insured_residence_address1');
    jQuery('input[name="customer_insured_residence_address2"]').val('customer_insured_residence_address2');
    jQuery('input[name="customer_insured_residence_address3"]').val('customer_insured_residence_address3');
    jQuery('input[name="customer_insured_residence_city"]').val('customer_insured_residence_city');
    jQuery('select[name="customer_insured_residence_country"]').val('HK');
    jQuery('input[name="customer_insured_mailing_address1"]').val('customer_insured_mailing_address1');
    jQuery('input[name="customer_insured_mailing_address2"]').val('customer_insured_mailing_address2');
    jQuery('input[name="customer_insured_mailing_address3"]').val('customer_insured_mailing_address3');
    jQuery('input[name="customer_insured_mailing_city"]').val('customer_insured_mailing_city');
    jQuery('select[name="customer_insured_mailing_country"]').val('HK');
    jQuery('select[name="customer_insured_occupationCode"]').val('T100101');
    jQuery('input[name="customer_insured_mobile"]').val('98765432');
    jQuery('select[name="customer_insured_smoker"]').val('Y');
    jQuery('select[name="customer_insured_monthly_income"]').val('10,000');
    jQuery('select[name="customer_insured_solicitation"]').val('Y');
    jQuery('select[name="customer_insured_opt_out_reason"]').val('COPS');
    jQuery('input[name="customer_payor_surname"]').val('customer_payor_surname');
    jQuery('input[name="customer_payor_givenName"]').val('customer_payor_givenName');
    jQuery('input[name="customer_payor_chineseName"]').val('customer_payor_chineseName');
    jQuery('select[name="customer_payor_identityType"]').val('I');
    jQuery('input[name="customer_payor_identityNumber"]').val('A321654(7)');
    jQuery('select[name="customer_payor_gender"]').val('F');
    jQuery('input[name="customer_payor_birthDate"]').val('2011-02-01');
    jQuery('select[name="currency"]').val('HKD');
    jQuery('select[name="paymentMode"]').val('12');
    jQuery('input[name="pep"]').val('pep');
    jQuery('select[name="another_person"]').val('Y');
    jQuery('select[name="ecopy"]').val('Y');
    jQuery('input[name="plan_code"]').val('plan_code');
    jQuery('input[name="face_amount"]').val('face_amount');
    jQuery('input[name="plan_level"]').val('plan_level');
    jQuery('input[name="family_package"]').val('family_package');
    jQuery('select[name="replacement_declaration"]').val('Y');
    jQuery('select[name="fna"]').val('Y');
    jQuery('input[name="health_details_q_1"]').attr('checked','checked');
    jQuery('input[name="health_details_q_2"]').attr('checked','checked');
    jQuery('input[name="health_details_q_3"]').attr('checked','checked');
    jQuery('input[name="health_details_q_4"]').attr('checked','checked');
    jQuery('input[name="health_details_q_5"]').attr('checked','checked');
    jQuery('input[name="agentCode"]').val('agentCode');
    jQuery('select[name="billingType"]').val('CUP');
    jQuery('input[name="authorizationCode"]').val('tokenized_card_number');
    jQuery('input[name="cardHolderName"]').val('cardHolderName');
    jQuery('input[name="cardholder_id_number"]').val('cardholder_id_number');
    jQuery('input[name="card_expiry_date"]').val('card_expiry_date');
    jQuery('input[name="initial_premium"]').val('initial_premium');
    jQuery('input[name="modal_premium_payment"]').val('modal_premium_payment');
    jQuery('select').change();
    
}