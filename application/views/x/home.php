
<script src="/application/views/x/home.js?v=<?= config_var(11060) ?>" type="text/javascript"></script>

<div class="container">
    <?php

    $session_source = superpower_assigned();
    $e___11035 = $this->config->item('e___11035'); //MENCH NAVIGATION
    $player_discovery_ids = array();


    if($session_source){

        //MY DISCOVERIES
        $player_discoveries = $this->X_model->fetch(array(
            'x__member' => $session_source['e__id'],
            'x__type IN (' . join(',', $this->config->item('n___12969')) . ')' => null, //MY DISCOVERIES
            'i__status IN (' . join(',', $this->config->item('n___7355')) . ')' => null, //PUBLIC
            'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
        ), array('x__left'), 0, 0, array('x__sort' => 'ASC'));

        echo ( count($player_discoveries) > 1 ? '<script> $(document).ready(function () {x_sort_load(6132)}); </script>' : '<style> .discover-sorter {display:none !important;} </style>' ); //Need 2 or more to sort

        if(count($player_discoveries)){

            echo '<div class="discover-topic" style="margin-top: 34px;"><span class="icon-block">'.$e___11035[12969]['m_icon'].'</span>'.$e___11035[12969]['m_name'].'</div>';


            echo '<div class="clear-discovery-list">';
            echo '<div id="idea_covers" class="cover-list">';
            foreach($player_discoveries as $i) {
                array_push($player_discovery_ids, $i['i__id']);
                echo view_i_cover($i, true);
            }
            echo '</div>';
            echo '</div>';

            echo '<div class="doclear">&nbsp;</div>';


            //DISCOVER DELETE ALL (ACCESSIBLE VIA MAIN MENU)
            echo '<div class="clear-discovery-list hidden margin-top-down">';
            echo '<div class="alert alert-danger" role="alert">';
            echo '<span class="icon-block"><i class="fas fa-exclamation-circle discover"></i></span><b class="discover montserrat">DELETE ALL DISCOVERIES?</b>';
            echo '<br /><span class="icon-block">&nbsp;</span>Action cannot be undone.';
            echo '</div>';
            echo '<p style="margin-top:20px;"><a href="javascript:void(0);" onclick="discover_clear_all()" class="btn btn-discover"><i class="far fa-trash-alt"></i> DELETE ALL</a> or <a href="javascript:void(0)" onclick="$(\'.clear-discovery-list\').toggleClass(\'hidden\')" style="text-decoration: underline;">Cancel</a></p>';
            echo '</div>';

            echo '<div class="doclear">&nbsp;</div>';


        }



        //Saved
        $player_saved = $this->X_model->fetch(array(
            'x__up' => $session_source['e__id'],
            'x__type' => 12896, //SAVED
            'i__status IN (' . join(',', $this->config->item('n___7355')) . ')' => null, //PUBLIC
            'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
        ), array('x__right'), 0, 0, array('x__id' => 'DESC'));

        if(count($player_saved)){

            echo '<div class="discover-topic" style="margin-top: 34px;"><span class="icon-block">'.$e___11035[12896]['m_icon'].'</span>'.$e___11035[12896]['m_name'].'</div>';

            echo '<div class="list-group no-side-padding">';
            foreach($player_saved as $priority => $i) {
                echo view_i_discover($i, null, true);
            }
            echo '</div>';

        }

    } else {

        //Not logged in, show description:
        $is = $this->I_model->fetch(array(
            'i__id' => config_var(13405),
        ));

        //IDEA TITLE
        echo '<h1 class="block-one" style="padding-top: 21px;"><span class="icon-block top-icon">'.view_x_icon_legend( false , 0 ).'</span><span class="title-block-lg">' . view_i_title($is[0]) . '</span></h1>';

        //IDEA MESSAGES
        echo '<div style="margin-bottom:34px;">';
        foreach($this->X_model->fetch(array(
            'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
            'x__type' => 4231, //IDEA NOTES Messages
            'x__right' => config_var(13405),
        ), array(), 0, 0, array('x__sort' => 'ASC')) as $message_discover) {
            echo $this->X_model->message_send( $message_discover['x__message'] );
        }
        echo '</div>';

    }



    //FEATURED IDEAS
    $featured_ideas = $this->X_model->fetch(array(
        'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
        'i__status IN (' . join(',', $this->config->item('n___7355')) . ')' => null, //PUBLIC
        'x__type IN (' . join(',', $this->config->item('n___12840')) . ')' => null, //IDEA LINKS TWO-WAY
        'x__left' => config_var(13405),
    ), array('x__right'), 0, 0, array('x__sort' => 'ASC'));

    echo '<div class="discover-topic" style="margin-top: 34px;"><span class="icon-block">'.$e___11035[13427]['m_icon'].'</span>'.$e___11035[13427]['m_name'].'</div>';
    echo '<div class="cover-list">';
    foreach($featured_ideas as $key => $i){
        if(!in_array($i['i__id'], $player_discovery_ids)){
            //Show only if not in discovering list:
            echo view_i_cover($i, false);
        }
    }
    echo '</div>';


    ?>
</div>
