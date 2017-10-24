$(document).ready(function(){
    $('.comfirm_before_submission').click(function(){
        if(!confirm('Click OK to continue with this action.')){
            event.preventDefault();
        }
    });
    $('#show_back').click(function(){
        $('#inst_id').html($('#id_back').html());
    });
    $('#show_front').click(function(){
        $('#inst_id').html($('#id_front').html());
    });
    $('#update_form_div,#pp_update_div').hide();
    $('#info_updater').click(function(){
        $('#profile_display_div').html($('#update_form_div').html());
    });
    $('#password_changer').click(function(){
        $('#profile_display_div').html($('#password_change_form_div').html());
    });
    $('#pp_update_but').click(function(){
        $('#pp_label').html($('#pp_update_div').html());
    });
    
    var sr = $('#search_results_container');
    if(sr.height() < 10){
        sr.hide();
    }else{
        sr.show();
    }
    $('#search_term').keyup(function(){
        var im = $('#index_mid');
        if(sr.height() < 10){
            sr.hide();
        }else{
            sr.show();
        }
        if($(this).val().length > 0){
           im.css('margin','2%'); 
           sr.css('overflow-y','scroll');
        }else{
           im.css('margin','12%');
           sr.css('overflow-y','hidden');
        }
        sr.load('sources/surveyor_searcher.php',{'search':$(this).val()});
    });
    $('#svy_srch_term').keyup(function(){
        var sr = $('#surveyors_container');
        sr.load('surveyor_searcher.php',{'srch_term':$(this).val()});
    });
    $('#srched').keyup(function(){
        var sr = $('#surveyors_container');
        sr.load('surveyor_searcher.php',{'admin_srch':$(this).val()});
    });
     $('.menu_from_left').hide();
     if(window.innerWidth < 1120){
            $('.ul_div > ul > li:not(.dropdown)').hide();
            $('.ul_div > ul > li.dropdown').show();
        }else{
            $('.ul_div > ul > li:not(.dropdown)').show();
            $('.ul_div > ul > li.dropdown').hide();
            $('.menu_from_left').hide();
        }
    if($(window).innerWidth() < 500){
        $('.invisible_on_sm').hide();
    }
    $(window).resize(function(){
        if($(window).innerWidth() < 500){
            $('.invisible_on_sm').hide();
        }else{
            $('.invisible_on_sm').show();
        }
        if(window.innerWidth < 1120){
            $('.ul_div > ul > li:not(.dropdown)').hide();
            $('.ul_div > ul > li.dropdown').show();
        }else{
            $('.ul_div > ul > li:not(.dropdown)').show();
            $('.ul_div > ul > li.dropdown').hide();
            $('.menu_from_left').hide();
        }
    });
    $('.ul_div > ul > li.dropdown').click(function(){
        $('.menu_from_left').toggle('slide','left');
    });
    var nv = $('.top_nav');
    var ms = $('menu_side');
    $(window).scroll(function(){
        if($(this).scrollTop() > 50){
            nv.addClass('navbar-fixed-top');
            nv.addClass('shadowed_bottom');
            $('h1:not(.prompter)').hide();
        }else{
            nv.removeClass('navbar-fixed-top');
            nv.removeClass('shadowed_bottom');
            $('h1').show();
        }
    });
    var admin_pc = $('#admin_pass_changer');
    var reg_div = $('#reg-div,#sec_admin_dut');
    var footer = $('#footer');
    admin_pc.hide();
    $('.chng_pass').click(function(){
        reg_div.html(admin_pc.html());
        $('.nav li:eq(1)').removeClass('active');
        $('.nav li:eq(2)').addClass('active');
    });
    var admin_svy_srch = $('#svy_srch_term');
    var footer = $('#footer');
    admin_svy_srch.keyup(function(){
        if($(this).val().length > 0){
            footer.addClass('navbar-fixed-bottom');
        }
    });
    var surveyors_container = $('#surveyors_container');
    var sc_height = surveyors_container.innerHeight();
    var space_left = 650 - sc_height;
    var margin_bot = space_left+"px";
    surveyors_container.css('margin-bottom',margin_bot);    
    $('#srched,#svy_srch_term').keyup(function(){
        var sc_height = surveyors_container.innerHeight();
        var space_left = 600 - sc_height;
        var margin_bot = space_left+"px";
        surveyors_container.css('margin-bottom',margin_bot);  
    });
});