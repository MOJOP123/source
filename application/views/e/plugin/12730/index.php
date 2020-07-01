<?php

//UI to compose a test message:
echo '<form method="GET" action="">';

echo '<div class="mini-header">Search String:</div>';
echo '<input type="text" class="form-control border maxout" name="search_for" value="'.@$_GET['search_for'].'"><br />';


$search_for_is_set = (isset($_GET['search_for']) && strlen($_GET['search_for'])>0);
$replace_with_is_set = ((isset($_GET['replace_with']) && strlen($_GET['replace_with'])>0) || (isset($_GET['append_text']) && strlen($_GET['append_text'])>0));
$replace_with_is_confirmed = false;

if($search_for_is_set){

    $matching_results = $this->E_model->fetch(array(
        'e__status IN (' . join(',', $this->config->item('sources_id_7358')) . ')' => null, //ACTIVE
        'LOWER(e__title) LIKE \'%'.strtolower($_GET['search_for']).'%\'' => null,
    ));

    //List the matching search:
    echo '<table class="table table-sm table-striped stats-table mini-stats-table">';


    echo '<tr class="panel-title down-border">';
    echo '<td style="text-align: left;" colspan="4">'.count($matching_results).' Sources Found</td>';
    echo '</tr>';


    if(count($matching_results) < 1){

        $replace_with_is_set = false;
        unset($_GET['confirm_statement']);
        unset($_GET['replace_with']);

    } else {

        $confirmation_keyword = 'Replace '.count($matching_results);
        $replace_with_is_confirmed = (isset($_GET['confirm_statement']) && strtolower($_GET['confirm_statement'])==strtolower($confirmation_keyword));

        echo '<tr class="panel-title down-border" style="font-weight:bold !important;">';
        echo '<td style="text-align: left;">#</td>';
        echo '<td style="text-align: left;">Matching Search</td>';
        echo '<td style="text-align: left;">'.( $replace_with_is_set ? 'Replacement' : '' ).'</td>';
        echo '<td style="text-align: left;">&nbsp;</td>';
        echo '</tr>';

        foreach($matching_results as $count=>$en){

            if($replace_with_is_set){
                //Do replacement:
                $append_text = @$_GET['append_text'];
                $new_outcome = str_replace($_GET['search_for'],$_GET['replace_with'],$en['e__title']).$append_text;

                if($replace_with_is_confirmed){
                    //Update idea:
                    $this->E_model->update($en['e__id'], array(
                        'e__title' => $new_outcome,
                    ), true, $session_source['e__id']);
                }
            }

            echo '<tr class="panel-title down-border">';
            echo '<td style="text-align: left;">'.($count+1).'</td>';
            echo '<td style="text-align: left;">'.view_cache('e___6177' /* Source Status */, $en['e__status'], true, 'right').' <a href="/@'.$en['e__id'].'">'.$en['e__title'].'</a></td>';

            if($replace_with_is_set){

                echo '<td style="text-align: left;">'.$new_outcome.'</td>';
                echo '<td style="text-align: left;">'.( $replace_with_is_confirmed ? '<i class="fas fa-check-circle"></i> Outcome Updated' : '').'</td>';
            } else {

                echo '<td style="text-align: left;"></td>';
                echo '<td style="text-align: left;"></td>';
            }


            echo '</tr>';

        }
    }

    echo '</table>';
}


if($search_for_is_set && count($matching_results) > 0){
    //now give option to replace with:
    echo '<div class="mini-header">Replace With:</div>';
    echo '<input type="text" class="form-control border maxout" name="replace_with" value="'.@$_GET['replace_with'].'"><br />';

    //now give option to replace with:
    echo '<div class="mini-header">Append Text:</div>';
    echo '<input type="text" class="form-control border maxout" name="append_text" value="'.@$_GET['append_text'].'"><br />';
}

if($replace_with_is_set){
    //now give option to replace with:
    echo '<div class="mini-header">Confirm Replacement by Typing "'.$confirmation_keyword.'":</div>';
    echo '<input type="text" class="form-control border maxout" name="confirm_statement" value="'. @$_GET['confirm_statement'] .'"><br />';
}


echo '<input type="submit" class="btn btn-idea" value="Go">';
echo '</form>';
