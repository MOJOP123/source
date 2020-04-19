
<script src="/application/views/idea/idea_home.js?v=<?= config_var(11060) ?>" type="text/javascript"></script>

<div class="container">
    <?php
    if(!superpower_assigned(10939)) {

        //START PUBLISHING
        $en_all_10876 = $this->config->item('en_all_10876'); //MENCH WEBSITE
        echo '<div style="padding:10px 0;"><a href="'.$en_all_10876[10939]['m_desc'].'" class="btn btn-idea montserrat">GET STARTED <i class="fad fa-step-forward"></i></a></div>';

    } else {

        //IDEA BOOKMARKS
        echo '<div id="myIdeas" class="list-group">';
        foreach($this->DISCOVER_model->ln_fetch(array(
            'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7356')) . ')' => null, //Idea Status Active
            'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Transaction Status Public
            'ln_type_source_id' => 10573, //Idea Notes Bookmarks
            'ln_parent_source_id' => $session_en['en_id'], //For this player
        ), array('in_child'), 0, 0, array('in_weight' => 'DESC')) as $bookmark_in){
            echo echo_in($bookmark_in, 0, false, true);
        }

        $en_all_2738 = $this->config->item('en_all_2738'); //MENCH
        echo '<div class="list-group-item itemidea" style="padding:5px 0;">
                <div class="input-group border">
                    <span class="input-group-addon addon-lean" style="margin-top: 6px;"><span class="icon-block">'.$en_all_2738[4535]['m_icon'].'</span></span>
                    <input type="text"
                           class="form-control form-control-thick montserrat algolia_search dotransparent"
                           maxlength="' . config_var(11071) . '"
                           id="newIdeaTitle"
                           style="margin-bottom: 0; padding: 5px 0;"
                           placeholder="NEW IDEA">
                </div><div class="algolia_pad_search hidden in_pad_new_in"></div></div>';

        echo '</div>';

    }
    ?>
</div>