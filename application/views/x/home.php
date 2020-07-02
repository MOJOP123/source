
<script src="/application/views/x/home.js?v=<?= config_var(11060) ?>" type="text/javascript"></script>

<div class="container">
    <?php

    $session_e = superpower_assigned();
    $e___11035 = $this->config->item('e___11035'); //MENCH NAVIGATION
    $member_xy_ids = array();


    if($session_e){

        //MY DISCOVERIES
        $member_x = $this->X_model->fetch(array(
            'x__member' => $session_e['e__id'],
            'x__type IN (' . join(',', $this->config->item('n___12969')) . ')' => null, //MY DISCOVERIES
            'i__status IN (' . join(',', $this->config->item('n___7355')) . ')' => null, //PUBLIC
            'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
        ), array('x__left'), 0, 0, array('x__sort' => 'ASC'));

        echo ( count($member_x) > 1 ? '<script> $(document).ready(function () {x_sort_load(6132)}); </script>' : '<style> .x-sorter {display:none !important;} </style>' ); //Need 2 or more to sort

        if(count($member_x)){

            echo '<div class="headline" style="margin-top: 34px;"><span class="icon-block">'.$e___11035[12969]['m_icon'].'</span>'.$e___11035[12969]['m_name'].'</div>';


            echo '<div class="clear-xy-list">';
            echo '<div id="i_covers" class="cover-list">';
            foreach($member_x as $x) {
                array_push($member_xy_ids, $x['i__id']);
                echo view_i_cover($x, true);
            }
            echo '</div>';
            echo '</div>';

            echo '<div class="doclear">&nbsp;</div>';


            //DISCOVER DELETE ALL (ACCESSIBLE VIA MAIN MENU)
            echo '<div class="clear-xy-list hidden margin-top-down">';
            echo '<div class="alert alert-danger" role="alert">';
            echo '<span class="icon-block"><i class="fas fa-exclamation-circle discover"></i></span><b class="discover montserrat">DELETE ALL DISCOVERIES?</b>';
            echo '<br /><span class="icon-block">&nbsp;</span>Action cannot be undone.';
            echo '</div>';
            echo '<p style="margin-top:20px;"><a href="javascript:void(0);" onclick="x_clear_all()" class="btn btn-x"><i class="far fa-trash-alt"></i> DELETE ALL</a> or <a href="javascript:void(0)" onclick="$(\'.clear-xy-list\').toggleClass(\'hidden\')" style="text-decoration: underline;">Cancel</a></p>';
            echo '</div>';

            echo '<div class="doclear">&nbsp;</div>';


        }



        //Saved
        $member_saved = $this->X_model->fetch(array(
            'x__up' => $session_e['e__id'],
            'x__type' => 12896, //SAVED
            'i__status IN (' . join(',', $this->config->item('n___7355')) . ')' => null, //PUBLIC
            'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
        ), array('x__right'), 0, 0, array('x__id' => 'DESC'));

        if(count($member_saved)){

            echo '<div class="headline" style="margin-top: 34px;"><span class="icon-block">'.$e___11035[12896]['m_icon'].'</span>'.$e___11035[12896]['m_name'].'</div>';

            echo '<div class="list-group no-side-padding">';
            foreach($member_saved as $priority => $x) {
                echo view_i_x($x, null, true);
            }
            echo '</div>';

        }

    } else {

        //Not logged in, show description:

        //IDEA TITLE
        echo '<h1 class="block-one" style="padding-top: 21px;"><span class="icon-block top-icon">'.view_x_icon_legend( false , 0 ).'</span><span class="title-block-lg">' . view_i_title($i) . '</span></h1>';

        //IDEA MESSAGES
        echo '<div style="margin-bottom:34px;">';
        foreach($this->X_model->fetch(array(
            'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
            'x__type' => 4231, //IDEA NOTES Messages
            'x__right' => $i['i__id'],
        ), array(), 0, 0, array('x__sort' => 'ASC')) as $x) {
            echo $this->X_model->message_send( $x['x__message'] );
        }
        echo '</div>';

    }



    //FEATURED IDEAS
    $featured_is = $this->X_model->fetch(array(
        'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
        'i__status IN (' . join(',', $this->config->item('n___7355')) . ')' => null, //PUBLIC
        'x__type IN (' . join(',', $this->config->item('n___12840')) . ')' => null, //IDEA LINKS TWO-WAY
        'x__left' => $i['i__id'],
    ), array('x__right'), 0, 0, array('x__sort' => 'ASC'));

    echo '<div class="headline" style="margin-top: 34px;"><span class="icon-block">'.$e___11035[13427]['m_icon'].'</span>'.$e___11035[13427]['m_name'].'</div>';
    echo '<div class="cover-list">';
    foreach($featured_is as $key => $x){
        if(!in_array($x['i__id'], $member_xy_ids)){
            //Show only if not in discovering list:
            echo view_i_cover($x, false);
        }
    }
    echo '</div>';


    ?>
</div>
