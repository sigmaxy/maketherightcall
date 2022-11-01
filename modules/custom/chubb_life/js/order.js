jQuery(document).ready(function($){
    $(document).on('change','#same_as_owner',function(e) {
        toggle_customer_insured_same_as_owner();
        relationship_lock_value();
	});
    toggle_customer_insured_same_as_owner();
    relationship_lock_value();
    function toggle_customer_insured_same_as_owner(){
        if($('#same_as_owner').val()=='Y'){
            $('#edit-customer-insured').hide();
        }else{
            $('#edit-customer-insured').show();
        }
    }
    function relationship_lock_value(){
        if($('#same_as_owner').val()=='Y'){
            $("#relationship option").each(function(i){
                $(this).attr('disabled','disabled');
            });
            $("#relationship option[value=INS]").removeAttr('disabled');
            $("#relationship").val('INS');
            $("#relationship").change();
        }else{
            $("#relationship option").each(function(i){
                $(this).removeAttr('disabled');
            });
        }
    }
    $(document).on('blur','.order_birthdate',function(e) {
        var ToDate = new Date();
        if (new Date($(this).val()).getTime() >= ToDate.getTime()) {
            alert("The Date must be Smaller or Equal to today date");
            $(this).val('');
       }
	});
    function taxResidency_showmore_button(elements){
        if(elements.hasClass('show_more')){
            elements.removeClass('show_more');
            elements.prop("value", "Show Less");
        }else{
            elements.addClass('show_more');
            elements.prop("value", "Show More");
        }
    }
    $(document).on('click','#owner_taxResidency_showmore',function(e) {
        taxResidency_showmore_button($(this));
        owner_taxResidency_showmore();
	});
    owner_taxResidency_showmore();
    function owner_taxResidency_showmore() {
        if(!$('#owner_taxResidency_showmore').hasClass('show_more')){
            $('.form-item-taxresidency2').show()
            $('.form-item-taxresidencytin2').show()
            $('.form-item-taxresidency3').show()
            $('.form-item-taxresidencytin3').show()
        }else{
            $('.form-item-taxresidency2').hide()
            $('.form-item-taxresidencytin2').hide()
            $('.form-item-taxresidency3').hide()
            $('.form-item-taxresidencytin3').hide()
        }
    }
    $(document).on('click','#insured_taxResidency_showmore',function(e) {
        taxResidency_showmore_button($(this));
        insured_taxResidency_showmore();
	});
    insured_taxResidency_showmore();
    function insured_taxResidency_showmore() {
        if(!$('#insured_taxResidency_showmore').hasClass('show_more')){
            $('.form-item-customer-insured-taxresidency2').show()
            $('.form-item-customer-insured-taxresidencytin2').show()
            $('.form-item-customer-insured-taxresidency3').show()
            $('.form-item-customer-insured-taxresidencytin3').show()
        }else{
            $('.form-item-customer-insured-taxresidency2').hide()
            $('.form-item-customer-insured-taxresidencytin2').hide()
            $('.form-item-customer-insured-taxresidency3').hide()
            $('.form-item-customer-insured-taxresidencytin3').hide()
        }
    }
    $(document).on('change','#payor_same_as_owner',function(e) {
        if($('#payor_same_as_owner').val()=='Y'){
            $('#edit-customer-payor-surname').val($('#edit-surname').val());
            $('#edit-customer-payor-givenname').val($('#edit-givenname').val());
            $('#edit-customer-payor-identitytype').val($('#edit-identitytype').val());
            $('#edit-customer-payor-identitynumber').val($('#edit-identitynumber').val());
            $('#edit-customer-payor-gender').val($('#owner_gender').val());
            $('#edit-customer-payor-birthdate').val($('#owner_birthDate').val());
        }
	});
    $(document).on('click','#calculate_premium',function(e) {
        calculate_premium();
	});
    function getAge(dateString){
        let age;
        let today = new Date();
        let birthDate = new Date(dateString);
        let yyyyDiff = today.getFullYear() - birthDate.getFullYear();
        let mmDiff = today.getMonth() - birthDate.getMonth();
        let ddDiff = today.getDate() - birthDate.getDate();

        if (ddDiff < 0) {
            ddDiff = ddDiff + 30;
            mmDiff = mmDiff - 1;
        }
        if (mmDiff < 0) {
            mmDiff = mmDiff + 12;
            yyyyDiff = yyyyDiff - 1;
        }
        if (mmDiff > 6 || (mmDiff === 6 && ddDiff > 0)) {
            yyyyDiff += 1;
        }
        if (age < 0) {
            yyyyDiff = 0;
        }
	console.log('In getAge calculation, the age is '+yyyyDiff+' years '+mmDiff+' month '+ddDiff+' days');
        age = yyyyDiff;
        return age;
    }
    function calculate_premium(){
        var promotion_code = $('#promotion_code').val();
        var discount = 0;
        if (promotion_code) {
            if(drupalSettings.promotion_code_arr.includes(promotion_code)){
                discount = 0.15;
            }else{
                alert('Invalid Promotion Code');
            }
            
        }
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
        let initial_premium,modal_premium_payment,modal_premium,discount_modal_premium,after_discount_modal_premium,levy;
        var url = window.location.origin+drupalSettings.path.baseUrl+'chubb_life/data/ajax_get_premium/'+plan_code+'/'+plan_level+'/'+smoker+'/'+gender+'/'+age+'/'+currency;
        console.log('plan code is '+plan_code);
        console.log('plan_level is '+plan_level);
        console.log('smoker is '+smoker);
        console.log('gender is '+gender);
        console.log('age is '+age);
        console.log('currency is '+currency);
        console.log('url is '+url);
        console.log('payment_mode is '+payment_mode);
        if(!plan_code){
            alert('Please input plan code');
        }else if(!plan_level){
            alert('Please input plan level');
        }else if(!smoker){
            alert('Please smoker');
        }else if(!birthDate){
            alert('Please input brithdate');
        }else if(!currency){
            alert('Please input currency');
        }else{
            $.ajax({
                url: url,
                success: function (response) {
                    // console.log(response); 
                    if(response[0].status){
                        let premium = response[0].result;
                        let mode_factor = 1;
                        let initial_factor = 1;
                        if(payment_mode=='01'){
                            mode_factor = 0.0872
                            initial_factor = 2;
                        }
                        console.log('premium is '+premium);
                        modal_premium = (premium * mode_factor).toFixed(2);
                        console.log('modal_premium is '+modal_premium);
                        discount_modal_premium = (discount * modal_premium).toFixed(2);
                        console.log('discount_modal_premium is '+discount_modal_premium);
                        after_discount_modal_premium = (modal_premium - discount_modal_premium).toFixed(2);
                        console.log('after_discount_modal_premium is '+after_discount_modal_premium);
                        // console.log('after_discount_modal_premium is '+after_discount_modal_premium*0.001);
                        if(currency=='HKD'&& premium>=100000){
                            levy = 100;
                        }else if(currency=='USD'&& premium>=12820){
                            levy = 12.82;
                        }else if(currency=='CNY'&& premium>=83330){
                            levy = 83.33;
                        }else{
                            levy = (after_discount_modal_premium*0.001).toFixed(2);
                        }
                        console.log('levy is '+levy);
                        // console.log(parseFloat(after_discount_modal_premium)+parseFloat(levy));
                        console.log('initial_factor is '+initial_factor);
                        // let test = ((parseFloat(after_discount_modal_premium) + parseFloat(levy)) * initial_factor).toFixed(2);
                        // console.log(test);
                        initial_premium = ((parseFloat(after_discount_modal_premium) + parseFloat(levy)) * initial_factor).toFixed(2);
                        console.log('initial_premium is '+initial_premium);
                        modal_premium_payment = modal_premium
                        console.log('modal_premium_payment is '+modal_premium_payment);
                        
                        
                        
                        // console.log('modal_premium is '+modal_premium);
                        // console.log('discount_modal_premium is '+discount_modal_premium);
                        // console.log('after_discount_modal_premium is '+after_discount_modal_premium);
                        // console.log('initial_premium is '+initial_premium);
                        // console.log('modal_premium_payment is '+modal_premium_payment);



                        // if(currency=='HKD'&& premium>=100000){
                        //     levy = 100;
                        // }else if(currency=='USD'&& premium>=12820){
                        //     levy = 12.82;
                        // }else if(currency=='CNY'&& premium>=83330){
                        //     levy = 83.33;
                        // }else{
                        //     levy = 0.001 * premium * (1-discount);
                        // }
                        // if(payment_mode==12){
                        //     initial_premium = premium - premium*discount + levy;
                        //     modal_premium_payment = premium;
                        // }else if(payment_mode=='01'){
                        //     levy = levy * 0.0872;
                        //     initial_premium = (premium*0.0872 - premium*discount*0.0872 + levy)*2;
                            
                        //     modal_premium_payment = premium*0.0872;
                            
                        // }
                        // console.log('levy is '+levy);
                        // console.log('initial_premium is '+initial_premium);
                        // levy = (Math.round(levy * 100) / 100).toFixed(2);
                        // initial_premium = (Math.round(initial_premium * 100) / 100).toFixed(2);
                        // modal_premium_payment = (Math.round(modal_premium_payment * 100) / 100).toFixed(2);
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
        }
    }
    $(document).on('change','.clear_calculate',function(e) {
        $('#levy').val('');
        $('#initial_premium').val('');
        $('#modal_premium_payment').val('');
	});

    $(document).on('change','#plan_code',function(e) {
        calculate_face_amount();
        fill_product_name();
        questionair_display_toggle();
	});
    $(document).on('change','#plan_level',function(e) {
        calculate_face_amount();
	});
    $(document).on('change','#currency',function(e) {
        calculate_face_amount();
	});
    function calculate_face_amount(){
        var plan_code = $('#plan_code').val();
        var plan_level = $('#plan_level').val();
        var currency = $('#currency').val();
        if (typeof drupalSettings.face_amount[plan_code][plan_level] !== 'undefined') {
            if (typeof drupalSettings.face_amount[plan_code][plan_level][currency] !== 'undefined') {
                $('#face_amount').val(drupalSettings.face_amount[plan_code][plan_level][currency]);
            }else{
                $('#face_amount').val('');
            }
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
    questionair_display_toggle();
    function questionair_display_toggle(){
        if ($('#plan_code').val()=='MCE') {
            $('#edit-health-details').show();
        }else{
            $('#edit-health-details').hide();
        }
    }
    $(document).on('change','#customer_owner_solicitation',function(e) {
        owner_solicitation_display_toggle();
	});
    owner_solicitation_display_toggle();
    function owner_solicitation_display_toggle(){
        if ($('#customer_owner_solicitation').val()=='N') {
            $('.form-item-opt-out-reason').show();
        }else{
            $('.form-item-opt-out-reason').hide();
        }
    }
    $(document).on('change','#customer_insured_solicitation',function(e) {
        insured_solicitation_display_toggle();
	});
    insured_solicitation_display_toggle();
    function insured_solicitation_display_toggle(){
        if ($('#customer_insured_solicitation').val()=='N') {
            $('.form-item-customer-insured-opt-out-reason').show();
        }else{
            $('.form-item-customer-insured-opt-out-reason').hide();
        }
    }

    $(document).on('change','#mailing_same_as_residence',function(e) {
        mailing_same_as_residence_display_toggle();
	});
    mailing_same_as_residence_display_toggle();
    function mailing_same_as_residence_display_toggle(){
        if ($('#mailing_same_as_residence').val()=='N') {
            $('.form-item-mailing-address1').show();
            $('.form-item-mailing-address2').show();
            $('.form-item-mailing-address3').show();
            $('.form-item-mailing-city').show();
            $('.form-item-mailing-country').show();
        }else{
            $('.form-item-mailing-address1').hide();
            $('.form-item-mailing-address2').hide();
            $('.form-item-mailing-address3').hide();
            $('.form-item-mailing-city').hide();
            $('.form-item-mailing-country').hide();
        }
    }
    $(document).on('change','#customer_insured_mailing_same_as_residence',function(e) {
        insured_mailing_same_as_residence_display_toggle();
	});
    insured_mailing_same_as_residence_display_toggle();
    function insured_mailing_same_as_residence_display_toggle(){
        if ($('#customer_insured_mailing_same_as_residence').val()=='N') {
            $('.form-item-customer-insured-mailing-address1').show();
            $('.form-item-customer-insured-mailing-address2').show();
            $('.form-item-customer-insured-mailing-address3').show();
            $('.form-item-customer-insured-mailing-city').show();
            $('.form-item-customer-insured-mailing-country').show();
        }else{
            $('.form-item-customer-insured-mailing-address1').hide();
            $('.form-item-customer-insured-mailing-address2').hide();
            $('.form-item-customer-insured-mailing-address3').hide();
            $('.form-item-customer-insured-mailing-city').hide();
            $('.form-item-customer-insured-mailing-country').hide();
        }
    }
    $("#edit-remarks").on("keydown change", function(e){
        if (e.keyCode == 8)
            return;
        var x = $(this).val();
        if (x.match(/[\u3400-\u9FBF]/) && x.length >= 85) {
            e.preventDefault();
            $(this).val(x.substring(0,85));
        } else if (x.length >= 170){
            e.preventDefault();
            $(this).val(x.substring(0,170));
        }
    });

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
    jQuery('input[name="identityNumber"]').val('A1234567');
    jQuery('select[name="issueCountry"]').val('HK');
    jQuery('select[name="gender"]').val('F');
    jQuery('select[name="isPermanentHkid"]').val('Y');
    jQuery('input[name="birthDate"]').val('1990-12-31');
    jQuery('select[name="marital"]').val('3');
    jQuery('select[name="nationality"]').val('HK');
    jQuery('select[name="taxResidency1"]').val('CP');
    jQuery('input[name="taxResidencyTin1"]').val('A1234568');
    jQuery('select[name="taxResidency2"]').val('CP');
    jQuery('input[name="taxResidencyTin2"]').val('A1234569');
    jQuery('select[name="taxResidency3"]').val('CP');
    jQuery('input[name="taxResidencyTin3"]').val('A123456A');
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
    jQuery('input[name="customer_insured_identityNumber"]').val('S2323234');
    jQuery('select[name="customer_insured_issueCountry"]').val('HK');
    jQuery('select[name="customer_insured_gender"]').val('M');
    jQuery('select[name="customer_insured_isPermanentHkid"]').val('Y');
    jQuery('input[name="customer_insured_birthDate"]').val('1991-10-11');
    jQuery('select[name="customer_insured_marital"]').val('2');
    jQuery('select[name="customer_insured_nationality"]').val('HK');
    jQuery('select[name="customer_insured_taxResidency1"]').val('MC');
    jQuery('input[name="customer_insured_taxResidencyTin1"]').val('B1234431');
    jQuery('select[name="customer_insured_taxResidency2"]').val('MC');
    jQuery('input[name="customer_insured_taxResidencyTin2"]').val('B1234432');
    jQuery('select[name="customer_insured_taxResidency3"]').val('MC');
    jQuery('input[name="customer_insured_taxResidencyTin3"]').val('B1234433');
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
    jQuery('input[name="customer_payor_identityNumber"]').val('A3216547');
    jQuery('select[name="customer_payor_gender"]').val('F');
    jQuery('input[name="customer_payor_birthDate"]').val('2011-02-01');
    jQuery('select[name="currency"]').val('HKD');
    jQuery('select[name="paymentMode"]').val('12');
    jQuery('input[name="pep"]').val('pep');
    jQuery('select[name="another_person"]').val('Y');
    jQuery('select[name="ecopy"]').val('Y');
    jQuery('select[name="plan_code"]').val('RHC10');
    jQuery('select[name="plan_level"]').val('3');
    jQuery('input[name="promotion_code"]').val('CC17');
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
