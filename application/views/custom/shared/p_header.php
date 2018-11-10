<?php 
//Attempt to fetch session variables:
$udata = $this->session->userdata('user');
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="icon" type="image/png" href="/img/bp_16.png">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title><?= 'Mench'.( isset($title) ? ' | '.$title : '' ) ?></title>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
	
	<?php $this->load->view('custom/shared/header_resources' ); ?>

    <script src="/js/custom/global.js?v=v<?= $this->config->item('app_version') ?>" type="text/javascript"></script>
</head>

<body id="funnel">

<div class="main main-raised student-hub">
<div class="container body-container">

<?php
if(isset($hm) && $hm){
    echo $hm;
} else {
    $hm = $this->session->flashdata('hm');
    if($hm){
        echo $hm;
    }
}
?>