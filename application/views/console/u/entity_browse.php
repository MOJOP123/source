<?php
$udata = $this->session->userdata('user');


//Show logout button if user viewing their own entity:
if($entity['u_id']==$udata['u_id']){
    echo '<p style="float:right; margin-top:-75px;">
    <a href="/logout" class="btn btn-sm btn-primary"><i class="fas fa-power-off"></i><span> Logout</span></a>
</p>';
}
?>
<script>
    

$(document).ready(function() {
    //Detect any possible hashes that controll the menu?
    if(window.location.hash) {
        focus_hash(window.location.hash);
    } else {
        //Mark the first item as active:
        $('#topnav li').first().addClass('active');
        $('.tab-pane').first().addClass('active');
    }
});

function x_cover_set(x_id){
    //Set loader:
    $('#x_'+x_id+' .add-cover').addClass('hidden').after('<span class="badge badge-primary grey cover-load"><i class="fas fa-spinner fa-spin"></i></span>');

    //Add cover photo:
    $.post("/urls/set_cover", { x_id: x_id } , function(data) {

        if(data.status){

            $('.current-cover').remove(); //Remove Current cover icon
            $('#entity-box .profile-icon2').remove();
            $('#x_'+x_id+' .cover-load').html(data.message);

            //Tooltips:
            $('[data-toggle="tooltip"]').tooltip();

        } else {
            //We had an error:
            alert('Error: '+data.message);
            $('#x_'+x_id+' .cover-load').remove();
            $('#x_'+x_id+' .add-cover').removeClass('hidden')
        }

    });

}


function add_new_url(){

    if($('#add_url_input').val().length<1){
        //Empty field!
        alert('Error: Input field is empty. Paste a URL and then click "Add"');
        $('#add_url_input').focus();
        return false;
    }

    //Let's try adding:
    $('#add_url_input').prop('disabled', true); //Empty input
    $('#add_url_btn').attr('href','javascript:void(0);').html('<i class="fas fa-spinner fa-spin"></i>');


    $.post("/urls/add_url", {

        x_outbound_u_id: <?= $entity['u_id'] ?>,
        x_url: $('#add_url_input').val(),

    } , function(data) {

        //Release lock:
        $('#add_url_input').prop('disabled', false);
        $('#add_url_btn').attr('href','javascript:add_new_url();').html('ADD <i class="fas fa-link"></i>');
        $('.no-b-div-1').remove(); //This MIGHT be there if there was no URLs previously

        if(data.status){

            //Empty input to make it ready for next URL:
            $('#add_url_input').val('');

            //Add new object to list:
            add_to_list('list-urls', '.url-item', data.new_x);

            //Tooltips:
            $('[data-toggle="tooltip"]').tooltip();

        } else {
            //We had an error:
            alert('Error: '+data.message);
        }

    });

}

function x_delete(x_id){

    var r = confirm("Delete Reference?");
    if (r == false) {
        return false;
    }

    //Show loader to delete:
    $('#x_'+x_id).html('<img src="/img/round_load.gif" class="loader" /> Deleting... ');

    //Delete"
    $.post("/urls/delete_url", {

        x_id:x_id,

    } , function(data) {

        if(data.status){

            $('#x_'+x_id).html(data.message);

            //Remove the who bar:
            setTimeout(function() {
                $('#x_'+x_id).fadeOut();
            }, 377);

        } else {
            //We had an error:
            $('#x_'+x_id).html('<span style="color:#FF0000;"><i class="fas fa-exclamation-triangle"></i> Error: ' + data.message + '</span>');
        }

    });

}

function entity_load_more(page){

    //Show spinner:
    $('.load-more').html('<span><img src="/img/round_load.gif" class="loader" /></span>').hide().fadeIn();

    $.post("/entities/entity_load_more/<?= $inbound_u_id ?>/<?= $entities_per_page ?>/"+page, {} , function(data) {
        $('.load-more').remove();
        //Update UI to confirm with user:
        $('#list-entities').append(data);

        //Tooltips:
        $('[data-toggle="tooltip"]').tooltip();
    });

}
</script>


<?php

