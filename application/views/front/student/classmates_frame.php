<?php
$uadmission = $this->session->userdata('uadmission');

if(isset($uadmission) && count($uadmission)>0) {

    //Include header:
    $this->load->view('front/shared/student_nav' , array(
        'current' => 'classmates',
    ));

    //Fetch page instantly as we know who this is:
    ?>
    <script>
        $.post("/api_v1/load_classmates", { psid:0 }, function(data) {
            $( "#page_content").html(data);
        });
    </script>
    <?php

} else {

    ?>
    <script>
    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.com/en_US/messenger.Extensions.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'Messenger'));

    //the Messenger Extensions JS SDK is done loading:
    window.extAsyncInit = function() {

        //Get context:
        MessengerExtensions.getContext('1782431902047009',
            function success(thread_context){
                // success
                //User ID was successfully obtained.
                var psid = thread_context.psid;
                var signed_request = thread_context.signed_request;
                //Fetch Page:
                $.post( "/api_v1/load_classmates", { psid:psid }, function(data) {
                    //Update UI to confirm with user:
                    $( "#page_content").html(data);
                });
            },
            function error(err){

                //Give them instructions on how to access via mench.co:
                $("#page_content").html('<div class="alert alert-info" role="alert" style="line-height:110%;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><a href="https://mench.co/login" target="_blank"><u>Login</u></a> to access your Action Plan. Use <b><u>Forgot Password</u></b> if you never logged in before.</div>');

            }
        );
    };
    </script>
    <?php

}
?>

<div id="page_content"><div style="text-align:center;"><img src="/img/round_yellow_load.gif" class="loader" /></div></div>