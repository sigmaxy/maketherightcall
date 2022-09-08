jQuery(document).ready(function($){
    $(document).on('change','#same_as_owner',function(e) {
        toggle_customer_insured_same_as_owner();
	});
    toggle_customer_insured_same_as_owner();
    function toggle_customer_insured_same_as_owner(){
        if($('#same_as_owner').val()=='Y'){
            $('#edit-customer-insured').hide();
        }else{
            $('#edit-customer-insured').show();
        }
    }
    $(document).on('click','#calculate_premium',function(e) {
        calculate_premium();
	});
    function getAge(dateString) {
        var today = new Date();
        var birthDate = new Date(dateString);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    }
    function calculate_premium(){
        var plan_code = $('#plan_code').val();
        var plan_level = $('#plan_level').val();
        var smoker = $('#insured_smoker').val();
        var birthDate = $('#insured_birthDate').val();
        var gender = $('#insured_gender').val();
        if($('#same_as_owner').val()=='Y'){
            smoker = $('#owner_smoker').val();
            birthDate = $('#owner_birthDate').val();
            gender = $('#owner_gender').val();
        }
        var age = getAge(birthDate);
        var currency = $('#currency').val();
        var payment_mode = $('#paymentMode').val();
        var initial_premium,modal_premium_payment;
        var discount = 0;
        if(plan_code&&plan_level&&smoker&&birthDate&&currency){
            $.ajax({
                url: window.location.origin+drupalSettings.path.baseUrl+'chubb_life/data/ajax_get_premium/'+plan_code+'/'+plan_level+'/'+smoker+'/'+gender+'/'+age+'/'+currency,
                success: function (response) {
                    // console.log(response); 
                    if(response[0].status){
                        var premium = response[0].result;
                        var levy;
                        if(currency=='HKD'&& premium>=100000){
                            levy = 100;
                        }else if(currency=='USD'&& premium>=12820){
                            levy = 12.82;
                        }else{
                            levy = 0.001 * premium;
                        }
                        
                        if(payment_mode==12){
                            initial_premium = premium - premium*discount + levy;
                            modal_premium_payment = premium;
                        }else if(payment_mode==1){
                            initial_premium = (premium/12 - premium*discount/12 + levy)*2;
                            modal_premium_payment = premium*0.0872;
                        }
                        levy = (Math.round(levy * 100) / 100).toFixed(2);
                        initial_premium = (Math.round(initial_premium * 100) / 100).toFixed(2);
                        modal_premium_payment = (Math.round(modal_premium_payment * 100) / 100).toFixed(2);
                        $('#levy').val(levy);
                        $('#initial_premium').val(initial_premium);
                        $('#modal_premium_payment').val(modal_premium_payment);
                    }else{
                        alert(response[0].message);
                    }
                },
                error: function (request, status, error) {  
                    console.log('error'); 
                    console.log(request); 
                    console.log(status); 
                    console.log(error); 
                }
            });
        }else{
            alert('Please input plan code/plan level/smoker/brithdate/currency');
        }
    }
    $(document).on('change','#plan_code',function(e) {
        calculate_face_amount();
        fill_product_name();
	});
    $(document).on('change','#plan_level',function(e) {
        calculate_face_amount();
	});
    $(document).on('change','#currency',function(e) {
        calculate_face_amount();
	});
    function calculate_face_amount(){
        if (typeof drupalSettings.face_amount[$('#plan_code').val()][$('#plan_level').val()][$('#currency').val()] !== 'undefined') {
            $('#face_amount').val(drupalSettings.face_amount[$('#plan_code').val()][$('#plan_level').val()][$('#currency').val()]);
        }else{
            $('#face_amount').val('');
        }
    }
    function fill_product_name(){
        if (typeof drupalSettings.product_name[$('#plan_code').val()]['chinese_name'] !== 'undefined') {
            $('#product_name_chinese').val(drupalSettings.product_name[$('#plan_code').val()]['chinese_name']);
        }else{
            $('#product_name_chinese').val('');
        }
        if (typeof drupalSettings.product_name[$('#plan_code').val()]['english_name'] !== 'undefined') {
            $('#product_name_english').val(drupalSettings.product_name[$('#plan_code').val()]['english_name']);
        }else{
            $('#product_name_english').val('');
        }
    }
});
function addSlashes (element) {
    let ele = document.getElementById(element.id);
    ele = ele.value.split('/').join('');    // Remove slash (/) if mistakenly entered.
    if(ele.length < 4 && ele.length > 0){
        let finalVal = ele.match(/.{1,2}/g).join('/');
        document.getElementById(element.id).value = finalVal;
    }
}
function order_sample_data(){
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
    jQuery('select[name="marital"]').val('3');
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
    // jQuery('input[name="customer_payor_chineseName"]').val('customer_payor_chineseName');
    jQuery('select[name="customer_payor_identityType"]').val('I');
    jQuery('input[name="customer_payor_identityNumber"]').val('A321654(7)');
    jQuery('select[name="customer_payor_gender"]').val('F');
    jQuery('input[name="customer_payor_birthDate"]').val('2011-02-01');
    jQuery('select[name="currency"]').val('HKD');
    jQuery('select[name="paymentMode"]').val('12');
    jQuery('input[name="pep"]').val('pep');
    jQuery('select[name="another_person"]').val('Y');
    jQuery('select[name="ecopy"]').val('Y');
    jQuery('select[name="plan_code"]').val('RHC10');
    jQuery('select[name="plan_level"]').val('3');
    jQuery('input[name="promotion_code"]').val('promotion_code');
    jQuery('select[name="replacement_declaration"]').val('Y');
    jQuery('select[name="fna"]').val('Y');
    jQuery('input[name="health_details_q_1"]').attr('checked','checked');
    jQuery('input[name="health_details_q_2"]').attr('checked','checked');
    jQuery('input[name="health_details_q_3"]').attr('checked','checked');
    jQuery('input[name="health_details_q_4"]').attr('checked','checked');
    jQuery('input[name="health_details_q_5"]').attr('checked','checked');
    // jQuery('input[name="agentCode"]').val('agentCode');
    jQuery('select[name="billingType"]').val('CUP');
    jQuery('input[name="authorizationCode"]').val('tokenized_card_number');
    jQuery('input[name="cardHolderName"]').val('cardHolderName');
    jQuery('input[name="cardholder_id_number"]').val('cardholder_id_number');
    jQuery('input[name="card_expiry_date"]').val('12/2032');
    jQuery('#calculate_premium').click();
    jQuery('textarea[name="remarks"]').val('remarks');
    jQuery('select[name="dda_setup"]').val('18');
    jQuery('select').change();
}