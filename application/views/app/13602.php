<?php

$start_year = 2017;
$start_month = 01;

echo '<table>';

foreach($this->config->item('e___2738') as $x__type => $m) {

    if($x__type==12273){

        //IDEAS
        $unique = $this->X_model->fetch(array(
            'i__type IN (' . join(',', $this->config->item('n___7355')) . ')' => null, //PUBLIC
            'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
            'x__type IN (' . join(',', $this->config->item('n___13480')) . ')' => null, //UNIQUE IDEAS
        ), array('x__right'), 0, 0, array(), 'COUNT(x__id) as totals');

    } elseif($x__type==12274){

        //SOURCE
        $unique = $this->X_model->fetch(array(
            'e__type IN (' . join(',', $this->config->item('n___7357')) . ')' => null, //PUBLIC
            'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
            'x__type IN (' . join(',', $this->config->item('n___13548')) . ')' => null, //UNIQUE SOURCES
        ), array('x__down'), 0, 0, array(), 'COUNT(x__id) as totals');

    } elseif($x__type==6255){

        //READ
        $unique = $this->X_model->fetch(array(
            'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
            'x__type IN (' . join(',', $this->config->item('n___6255')) . ')' => null, //READ COIN
        ), array(), 0, 0, array(), 'COUNT(x__id) as totals');

    }


    echo '<tr>';
    echo '<td class="css__title doupper"><div class="col_name">'.extract_icon_color($m['m__icon'], true).' '.$m['m__title'].'</div></td>';
    echo '<td>'.number_format($unique[0]['totals'], 0).'</td>';
    echo '<td>&nbsp;</td>';
    echo '<td>&nbsp;</td>';
    echo '<td>&nbsp;</td>';


    for($i=0;$i<1000;$i++){

        $time_start = date("Y-m-d H:i:s", mktime(0, 0, 0, $start_month+$i, 1, $start_year));
        $time_end = date("Y-m-d H:i:s", mktime(0, 0, 0, $start_month+$i+1, 1, $start_year));

        if($x__type==12273){

            //IDEAS
            $query = $this->X_model->fetch(array(
                'i__type IN (' . join(',', $this->config->item('n___7355')) . ')' => null, //PUBLIC
                'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
                'x__type IN (' . join(',', $this->config->item('n___13480')) . ')' => null, //UNIQUE IDEAS
                'x__time >=' => $time_start,
                'x__time <' => $time_end,
            ), array('x__right'), 0, 0, array(), 'COUNT(x__id) as totals');

        } elseif($x__type==12274){

            //SOURCE
            $query = $this->X_model->fetch(array(
                'e__type IN (' . join(',', $this->config->item('n___7357')) . ')' => null, //PUBLIC
                'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
                'x__type IN (' . join(',', $this->config->item('n___13548')) . ')' => null, //UNIQUE SOURCES
                'x__time >=' => $time_start,
                'x__time <' => $time_end,
            ), array('x__down'), 0, 0, array(), 'COUNT(x__id) as totals');

        } elseif($x__type==6255){

            //READ
            $query = $this->X_model->fetch(array(
                'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
                'x__type IN (' . join(',', $this->config->item('n___6255')) . ')' => null, //READ COIN
                'x__time >=' => $time_start,
                'x__time <' => $time_end,
            ), array(), 0, 0, array(), 'COUNT(x__id) as totals');

        }

        echo '<td style="font-size: 0.8em;"><div class="col_stat">'.( $query[0]['totals'] > 0 ? number_format($query[0]['totals'], 0) : '&nbsp;' ).'</div></td>';

        if(date("Y-m", mktime(0, 0, 0, $start_month+$i, 1, $start_year))==date("Y-m")){
            break;
        }

    }
    echo '</tr>';
}


echo '<tr>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
for($i=0;$i<1000;$i++){

    $time_start = date("Y-m-d H:i:s", mktime(0, 0, 0, $start_month+$i, 1, $start_year));
    $time_end = date("Y-m-d H:i:s", mktime(0, 0, 0, $start_month+$i+1, 1, $start_year));

    echo '<td style="font-size: 0.8em;" title="'.$time_start.' - '.$time_end.'"><div class="col_stat css__title"><b>'.date("ym", mktime(0, 0, 0, $start_month+$i, date("j"), $start_year)).'</b></div></td>';

    if(date("Y-m", mktime(0, 0, 0, $start_month+$i, 1, $start_year))==date("Y-m")){
        break;
    }
}
echo '</tr>';


echo '</table>';
