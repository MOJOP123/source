<?php

$timestamp = time();
$en_all_11035 = $this->config->item('en_all_11035'); //MENCH  NAVIGATION

?>


<script>
    //Include some cached sources:
    var clear_read_url = '<?= '/read/actionplan_reset_progress/'.$session_en['en_id'].'/'.$timestamp.'/'.md5($session_en['en_id'] . $this->config->item('cred_password_salt') . $timestamp) ?>';

    <?= ( count($player_reads) >= 2 ? '$(document).ready(function () {load_read_sort()});' : '' ) ?>

</script>
<script src="/application/views/read/read_home.js?v=v<?= config_var(11060) ?>" type="text/javascript"></script>

<div class="container">
<?php
echo '<div class="read-topic"><span class="icon-block">'.$en_all_11035[7347]['m_icon'].'</span>'.$en_all_11035[7347]['m_name'].'</div>';


if(!$session_en){

    echo '<div style="padding:10px 0 20px;"><a href="/sign?url=/read" class="btn btn-read montserrat">'.$en_all_11035[4269]['m_name'].'<span class="icon-block">'.$en_all_11035[4269]['m_icon'].'</span></a> to get started.</div>';

} else {


    //List Reads:
    echo '<div id="actionplan_steps" class="list-group no-side-padding">';
    foreach ($player_reads as $priority => $ln) {
        echo echo_in_read($ln, false, null, null, null, true);
    }
    echo '</div>';


    //Call to Actions:
    echo '<div style="margin-top: 10px;">';

        //Add New Read:
        echo '<a href="/" class="btn btn-read" title="'.$en_all_11035[12581]['m_name'].'">'.$en_all_11035[12581]['m_icon'].'</a>&nbsp;&nbsp;';


        //Next Read:
        echo '<a href="/read/next" class="btn btn-read">'.$en_all_11035[12211]['m_name'].' '.$en_all_11035[12211]['m_icon'].'</a>&nbsp;&nbsp;';


        //Give option to delete all:
        echo '<a href="javascript:void(0)" onclick="$(\'.clear-reading-list\').toggleClass(\'hidden\')" class="btn btn-read '.superpower_active(10984).'">'.$en_all_11035[6415]['m_icon'].'</a>';
        echo '<div class="clear-reading-list hidden" style="padding:34px 0;">';
        echo '<p><span class="icon-block"><i class="fad fa-exclamation-triangle read"></i></span><b class="read montserrat">WARNING:</b> You are about to clear you entire reading list. You will lose all your <span class="icon-block">🔴</span><b class="montserrat read">READ COINS</b> but can earn them back by reading again.</p>';
        echo '<p style="margin-top:20px;"><a href="javascript:void(0);" onclick="clear_all_reads()" class="btn btn-read"><i class="far fa-trash-alt"></i> CLEAR ALL READS</a> or <a href="javascript:void(0)" onclick="$(\'.clear-reading-list\').toggleClass(\'hidden\')" style="text-decoration: underline;">Cancel</a></p>';
        echo '</div>';


    echo '</div>';


}
?>
</div>