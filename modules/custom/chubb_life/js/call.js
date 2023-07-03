jQuery(document).ready(function($){
    function calculate_hrc_premium(currency,payment_mode){
        var promotion_code = $('#promotion_code').val();
        var discount = 0;
        if (promotion_code) {
            if(promotion_code=='CD01'){
                discount = 0;
            }else if(drupalSettings.promotion_code_arr.includes(promotion_code)){
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
        let initial_premium,modal_premium_payment,modal_premium,discount_modal_premium,after_discount_modal_premium,levy;
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
                        let premium = response[0].result;
                        let mode_factor = 1;
                        let initial_factor = 1;
                        if(payment_mode=='01'){
                            mode_factor = 0.0872
                            initial_factor = 2;
                        }
                        console.log('plan_code is '+plan_code);
                        console.log('currency is '+currency+' payment_mode is '+payment_mode);
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
                        if(currency=='USD'){
                            if(payment_mode==12){
                                $('.rhc_product_premium_annual_usd').html(modal_premium_payment);
                                $('.rhc_product_premium_annual_usd_10y').html((parseFloat(modal_premium_payment)*10).toFixed(2));
                                if (plan_code=='RHC5') {
                                    $('.rhc_product_premium_annual_usd_110').html((parseFloat(modal_premium_payment)*10*1.02).toFixed(2));
                                }else{
                                    $('.rhc_product_premium_annual_usd_110').html((parseFloat(modal_premium_payment)*10*1.1).toFixed(2));
                                }
                                $('.rhc_product_premium_annual_usd_115').html((parseFloat(modal_premium_payment)*10*1.15).toFixed(2));
                                $('.rhc_product_initial_premium_annual_usd').html(initial_premium);
                            }else if(payment_mode==1){
                                $('.rhc_product_premium_monthly_usd').html(modal_premium_payment);
                                $('.rhc_product_premium_monthly_usd_ave').html((parseFloat(modal_premium_payment)/30).toFixed(2));
                                $('.rhc_product_premium_monthly_usd_10y').html((parseFloat(modal_premium_payment)*120).toFixed(2));
                                if (plan_code=='RHC5') {
                                    $('.rhc_product_premium_monthly_usd_110').html((parseFloat(modal_premium_payment)*120*1.02).toFixed(2));
                                }else{
                                    $('.rhc_product_premium_monthly_usd_110').html((parseFloat(modal_premium_payment)*120*1.1).toFixed(2));
                                }
                                $('.rhc_product_premium_monthly_usd_115').html((parseFloat(modal_premium_payment)*120*1.15).toFixed(2));
                                $('.rhc_product_initial_premium_monthly_usd').html(initial_premium);
                            }
                        }else if(currency=='HKD'){
                            if(payment_mode==12){
                                $('.rhc_product_premium_annual_hkd').html(modal_premium_payment);
                                $('.rhc_product_premium_annual_hkd_10y').html((parseFloat(modal_premium_payment)*10).toFixed(2));
                                if (plan_code=='RHC5') {
                                    $('.rhc_product_premium_annual_hkd_110').html((parseFloat(modal_premium_payment)*10*1.02).toFixed(2));
                                }else{
                                    $('.rhc_product_premium_annual_hkd_110').html((parseFloat(modal_premium_payment)*10*1.1).toFixed(2));
                                }
                                $('.rhc_product_premium_annual_hkd_115').html((parseFloat(modal_premium_payment)*10*1.15).toFixed(2));
                                $('.rhc_product_initial_premium_annual_hkd').html(initial_premium);
                            }else if(payment_mode==1){
                                $('.rhc_product_premium_monthly_hkd').html(modal_premium_payment);
                                $('.rhc_product_premium_monthly_hkd_ave').html((parseFloat(modal_premium_payment)/30).toFixed(2));
                                $('.rhc_product_premium_monthly_hkd_10y').html((parseFloat(modal_premium_payment)*120).toFixed(2));
                                if (plan_code=='RHC5') {
                                    $('.rhc_product_premium_monthly_hkd_110').html((parseFloat(modal_premium_payment)*120*1.02).toFixed(2));
                                }else{
                                    $('.rhc_product_premium_monthly_hkd_110').html((parseFloat(modal_premium_payment)*120*1.1).toFixed(2));
                                }
                                $('.rhc_product_premium_monthly_hkd_115').html((parseFloat(modal_premium_payment)*120*1.15).toFixed(2));
                                $('.rhc_product_initial_premium_monthly_hkd').html(initial_premium);
                            }
                        }
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
    }
    $(document).on('click','#calculate_rhc_premium',function(e) {
        calculate_rhc_face_amount();
        if($('#plan_code').val()=='RHC5'){
            calculate_hrc_premium('USD',12);
            calculate_hrc_premium('USD','01');
        }else{
            calculate_hrc_premium('USD',12);
            calculate_hrc_premium('USD','01');
            calculate_hrc_premium('HKD',12);
            calculate_hrc_premium('HKD','01');
        }
	});
});