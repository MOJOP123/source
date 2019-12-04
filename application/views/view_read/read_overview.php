
<div class="container">

    <div class="alert alert-info" style="margin-bottom: 20px;">
        <div><i class="fas fa-lightbulb-on"></i> <b class="montserrat">MENCH</b> is an interactive publishing platform that allows anyone to share ideas that matter. Writers use a simple web app to create microblogs, each focused on a key idea. Microblogs can be linked together collaboratively to communicate bigger ideas, or cite expert sources to <span class="read-more hidden">gain more credibility. Readers use the web or Messenger to interactively choose their next read. Players earn crypto-coins for each word they read or write.</span><span class="read-more">... <a href="javascript:void(0);" onclick="$('.read-more').toggleClass('hidden')">more</a></span></div>
        <div class="read-more hidden"><a href="/play">Top Players</a> | <a href="/blog">Start Blogging</a></div>
    </div>

    <?php

    //Go through all categories and see which ones have published courses:
    foreach($this->config->item('en_all_10869') /* Course Categories */ as $en_id => $m) {

        //Count total published courses here:
        $published_ins = $this->READ_model->ln_fetch(array(
            'ln_status_entity_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
            'in_status_entity_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //Intent Statuses Public
            'in_completion_method_entity_id IN (' . join(',', $this->config->item('en_ids_7582')) . ')' => null, //READ LOGIN REQUIRED
            'ln_type_entity_id' => 4601, //BLOG KEYWORDS
            'ln_parent_entity_id' => $en_id,
        ), array('in_child'), 0, 0, array('in_outcome' => 'ASC'));

        if(!count($published_ins)){
            continue;
        }

        $common_prefix = common_prefix($published_ins, 'in_outcome');
        $common_prefix = null;

        //Show featured blogs in this category:
        echo '<div style="margin-top: 30px; font-size: 0.8em; color: #999;"><span class="icon-block">'.$m['m_icon'].'</span> '.$m['m_name'].'</div>';
        echo '<div class="list-group">';
        foreach($published_ins as $published_in){
            echo echo_in_read($published_in, $common_prefix);
        }
        echo '</div>';

    }

    ?>

</div>