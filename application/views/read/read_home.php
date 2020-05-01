<?php
$timestamp = time();
//Fetch reads list:
$player_reads = $this->LEDGER_model->ln_fetch(array(
    'ln_creator_source_id' => $session_en['en_id'],
    'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_7347')) . ')' => null, //READ LIST Idea Set
    'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //PUBLIC
    'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //PUBLIC
), array('in_previous'), 0, 0, array('ln_order' => 'ASC'));
?>

<style>
    <?= ( count($player_reads) < 2 ? '.read-sorter {display:none !important;}' : '' ) //Need 2 or more to sort ?>
</style>
<script>

    //Include some cached sources:
    var clear_read_url = '<?= '/read/read_coins_remove_all/'.$session_en['en_id'].'/'.$timestamp.'/'.md5($session_en['en_id'] . $this->config->item('cred_password_salt') . $timestamp) ?>';

    <?= ( count($player_reads) >= 2 ? '$(document).ready(function () {read_sort_load()});' : '' ) ?>

</script>
<script src="/application/views/read/read_home.js?v=<?= config_var(11060) ?>" type="text/javascript"></script>


<div class="container">
<?php


$en_all_11035 = $this->config->item('en_all_11035'); //SOURCE
echo '<div class="read-topic"><span class="icon-block">'.$en_all_11035[7347]['m_icon'].'</span>'.$en_all_11035[7347]['m_name'].'</div>';




//List Reads:
if(!count($player_reads)){

    echo '<div class="alert alert-danger"><span class="icon-block"><i class="fas fa-exclamation-circle read"></i></span>No Reads yet. Visit <a href="/" class="underline">Home</a> to start reading.</div>';

} else {

    echo '<div class="clear-reads-list">';

    $all_completed = true;
    echo '<div id="actionplan_steps" class="list-group no-side-padding">';
    foreach($player_reads as $priority => $in) {
        $completion_rate = $this->READ_model->read_completion_progress($session_en['en_id'], $in);
        echo echo_in_read($in, false, null, null, true, $completion_rate);
        if($completion_rate['completion_percentage']!=100 && $all_completed){
            $all_completed = false;
        }
    }
    echo '</div>';

    //NEXT
    if(!$all_completed){
        echo '<div class="inline-block margin-top-down pull-right"><a href="/read/next" class="btn btn-read btn-circle">'.$en_all_11035[12211]['m_icon'].'</a></div>';
    }


    echo '</div>';


    //READ DELETE ALL (ACCESSIBLE VIA MAIN MENU)
    echo '<div class="clear-reads-list hidden margin-top-down">';
    echo '<div class="alert alert-danger" role="alert">';
    echo '<span class="icon-block"><i class="fas fa-exclamation-circle read"></i></span><b class="read montserrat">DELETE ALL READ COINS?</b>';
    echo '<br /><span class="icon-block">&nbsp;</span>Action cannot be undone.';
    echo '</div>';
    echo '<p style="margin-top:20px;"><a href="javascript:void(0);" onclick="read_clear_all()" class="btn btn-read"><i class="far fa-trash-alt"></i> DELETE ALL</a> or <a href="javascript:void(0)" onclick="$(\'.clear-reads-list\').toggleClass(\'hidden\')" style="text-decoration: underline;">Cancel</a></p>';
    echo '</div>';

}




?>
</div>