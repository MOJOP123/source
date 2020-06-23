<?php

$session_source = superpower_assigned();
$first_segment = $this->uri->segment(1);
$is_home = !strlen($first_segment);
$sources__11035 = $this->config->item('sources__11035'); //MENCH NAVIGATION
$sources__2738 = $this->config->item('sources__2738');
$current_mench = current_mench();

?><!doctype html>
<html lang="en" >
<head>

    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="/img/<?= ( !$first_segment ? 'mench' : $current_mench['x_name'] ) ?>.png">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= ( isset($title) ? $title : '' ) ?></title>


    <script type="text/javascript">
    <?php
    //PLAYER VARIABLES
    echo ' var js_session_superpowers_assigned = ' . json_encode( ($session_source && count($this->session->userdata('session_superpowers_assigned'))) ? $this->session->userdata('session_superpowers_assigned') : array() ) . '; ';
    echo ' var js_pl_id = ' . ( $session_source ? $session_source['e__id'] : 0 ) . '; ';
    echo ' var js_pl_name = \'' . ( $session_source ? $session_source['e__title'] : '' ) . '\'; ';
    echo ' var base_url = \'' . $this->config->item('base_url') . '\'; ';

    //JAVASCRIPT PLATFORM MEMORY
    foreach($this->config->item('sources__11054') as $x__type => $m){
        if(count($this->config->item('sources__'.$x__type))){
            echo ' var js_sources__'.$x__type.' = ' . json_encode($this->config->item('sources__'.$x__type)) . ';';
            echo ' var js_sources_id_'.$x__type.' = ' . json_encode($this->config->item('sources_id_'.$x__type)) . ';';
        }
    }
    ?>
    </script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/js/bootstrap.min.js" integrity="sha384-3qaqj0lc6sV/qpzrc1N5DC6i1VRn/HyX4qdPaiEFbn54VjQBEU341pvjz7Dv3n6P" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/typeit@6.1.1/dist/typeit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/autosize@4.0.2/dist/autosize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@15.1.1/dist/lazyload.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.textcomplete/1.8.5/jquery.textcomplete.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autocomplete.js/0.37.0/autocomplete.jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/algoliasearch/3.35.1/algoliasearch.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.10.1/Sortable.min.js" type="text/javascript"></script>

    <script src="/application/views/global.js?v=<?= config_var(11060) ?>" type="text/javascript"></script>

    <?php if($current_mench['x_name']=='discover'){ ?>
    <script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property=5ec369bdaa9dfe001ab3f797&product=custom-share-buttons&cms=website' async='async'></script>
    <?php } ?>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css" integrity="sha384-SI27wrMjH3ZZ89r4o+fGIJtnzkAnFs3E4qz9DIYioCQ5l9Rd/7UAa8DHcaL8jkWt" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:800|Roboto+Mono:wght@500|Rubik&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.13.0/css/all.css" integrity="sha384-IIED/eyOkM6ihtOiQsX2zizxFBphgnv1zbe1bKA+njdFzkr6cDNy16jfIKWu4FNH" crossorigin="anonymous">
    <link href="/application/views/global.css?v=<?= config_var(11060) ?>" rel="stylesheet"/>

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-92774608-1"></script>
    <script type="text/javascript">
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-92774608-1');
    </script>

</head>

<body class="<?= 'to'.$current_mench['x_name'] ?>">

<?php
//Any message we need to show here?
if (!isset($flash_message)) {
    $flash_message = $this->session->flashdata('flash_message');
}


if(strlen($flash_message) > 0) {

    //Delete from Flash:
    $this->session->unmark_flash('flash_message');

    echo '<div class="container '.( isset($hide_header) ? ' center-info ' : '' ).'" id="custom_message" style="padding-bottom: 0;">'.$flash_message.'</div>';
}



