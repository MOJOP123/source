


var logged_messenger = false;
var logged_website = false;
var step_count = 0;


$(document).ready(function () {
    goto_step(( referrer_in_id > 0 ? 1 : 2 ));

    $(document).keyup(function (e) {
        //Watch for action keys:
        if (e.keyCode == 13) {
            if(step_count==2){
                search_email();
            } else if(step_count==3){
                singin_check_password();
            } else if(step_count==4){
                add_account();
            }
        }
    });
});

function goto_step(this_step_count){

    //Update step count:
    step_count = this_step_count;

    $('.signup-steps').addClass('hidden');
    $('#step'+step_count).removeClass('hidden');

    $('#step'+step_count+' :input:visible:first').focus();
}

function confirm_sign_on_messenger(){
    var r = confirm("Ok, I will take you to Messenger now...");
    if (r == true) {
        sign_on_messenger();
    }
}

function sign_on_messenger(){

    if(!logged_messenger){
        js_ln_create(channel_choice_messenger);
        logged_messenger = true;
    }

    //Redirect to Messenger with a bit of delay to log the link above:
    setTimeout(function () {
        window.location = 'https://m.me/menchblogs';
    }, 377);

}



function vote_channel(en_chosen){

    //Cast Vote:
    js_ln_create({
        ln_type_player_id: 12106,
        ln_parent_player_id: 12105,
        ln_child_player_id: en_chosen,
    });

    $('.vote-results').html('Vote successfully casted. Choose a reading platform to continue.');

}



function select_channel(en_chosen){


    if(parseInt(en_chosen) == 6196 /* Mench on Messenger */ ){

        //Remove button:
        $('#step1button').html('<div style="font-size: 1.2em; padding-top:10px;"><i class="far fa-yin-yang fa-spin"></i> Loading Messenger...</div>');

        //Log link:
        sign_on_messenger();

    } else if (parseInt(en_chosen) == 12103) {

        //Log link:
        if(!logged_website){
            js_ln_create(channel_choice_website);
            logged_website = true;
        }

        goto_step(2);

    }
}

var email_is_searching = false;
function search_email(){

    if(email_is_searching){
        return false;
    }

    //Lock fields:
    email_is_searching = true;
    $('#email_check_next').html('<i class="far fa-yin-yang fa-spin"></i>');
    $('#input_email').prop('disabled', true).css('background-color','#f4f5f7');
    $('#password_errors').html('');
    $('#custom_message').html(''); //Remove previous errors, if any

    //Check email and validate:
    $.post("/play/singin_check_email", {

        input_email: $('#input_email').val(),
        referrer_in_id: referrer_in_id,

    }, function (data) {

        //Release field lock:
        email_is_searching = false;
        $('#email_check_next').html('<i class="fas fa-angle-right"></i>');
        $('#input_email').prop('disabled', false).css('background-color','#FFFFFF');

        if (data.status) {

            //Update player id IF existed already:
            $('#login_en_id').val(data.login_en_id);

            //Update email:
            $('#input_email').val(data.clean_input_email);
            $('.focus_email').html(data.clean_input_email);
            $('#email_errors').html('&nbsp;');

            //Go to next step:
            goto_step(( data.email_existed_already ? 3 /* To ask for password */ : 4 /* To check their email and create new account */ ));

        } else {
            //Show errors:
            $('#email_errors').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + data.message + '</b>').hide().fadeIn();
            $('#input_email').focus();
        }
    });

}

var account_is_adding = false;
function add_account(){

    if(account_is_adding){
        return false;
    }

    //Lock fields:
    account_is_adding = true;
    $('#add_acount_next').html('<i class="far fa-yin-yang fa-spin"></i>');
    $('#input_name, #new_password').prop('disabled', true).css('background-color','#f4f5f7');

    //Check email and validate:
    $.post("/play/sign_create_account", {
        input_email: $('#input_email').val(),
        input_name: $('#input_name').val(),
        new_password: $('#new_password').val(),
        referrer_url: referrer_url,
        referrer_in_id: referrer_in_id,
    }, function (data) {

        if (data.status) {

            //Release field lock:
            $('#add_acount_next').html('<i class="fas fa-check-circle"></i>');
            $('#new_account_errors').html('&nbsp;');

            setTimeout(function () {
                //Redirect to next step:
                window.location = data.login_url;
            }, 377);

        } else {


            //Release field lock:
            account_is_adding = false;
            $('#add_acount_next').html('Create Account <i class="fas fa-angle-right"></i>');
            $('#new_password, #input_name').prop('disabled', false).css('background-color','#FFFFFF');

            //Do we know which field to focus on?
            if(data.focus_input_field.length>0) {
                $('#' + data.focus_input_field).focus();
            }

            //Show errors:
            $('#new_account_errors').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + data.message + '</b>').hide().fadeIn();
        }

    });

}

var password_is_checking = false;
function singin_check_password(){

    if(password_is_checking){
        return false;
    }

    //Lock fields:
    password_is_checking = true;
    $('#password_check_next').html('<i class="far fa-yin-yang fa-spin"></i>');
    $('#input_password').prop('disabled', true).css('background-color','#f4f5f7');

    //Check email and validate:
    $.post("/play/singin_check_password", {
        login_en_id: $('#login_en_id').val(),
        input_password: $('#input_password').val(),
        referrer_url: referrer_url,
        referrer_in_id: referrer_in_id,
    }, function (data) {

        if (data.status) {

            //Release field lock:
            $('#password_check_next').html('<i class="fas fa-check-circle"></i>');
            $('#password_errors').html('');

            //Redirect
            window.location = data.login_url;

        } else {

            //Release field lock:
            password_is_checking = false;
            $('#password_check_next').html('<i class="fas fa-angle-right"></i>');
            $('#input_password').prop('disabled', false).css('background-color','#FFFFFF').focus();

            //Show errors:
            $('#password_errors').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + data.message + '</b>').hide().fadeIn();
        }

    });

}

function magicemail(){
    var r = confirm("I will email you a link to "+$('#input_email').val()+" so you can easily login.");
    if (r == true) {

        //Update UI:
        goto_step(5); //To check their email and create new account
        $('.magic_result').html('<i class="far fa-yin-yang fa-spin"></i> Emailing you a magic link...');

        //Check email and validate:
        $.post("/play/magicemail", {
            input_email: $('#input_email').val(),
            referrer_in_id: referrer_in_id,
        }, function (data) {
            if (data.status) {
                //All good, they can close window:
                $('.magic_result').html('<i class="fas fa-eye"></i> Check Your Email').hide().fadeIn();
            } else {
                //Show errors:
                $('.magic_result').html('<b style="color: #FF0000;"><i class="fas fa-exclamation-triangle"></i> Error: ' + data.message + '</b>').hide().fadeIn();
            }
        });
    }
}