if(!$inbound_u_id){

    //Show help tip for Entities:
    echo '<div class="help_body below_h maxout" id="content_6776"></div>';

} else {

    //Show info box for this item:
    echo '<div id="entity-box" class="list-group maxout">';
    echo '<div id="u_'.$entity['u_id'].'" entity-id="'.$entity['u_id'].'" class="list-group-item">';

    //Right content:
    echo '<span class="pull-right">';
    echo echo_score($entity['u_e_score']);
    echo '<a class="badge badge-primary stnd-btn" onclick="load_modify('.$entity['u_id'].')" href="/entities/'.$entity['u_id'].'/modify"><i class="fas fa-cog"></i></a>';
    echo '</span>';

    //Regular section:
    echo echo_cover($entity,'profile-icon2');
    echo '<b id="u_title">'.$entity['u_full_name'].'</b>';
    echo ' <span class="obj-id">@'.$entity['u_id'].'</span>';

    //Do they have any social profiles in their link?
    echo echo_social_profiles($this->Db_model->x_social_fetch($entity['u_id']));

    //Check last engagement ONLY IF admin:
    if($udata['u_inbound_u_id']==1281){

        //Check last engagement:
        $last_eng = $this->Db_model->e_fetch(array(
            '(e_inbound_u_id='.$entity['u_id'].')' => null,
        ),1);

        if(count($last_eng)>0){
            echo ' &nbsp;<a href="/cockpit/browse/engagements?e_u_id='.$entity['u_id'].'" style="display: inline-block;" data-toggle="tooltip" data-placement="right" title="Last engaged '.echo_diff_time($last_eng[0]['e_timestamp']).' ago. Click to see all engagements"><i class="fas fa-exchange rotate45"></i> <b>'.echo_diff_time($last_eng[0]['e_timestamp']).' &raquo;</b></a>';
        }
    }

    echo '<div>'.$entity['u_bio'].'</div>';

    echo '</div>';
    echo '</div>';
}


//Start fetching related objects:
$admissions = $this->Db_model->ru_fetch(array(
    'ru_outbound_u_id' => $inbound_u_id,
), array(
    'ru.ru_id' => 'DESC',
), array('b'));

$b_team_member = $this->Db_model->ba_fetch(array(
    'ba.ba_outbound_u_id' => $inbound_u_id,
), array('b'));

$payments = $this->Db_model->t_fetch(array(
    't_inbound_u_id' => $inbound_u_id,
));


$show_tabs = array(
    'list' => (count($child_entities)>0 || $entity['u_id']==1326 ? '<li id="nav_list"><a href="#list"><i class="fas fa-at"></i> List</a></li>' : false ),
    'coach' => (count($b_team_member)>0 ? '<li id="nav_coach"><a href="#coach"><i class="fas fa-whistle"></i> Coach</a></li>' : false ),
    'student' => (count($admissions)>0 ? '<li id="nav_student"><a href="#student"><i class="fas fa-graduation-cap"></i> Student</a></li>' : false ),
    'payments' => (count($payments)>0 ? '<li id="nav_payments"><a href="#payments"><i class="fab fa-paypal"></i> Payments</a></li>' : false ),
    'referenced' => ( !in_array($entity['u_inbound_u_id'],array(0,1278,1326)) ? '<li id="nav_referenced"><a href="#referenced"><i class="fas fa-eye"></i> Referenced</a></li>' : false ),
);

?>



<ul id="topnav" class="nav nav-pills nav-pills-primary">
    <?php
    //FYI First List item would get the "active" CSS Class appended with JS in code above

    //Go through all tabs and see wassup:
    foreach($show_tabs as $key=>$tab_header){
        //Will echo list item IF exists:
        echo $tab_header;
    }
    ?>
</ul>