if(!isset($hide_header)){
    //Do not show for /sign view
    ?>

    <!-- MENCH LINE -->
    <div class="container fixed-top" style="padding-bottom: 0 !important;">
        <div class="row">
            <table class="mench-navigation">
                <tr>
                    <td>
                        <?php

                        //MAIN NAVIGATION
                        echo '<div class="primary_nav mench_nav">';
                        if(!$session_source){

                            //LOGO ONLY
                            echo '<a href="/"><img src="/img/mench.png" class="mench-logo mench-spin" /><b class="montserrat text-logo">MENCH</b></a>';

                        } else {

                            //RESORT
                            $sources__12893_resort = array();
                            $count = 0;
                            foreach($this->config->item('sources__12893') as $x__type => $m) {
                                $m['e__id'] = $x__type;
                                if((!isset($sources__12893_resort[0])) && (
                                    $_SERVER['REQUEST_URI'] == $m['m_desc'] ||
                                    ( $x__type==6205 /*  DISCOVER  */ && $current_mench['x_name']=='discover' ) ||
                                    ( $x__type==4535 /* IDEATE */ && $current_mench['x_name']=='idea' ) ||
                                    ( $x__type==4536 /* SOURCE */ && $current_mench['x_name']=='source' )
                                )){
                                    $sources__12893_resort[0] = $m;
                                } else {
                                    $count++;
                                    $sources__12893_resort[$count] = $m;
                                }
                            }
                            ksort($sources__12893_resort);


                            //Show Mench Menu:
                            foreach($sources__12893_resort as $count => $m) {

                                $class = extract_icon_color($m['m_icon']);

                                //Apply superpower to Mench actions only
                                $superpower_actives = ( in_array($m['e__id'], $this->config->item('sources_id_2738')) ? array_intersect($this->config->item('sources_id_10957'), $m['m_parents']) : array());

                                echo '<div class="btn-group mench_coin '.$class.' border-' . $class.( count($superpower_actives) ? superpower_active(end($superpower_actives)) : '' ).'">';
                                echo '<a class="btn ' . $class . '" href="' . $m['m_desc'] .'">';
                                echo '<span class="icon-block">' . $m['m_icon'] . '</span>';
                                echo '<span class="montserrat ' . $class . '_name '.( $count>0 ? 'show-max' : '' ).'">' . $m['m_name'] . '&nbsp;</span>';
                                echo '</a>';
                                echo '</div>';

                            }

                        }
                        echo '</div>';

                        //Search Bar
                        echo '<div class="primary_nav search_nav hidden"><form id="searchFrontForm"><input class="form-control algolia_search" type="search" id="mench_search" data-lpignore="true" placeholder="'.$sources__11035[7256]['m_name'].'"></form></div>';

                        ?>
                    </td>

                    <?php

                    //Search
                    if(intval(config_var(12678))){
                        echo '<td class="block-link"><a href="javascript:void(0);" onclick="toggle_search()" style="margin-left: 0;"><span class="search_icon">'.$sources__11035[7256]['m_icon'].'</span><span class="search_icon hidden" title="'.$sources__11035[13401]['m_name'].'">'.$sources__11035[13401]['m_icon'].'</span></a></td>';
                    }

                    //Account
                    if ($session_source) {

                        //Player Menu
                        $sources__4527 = $this->config->item('sources__4527'); //Platform Memory
                        $sources__10876 = $this->config->item('sources__10876'); //Mench Website
                        $load_menu = 12500;

                        echo '<td class="block-menu">';
                        echo '<div class="dropdown inline-block">';
                        echo '<button type="button" class="btn no-side-padding" id="dropdownMenuButton'.$load_menu.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                        echo '<span class="icon-block">' .$sources__4527[$load_menu]['m_icon'].'</span>';
                        echo '</button>';

                        echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton'.$load_menu.'">';
                        foreach($this->config->item('sources__'.$load_menu) as $x__type => $m) {

                            //Skip superpowers if not assigned
                            if($x__type==10957 && !count($this->session->userdata('session_superpowers_assigned'))){
                                continue;
                            } elseif($x__type==6415 && !$is_home){
                                //Deleting discovers only available on Discoveries home
                                continue;
                            } elseif($x__type==12749 && !$is_home && !is_numeric($first_segment)){
                                //Not an editable discovery
                                continue;
                            }

                            $superpower_actives = array_intersect($this->config->item('sources_id_10957'), $m['m_parents']);
                            $extra_class = null;
                            $text_class = null;

                            if($x__type==12749) {

                                //Map Idea
                                $page_url = 'href="/~'.( $is_home ? $this->config->item('featured_i__id') : $first_segment ).'"';

                            } elseif(in_array($x__type, $this->config->item('sources_id_10876'))){

                                //Fetch URL:
                                $page_url = 'href="'.$sources__10876[$x__type]['m_desc'].'"';

                            } elseif($x__type==12205) {

                                //SET TO PLAYER
                                $x__type = $session_source['e__id'];
                                $page_url = 'href="/@'.$x__type.'"';
                                $m['m_name'] = $session_source['e__title'];
                                $m['m_icon'] = $session_source['e__icon'];
                                $text_class = 'text__6197_'.$x__type;

                            } elseif($x__type==12899) {

                                //FEEDBACK SUPPORT
                                $page_url = 'href="javascript:void(0);"';
                                $extra_class = ' icon_12899 ';

                            } elseif($x__type==6415) {

                                //CLEAR DISCOVERIES
                                $page_url = 'href="javascript:void(0)" onclick="$(\'.clear-discovery-list\').toggleClass(\'hidden\')"';

                            } else {

                                continue;

                            }

                            //Navigation
                            echo '<a '.$page_url.' class="dropdown-item montserrat doupper '.extract_icon_color($m['m_icon']).( count($superpower_actives) ? superpower_active(end($superpower_actives)) : '' ).$extra_class.'"><span class="icon-block">'.$m['m_icon'].'</span><span class="'.$text_class.'">'.$m['m_name'].'</span></a>';

                        }

                        echo '</div>';
                        echo '</div>';
                        echo '</td>';

                    } else {

                        //FEEDBACK SUPPORT
                        //echo '<td class="block-link"><a class="icon_12899" href="javascript:void(0);" title="'.$sources__11035[12899]['m_name'].'">'.$sources__11035[12899]['m_icon'].'</a></td>';

                        //Sign In/Up
                        echo '<td class="block-link block-sign-link"><a href="/source/signin" class="montserrat"><span class="show-max">'.$sources__11035[4269]['m_name'].'&nbsp;</span>'.$sources__11035[4269]['m_icon'].'</a></td>';

                    }

                    ?>
                </tr>
            </table>
        </div>
    </div>

<?php } ?>