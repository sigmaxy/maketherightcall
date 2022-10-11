jQuery(document).ready(function($){
    function calculate_hrc_premium(currency,payment_mode){
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
        var age = $('#plan_age').val();
        var gender = $('#plan_gender').val();
        var plan_level = $('#plan_level').val();
        var smoker = 'N';
        var initial_premium,modal_premium_payment;
        var url = window.location.origin+drupalSettings.path.baseUrl+'chubb_life/data/ajax_get_premium/'+plan_code+'/'+plan_level+'/'+smoker+'/'+gender+'/'+age+'/'+currency;
        // console.log('plan code is '+plan_code);
        // console.log('plan_level is '+plan_level);
        // console.log('smoker is '+smoker);
        // console.log('gender is '+gender);
        // console.log('age is '+age);
        // console.log('currency is '+currency);
        // console.log('url is '+url);
        // console.log('plan is '+plan_code);
        if(plan_code&&plan_level&&smoker&&age&&currency){
            $.ajax({
                url: url,
                success: function (response) {
                    // console.log(response); 
                    if(response[0].status){
                        var premium = response[0].result;
                        var levy;
                        if(currency=='HKD'&& premium>=100000){
                            levy = 100;
                        }else if(currency=='USD'&& premium>=12820){
                            levy = 12.82;
                        }else if(currency=='CNY'&& premium>=83330){
                            levy = 83.33;
                        }else{
                            levy = 0.001 * premium * (1 - discount);
                        }
                        if(payment_mode==12){
                            initial_premium = premium - premium*discount + levy;
                            modal_premium_payment = premium;
                        }else if(payment_mode==1){
                            initial_premium = (premium*0.0872 - premium*discount*0.0872 + levy)*2;
                            modal_premium_payment = premium*0.0872;
                            levy = levy * 0.0872;
                        }
                        levy = (Math.round(levy * 100) / 100).toFixed(2);
                        initial_premium = (Math.round(initial_premium * 100) / 100).toFixed(2);
                        modal_premium_payment = (Math.round(modal_premium_payment * 100) / 100).toFixed(2);
                        
                        if(currency=='USD'){
                            if(payment_mode==12){
                                $('.rhc_product_premium_annual_usd').html(modal_premium_payment);
                                $('.rhc_product_initial_premium_annual_usd').html(initial_premium);
                            }else if(payment_mode==1){
                                $('.rhc_product_premium_monthly_usd').html(modal_premium_payment);
                                $('.rhc_product_initial_premium_monthly_usd').html(initial_premium);
                            }
                        }else if(currency=='HKD'){
                            if(payment_mode==12){
                                $('.rhc_product_premium_annual_hkd').html(modal_premium_payment);
                                $('.rhc_product_initial_premium_annual_hkd').html(initial_premium);
                            }else if(payment_mode==1){
                                $('.rhc_product_premium_monthly_hkd').html(modal_premium_payment);
                                $('.rhc_product_initial_premium_monthly_hkd').html(initial_premium);
                            }
                        }
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
    function calculate_rhc_face_amount(){
        var plan_code = $('#plan_code').val();
        var plan_level = $('#plan_level').val();
        if (typeof drupalSettings.face_amount[plan_code][plan_level] !== 'undefined') {
            if (typeof drupalSettings.face_amount[plan_code][plan_level]['USD'] !== 'undefined') {
                $('.rhc_product_face_amount_usd').html(drupalSettings.face_amount[plan_code][plan_level]['USD']);
            }else{
                $('.rhc_product_face_amount_usd').html('');
            }
            if (typeof drupalSettings.face_amount[plan_code][plan_level]['HKD'] !== 'undefined') {
                $('.rhc_product_face_amount_hkd').html(drupalSettings.face_amount[plan_code][plan_level]['HKD']);
            }else{
                $('.rhc_product_face_amount_hkd').html('');
            }
        }else{
            $('.rhc_product_face_amount_usd').html('');
            $('.rhc_product_face_amount_hkd').html('');
        }
        console.log('test');
    }
    $(document).on('click','#calculate_rhc_premium',function(e) {
        calculate_rhc_face_amount();
        if($('#plan_code').val()=='RHC5'){
            calculate_hrc_premium('USD',12);
            calculate_hrc_premium('USD',1);
        }else{
            calculate_hrc_premium('USD',12);
            calculate_hrc_premium('USD',1);
            calculate_hrc_premium('HKD',12);
            calculate_hrc_premium('HKD',1);
        }
	});
});