<div class="tab-content tab-space">

    <?php

    if($show_tabs['list']){

        echo '<div class="tab-pane" id="tablist">';

        echo '<div id="list-entities" class="list-group maxout grey-list">';
        foreach($child_entities as $u){
            echo echo_u($u);
        }

        if($entity['u__outbound_count']>count($child_entities)){
            echo_next_u(1, $entities_per_page, $entity['u__outbound_count']);
        }

        if($entity['u_id']==1326 && 0){
            ?>
            <script>

                function add_source_by_url(){
                    var input = $('#url_for_source').val();

                    if(!input.length){
                        alert('Hint: Enter a URL to create a new reference');
                        return false;
                    }

                    $.post("/entities/entitiy_create_from_url", { url:input }, function(data) {

                        if(data.status){

                            //Link has been added!

                            // Remove old parent first:
                            $( "#u_"+data.new_u_id).remove();

                            // Add parent:
                            $( "#list-entities").before(data.new_u);

                        } else {
                            //Show error:
                            alert('ERROR: '+data.message);
                        }

                    });

                }

                //Watch for Ctrl+Enter add
                $(document).ready(function() {
                    $('#url_for_source').keydown(function(event){
                        if((event.keyCode == 10 || event.keyCode == 13) && event.ctrlKey) {
                            add_source_by_url();
                            event.preventDefault();
                            return false;
                        }
                    });
                });

            </script>
            <div class="list-group-item list_input grey-input">
                <div class="input-group">
                    <div class="form-group is-empty"><input type="url" class="form-control" id="url_for_source" placeholder="Paste URL here..."></div>
                    <span class="input-group-addon">
                        <a class="badge badge-primary stnd-btn" href="javascript:add_source_by_url();">ADD <i class="fas fa-link"></i></a>
                    </span>
                </div>
            </div>
            <?php
        } elseif($entity['u_id']==1278 && 0){
            ?>
            <script>
                function add_person_by_email(){

                }
            </script>
            <div class="list-group-item list_input grey-input">
                <div class="input-group">
                    <div class="form-group is-empty"><input type="email" class="form-control" id="email_for_user" placeholder="newuser@email.com"></div>
                    <span class="input-group-addon">
                        <a class="badge badge-primary stnd-btn" onclick="add_person()" href="javascript:void(0);">ADD <i class="fas fa-user"></i></a>
                    </span>
                </div>
            </div>
            <?php
        }

        echo '</div>';
        echo '</div>';

    }

    if($show_tabs['coach']){

        echo '<div class="tab-pane" id="tabcoach">';
        echo '<div id="list-coach" class="list-group maxout grey-list">';
        foreach($b_team_member as $ba){
            echo_ba($ba);
        }
        echo '</div>';
        echo '</div>';

    }

    if($show_tabs['student']){

        echo '<div class="tab-pane" id="tabstudent">';
        echo '<div id="list-student" class="list-group maxout grey-list">';
        foreach($admissions as $ru){
            echo_ru($ru);
        }
        echo '</div>';
        echo '</div>';

    }

    if($show_tabs['payments']){

        echo '<div class="tab-pane" id="tabpayments">';
        echo '<div id="list-payments" class="list-group maxout grey-list">';
        foreach($payments as $t){
            echo_t($t);
        }
        echo '</div>';
        echo '</div>';

    }


    if($show_tabs['referenced']){

        echo '<div class="tab-pane" id="tabreferenced">'; //Tab content starts

        //Fetch all the URLs for this Entity:
        $urls = $this->Db_model->x_fetch(array(
            'x_status >' => 0,
            'x_outbound_u_id' => $entity['u_id'],
        ), array(), array(
            'x_id' => 'ASC'
        ));

        //URL List:
        echo '<div id="list-urls" class="list-group maxout grey-list">';

        if(count($urls)>0){
            foreach($urls as $x){
                echo echo_x($entity,$x);
            }
        } else {
            echo '<div class="list-group-item alert alert-info no-b-div-1" style="padding: 15px 10px;"><i class="fas fa-exclamation-triangle" style="margin:0 8px 0 2px;"></i> No Referenced added yet. Add your first Reference by pasting a URL below.</div>';
        }


        //Add new Reference:
        echo '<div class="list-group-item list_input grey-input">
                <div class="input-group">
                    <div class="form-group is-empty"><input type="url" class="form-control" id="add_url_input" placeholder="Paste URL here..."></div>
                    <span class="input-group-addon">
                        <a class="badge badge-primary stnd-btn" id="add_url_btn" href="javascript:add_new_url();">ADD <i class="fas fa-link"></i></a>
                    </span>
                </div>
            </div>';

        echo '</div>';


        echo '</div>'; //Tab content ends
    }

    ?>


    </div>

</div>
