jQuery(document).ready(function($){
    function calculate_rhc_premium(currency,payment_mode){
        var discount = 0;
        var plan_code = $('#plan_code').val();
        var age = $('#plan_age').val();
        var gender = $('#plan_gender').val();
        var plan_level = $('#plan_level').val();
        var smoker = 'N';
        let initial_premium,modal_premium_payment,modal_premium,discount_modal_premium,after_discount_modal_premium,levy;
        var url = window.location.origin+drupalSettings.path.baseUrl+'chubb_life/data/ajax_get_premium/'+plan_code+'/'+plan_level+'/'+smoker+'/'+gender+'/'+age+'/'+currency;
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
            calculate_rhc_premium('USD',12);
            calculate_rhc_premium('USD','01');
        }else{
            calculate_rhc_premium('USD',12);
            calculate_rhc_premium('USD','01');
            calculate_rhc_premium('HKD',12);
            calculate_rhc_premium('HKD','01');
        }
	});


    function calculate_rst_premium(currency,payment_mode){
        var discount = 0;
        var plan_code = $('#rst_plan_code').val();
        var age = $('#rst_plan_age').val();
        var gender = $('#rst_plan_gender').val();
        var plan_level = $('#rst_plan_level').val();
        var smoker = 'N';
        let initial_premium,modal_premium_payment,modal_premium,discount_modal_premium,after_discount_modal_premium,levy;
        var url = window.location.origin+drupalSettings.path.baseUrl+'chubb_life/data/ajax_get_premium/'+plan_code+'/'+plan_level+'/'+smoker+'/'+gender+'/'+age+'/'+currency;
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
                        if(currency=='HKD'){
                            if(payment_mode==12){
                                $('.rst_product_premium_annual_hkd').html(modal_premium_payment);
                                $('.rst_product_initial_premium_annual_hkd').html(initial_premium);
                                if (plan_code=='RST08') {
                                    $('.rst_product_year').html(8);
                                    $('.rst_product_premium_annual_hkd_10y').html((parseFloat(modal_premium_payment)*8).toFixed(2));
                                    $('.rst_product_premium_annual_hkd_100').html((parseFloat(modal_premium_payment)*8*1).toFixed(2));
                                    $('.rst_product_premium_annual_hkd_103').html('');
                                }else{
                                    $('.rst_product_year').html(10);
                                    $('.rst_product_premium_annual_hkd_10y').html((parseFloat(modal_premium_payment)*10).toFixed(2));
                                    $('.rst_product_premium_annual_hkd_100').html('');
                                    $('.rst_product_premium_annual_hkd_103').html((parseFloat(modal_premium_payment)*10*1.03).toFixed(2));
                                }
                                
                                
                            }else if(payment_mode==1){
                                $('.rst_product_premium_monthly_hkd').html(modal_premium_payment);
                                $('.rst_product_initial_premium_monthly_hkd').html(initial_premium);
                                $('.rst_product_premium_monthly_hkd_ave').html((parseFloat(modal_premium_payment)/30).toFixed(2));
                                if (plan_code=='RST08') {
                                    $('.rst_product_year').html(8);
                                    $('.rst_product_premium_monthly_hkd_10y').html((parseFloat(modal_premium_payment)*96).toFixed(2));
                                    $('.rst_product_premium_monthly_hkd_100').html((parseFloat(modal_premium_payment)*96*1).toFixed(2));
                                    $('.rst_product_premium_monthly_hkd_103').html('');
                                }else{
                                    $('.rst_product_year').html(10);
                                    $('.rst_product_premium_monthly_hkd_10y').html((parseFloat(modal_premium_payment)*120).toFixed(2));
                                    $('.rst_product_premium_monthly_hkd_100').html('');
                                    $('.rst_product_premium_monthly_hkd_103').html((parseFloat(modal_premium_payment)*120*1.03).toFixed(2));
                                }
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
    function calculate_rst_face_amount(){
        var plan_code = $('#rst_plan_code').val();
        var plan_level = $('#rst_plan_level').val();
        if (typeof drupalSettings.face_amount[plan_code][plan_level] !== 'undefined') {
            if (typeof drupalSettings.face_amount[plan_code][plan_level]['HKD'] !== 'undefined') {
                $('.rst_product_face_amount_hkd').html(drupalSettings.face_amount[plan_code][plan_level]['HKD']);
            }else{
                $('.rst_product_face_amount_hkd').html('');
            }
        }else{
            $('.rst_product_face_amount_usd').html('');
            $('.rst_product_face_amount_hkd').html('');
        }
    }
    $(document).on('click','#calculate_rst_premium',function(e) {
        calculate_rst_face_amount();
        if($('#rst_plan_code').val()=='RST08'){
            calculate_rst_premium('HKD',12);
            calculate_rst_premium('HKD','01');
        }else{
            calculate_rst_premium('HKD',12);
            calculate_rst_premium('HKD','01');
        }
	});

    function calculate_rpa_premium(currency,payment_mode){
        var discount = 0;
        var plan_code = $('#rpa_plan_code').val();
        var age = $('#rpa_plan_age').val();
        var gender = $('#rpa_plan_gender').val();
        var plan_level = $('#rpa_plan_level').val();
        var smoker = 'N';
        let initial_premium,modal_premium_payment,modal_premium,discount_modal_premium,after_discount_modal_premium,levy;
        var url = window.location.origin+drupalSettings.path.baseUrl+'chubb_life/data/ajax_get_premium/'+plan_code+'/'+plan_level+'/'+smoker+'/'+gender+'/'+age+'/'+currency;
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
                        

                        if(payment_mode==12){
                            $('.rpa_product_premium_annual_hkd').html('');
                            $('.rpa_product_initial_premium_annual_hkd').html('');
                            $('.rpa_product_premium_annual_hkd_10y').html('');
                            $('.rpa_product_premium_annual_hkd_100').html('');
                            $('.rpa_product_premium_annual_hkd_103').html('');

                            $('.rpa_product_premium_annual_usd').html('');
                            $('.rpa_product_initial_premium_annual_usd').html('');
                            $('.rpa_product_premium_annual_usd_10y').html('');
                            $('.rpa_product_premium_annual_usd_100').html('');
                            $('.rpa_product_premium_annual_usd_103').html('');

                            $('.rpa_product_premium_annual_'+currency.toLowerCase()).html(modal_premium_payment);
                            $('.rpa_product_initial_premium_annual_'+currency.toLowerCase()).html(initial_premium);
                            if (plan_code=='DG08U'||plan_code=='DG08H') {
                                console.log('sigma currency '+currency.toLowerCase());
                                $('.rpa_product_year').html(8);
                                $('.rpa_product_premium_annual_'+currency.toLowerCase()+'_10y').html((parseFloat(modal_premium_payment)*8).toFixed(2));
                                $('.rpa_product_premium_annual_'+currency.toLowerCase()+'_100').html((parseFloat(modal_premium_payment)*8*1).toFixed(2));
                                $('.rpa_product_premium_annual_'+currency.toLowerCase()+'_103').html('');
                            }else{
                                $('.rpa_product_year').html(10);
                                $('.rpa_product_premium_annual_'+currency.toLowerCase()+'_10y').html((parseFloat(modal_premium_payment)*10).toFixed(2));
                                $('.rpa_product_premium_annual_'+currency.toLowerCase()+'_100').html('');
                                $('.rpa_product_premium_annual_'+currency.toLowerCase()+'_103').html((parseFloat(modal_premium_payment)*10*1.03).toFixed(2));
                            }
                            
                            
                        }else if(payment_mode==1){
                            $('.rpa_product_premium_monthly_hkd').html('');
                            $('.rpa_product_initial_premium_monthly_hkd').html('');
                            $('.rpa_product_premium_monthly_hkd_10y').html('');
                            $('.rpa_product_premium_monthly_hkd_100').html('');
                            $('.rpa_product_premium_monthly_hkd_103').html('');

                            $('.rpa_product_premium_monthly_usd').html('');
                            $('.rpa_product_initial_premium_monthly_usd').html('');
                            $('.rpa_product_premium_monthly_usd_10y').html('');
                            $('.rpa_product_premium_monthly_usd_100').html('');
                            $('.rpa_product_premium_monthly_usd_103').html('');

                            $('.rpa_product_premium_monthly_'+currency.toLowerCase()).html(modal_premium_payment);
                            $('.rpa_product_initial_premium_monthly_'+currency.toLowerCase()).html(initial_premium);
                            // $('.rpa_product_premium_monthly_'+currency.toLowerCase()+'_ave').html((parseFloat(modal_premium_payment)/30).toFixed(2));
                            if (plan_code=='DG08U'||plan_code=='DG08H') {
                                $('.rpa_product_year').html(8);
                                $('.rpa_product_premium_monthly_'+currency.toLowerCase()+'_10y').html((parseFloat(modal_premium_payment)*96).toFixed(2));
                                $('.rpa_product_premium_monthly_'+currency.toLowerCase()+'_100').html((parseFloat(modal_premium_payment)*96*1).toFixed(2));
                                $('.rpa_product_premium_monthly_'+currency.toLowerCase()+'_103').html('');
                            }else{
                                $('.rpa_product_year').html(10);
                                $('.rpa_product_premium_monthly_'+currency.toLowerCase()+'_10y').html((parseFloat(modal_premium_payment)*120).toFixed(2));
                                $('.rpa_product_premium_monthly_'+currency.toLowerCase()+'_100').html('');
                                $('.rpa_product_premium_monthly_'+currency.toLowerCase()+'_103').html((parseFloat(modal_premium_payment)*120*1.03).toFixed(2));
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
    function calculate_rpa_face_amount(){
        var plan_code = $('#rpa_plan_code').val();
        var plan_level = $('#rpa_plan_level').val();
        $('.rpa_product_face_amount_usd').html('');
        $('.rpa_product_face_amount_hkd').html('');
        if (typeof drupalSettings.face_amount[plan_code][plan_level] !== 'undefined') {
            if (typeof drupalSettings.face_amount[plan_code][plan_level]['HKD'] !== 'undefined') {
                $('.rpa_product_face_amount_hkd').html(drupalSettings.face_amount[plan_code][plan_level]['HKD']);
            }
            if (typeof drupalSettings.face_amount[plan_code][plan_level]['USD'] !== 'undefined') {
                $('.rpa_product_face_amount_usd').html(drupalSettings.face_amount[plan_code][plan_level]['USD']);
            }
        }
    }
    $(document).on('click','#calculate_rpa_premium',function(e) {
        calculate_rpa_face_amount();
        if($('#rpa_plan_code').val()=='DG08U'||$('#rpa_plan_code').val()=='DG10U'){
            calculate_rpa_premium('USD',12);
            calculate_rpa_premium('USD','01');
        }else{
            calculate_rpa_premium('HKD',12);
            calculate_rpa_premium('HKD','01');
        }
    });
});