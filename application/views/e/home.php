<div class="container">

    <?php

    $load_max = config_var(13206);
    $show_max = config_var(11986);

    //SOURCE HOME
    foreach($this->config->item('sources__13207') as $e__id => $m) {

        if(in_array($e__id, $this->config->item('sources_id_13365'))){

            //SOURCE GROUPS
            //TODO: Expand to include x__down for IDEA COINS (Currently only counts x__up)
            $e_list = $this->X_model->fetch(array(
                'x__status IN (' . join(',', $this->config->item('sources_id_7359')) . ')' => null, //PUBLIC
                'x__type IN (' . join(',', $this->config->item('sources_id_12273')) . ')' => null, //IDEA COIN
                ' EXISTS (SELECT 1 FROM mench__x WHERE e__id=x__down AND x__up IN (' . join(',', $this->config->item('sources_id_'.$e__id)) . ') AND x__type IN (' . join(',', $this->config->item('sources_id_4592')) . ') AND x__status IN ('.join(',', $this->config->item('sources_id_7359')) /* PUBLIC */.')) ' => null,
            ), array('x__up'), $load_max, 0, array('totals' => 'DESC'), 'COUNT(x__id) as totals, e__id, e__title, e__icon, e__metadata, e__status, e__weight', 'e__id, e__title, e__icon, e__metadata, e__status, e__weight');

        } else {
            //Unknown
            continue;
        }

        if(!count($e_list)){
            continue;
        }



        echo '<div class="discover-topic"><span class="icon-block">'.$m['m_icon'].'</span>'.$m['m_name'].'</div>';
        echo '<div class="list-group" style="padding-bottom:34px;">';


        foreach($e_list as $count=>$source) {

            if($count==$show_max){
                echo '<div class="list-group-item see_more_who'.$e__id.' no-side-padding"><a href="javascript:void(0);" onclick="$(\'.see_more_who'.$e__id.'\').toggleClass(\'hidden\')" class="block"><span class="icon-block"><i class="far fa-plus-circle source"></i></span><b class="montserrat source" style="text-decoration: none !important;">SEE MORE</b></a></div>';
                echo '<div class="list-group-item see_more_who'.$e__id.' no-height"></div>';
            }

            echo view_e($source, false, ( $count<$show_max ? '' : 'see_more_who'.$e__id.' hidden'));

        }

        echo '</div>';
    }

    ?>
</div>