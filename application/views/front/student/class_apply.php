<?php
//Expand Prerequisites:
$pre_req_array = prep_prerequisites($admission);
?>
<script>
var current_section = 1; //The index for the wizard

function move_ui(adjustment){

	//Any pre-check with submitted data?
	//Let's check the value of the current posstible ID for input validation checking:
	if(adjustment>0 && typeof $('.wizard-box').eq((current_section-1)).attr( "id" ) !== 'undefined' && $('.wizard-box').eq((current_section-1)).attr( "id" ).length){
		var the_id = $('.wizard-box').eq((current_section-1)).attr( "id" );
		
		if(the_id=='overview_agree' && !document.getElementById('project_overview_agreement').checked){
			alert('You must agree to continue...');
			$('#'+the_id+' input').focus();
			return false;
		} else if(the_id=='refund_agreement' && !document.getElementById('project_refund_agreement').checked){
			alert('You must agree to continue...');
			$('#'+the_id+' input').focus();
			return false;
		}
	}
    
    
	//Variables:
	var total_steps = $('.wizard-box').length;
	if(adjustment<0 && current_section==1){
		return false;
	} else if(adjustment>0 && current_section==total_steps){
		return false;
	}
	
	//We're all good, lets continue:
	current_section = current_section+adjustment;
	var progress = Math.round((current_section/total_steps*100));

	//UI Adjustment
	$('.wizard-box').hide();
	$('.wizard-box').eq((current_section-1)).fadeIn(function(){
		  $( this ).find( "input, .ql-editor, textarea" ).focus();
	});

	//Previous Button adjustments:
	if(current_section==1){
		$('#btn_prev').hide();
	} else {
		$('#btn_prev').show();
	}
	
	//Update progress:
	$('.progress-bar').attr('aria-valuenow',progress).css('width',progress+'%');
	$('#step_progress').html(progress+'% Done');

	
	//Submit data only if last item:
	if(current_section==total_steps){

		//Hide both buttons:
		$('#btn_next, #btn_prev').hide();
		
		//Send for processing:
		$.post("/api_v1/submit_application", {

			//Core variables:
			ru_id:<?= $ru_id ?>,
			u_id:<?= $u_id ?>,
			u_key:'<?= $u_key ?>',
			
			//Get some PHP help to generate answers array for saving:
			answers: {
				'prerequisites' : {
					<?php
	        		if(count($pre_req_array)>0){
	        		    foreach($pre_req_array as $index=>$prereq){
	            	        //Now show the JS check for these fields:
	            	        ?>
	            	        '<?= ($index+1) ?>' : {
	        		        	'item' : '<?= str_replace('\'','',$prereq) ?>',
	        			        'answer' : ( document.getElementById('pre_requisite_<?= ($index+1) ?>').checked ? 'Yes' : 'No' ),
	        			    },
	            	        <?php
	            	    }
	            	 }
	            	 ?>
			    },
			},
    		
		}, function(data) {
			//Append data to view:
			$( "#application_result" ).html(data);
		});
	}
}

$(document).ready(function() {
	//Load first one:
	move_ui(0);
	//Watch for Ctrl+Enter
	$('body').keyup(function(e){
        if((event.keyCode == 10 || event.keyCode == 13) && event.ctrlKey)
        {
        	move_ui(1);
        }
    });
});
</script>

<style>
.wizard-box * { line-height:110%; }
.wizard-box { font-size:0.8em; }
.wizard-box label { font-size:0.8em; }
.wizard-box p, .wizard-box ul { margin-bottom:15px; }
.wizard-box ul li { margin-bottom:10px; }
.wizard-box a { text-decoration:underline; }
.wizard-box h4 { margin:0 0 15px 0; padding:0; font-size:1.2em; }
.aligned-list>li>i { width:36px; display:inline-block; text-align:center; }
.large-fa {font-size: 60px; margin-top:15px;}
.xlarge-fa {font-size: 68px; margin-top:15px;}
.progress{background-color:#FFF !important;}
.progress-bar{background-color:#000 !important;}
.enter{width:170px;}
.btn-primary{background-color: #000 !important;color:#FFF !important;}
.checkbox-material>.check{margin-top:-6px; margin-left:14px;}
.checkbox{ margin:30px 0; }
.form-group{text-align:left;}
.form-group textarea{padding:10px; max-width:600px; width:100%; height:120px; margin:5px 0 25px; font-size:18px; border:1px solid #000; }
#application_result {text-align:center; text-align: center; background-color: #FFF; margin:10px 0 40px 0; padding: 30px 5px 0; border-radius: 6px; height:125px; }
</style>




<p style="border-bottom:4px solid #000; font-weight:bold; padding-bottom:10px; margin-bottom:20px; display:block;">Join <?= $admission['c_objective'] ?> - Starting <?= time_format($admission['r_start_date'],4) ?></p>


<div class="wizard-box">
	<p>Hi <?= $admission['u_fname'] ?>,</p>
	<p>Welcome to the Project application.</p>
	<p>We just sent an email to <b><?= $admission['u_email'] ?></b> with a link to this application so you can easily access it at anytime.</p>
	<p>We're so excited to have you here! We're about to ask you a few questions to find out if you're a good fit for this Project.</p>
	<p>This application should take about 5 minutes to complete.</p>
</div>

<?php 
$start_times = $this->config->item('start_times');
?>

<div class="wizard-box" id="overview_agree">
	<p>Confirm that you commit to participating and doing the required work for this Project:</p>
	<ul>
		<li>Project Outcome: <b><?= $admission['c_objective'] ?></b></li>
    	<li>Instructor<?= ( count($admission['b__admins'])==1 ? '' : 's' ) ?>: 
        	<?php 
        	foreach($admission['b__admins'] as $key=>$instructor){
        	    if($key>0){
        	        echo ', ';
        	    }
        	    echo '<b>'.$instructor['u_fname'].' '.$instructor['u_lname'].'</b>';
        	}
        	?>
    	</li>
    	<li>Start Time: <b><?= time_format($admission['r_start_date'],2).' '.$start_times[$admission['r_start_time_mins']] ?> PST</b></li>
    	<li>End Time: <b><?= time_format($admission['r_start_date'],2,(7*24*3600-60)).' '.$start_times[$admission['r_start_time_mins']] ?> PST</b></li>
    	<li>Your Commitment: <b><?= echo_hours(($admission['c__estimated_hours']/count($admission['c__child_intents']))) ?></b></li>
	</ul>
	<div class="form-group label-floating is-empty">
    	<div class="checkbox">
        	<label>
        		<input type="checkbox" id="project_overview_agreement" /> <b style="font-size:1.3em;">Yes I Agree</b>
        	</label>
        </div>
    </div>
</div>


<?php if(count($pre_req_array)>0){ ?>
<div class="wizard-box" id="confirm_pre_requisites">
	<p>Below it's the list with all the prerequisites needed to apply for this Project.</p>
	<p>Select all the ones you currently meet:</p>
	<?php
	foreach($pre_req_array as $index=>$prereq){
	    ?>
	    <div class="form-group label-floating is-empty">
        	<div class="checkbox" style="margin:0; padding:0;">
            	<label>
            		<input type="checkbox" id="pre_requisite_<?= ($index+1) ?>" /> <b style="font-size:1.2em;"><?= $prereq ?></b>
            	</label>
            </div>
        </div>
	    <?php
	}
	?>
	<br />
</div>
<?php } ?>



<?php if($admission['b_fp_id']>0 && (!($admission['u_cache__fp_id']==$admission['b_fp_id']) || $admission['u_cache__fp_psid']<1)){ ?>
<div class="wizard-box">
	<p><b style="font-size:1.2em;">Activate Messenger</b></p>
	<p>This Project offers a direct chat line with the instructor team using Facebook Messenger. Activate your Messenger by clicking on this link:</p>
	<p style="margin:40px 0; font-weight:bold;"><a href="<?= $this->Comm_model->fb_activation_url($admission['u_id'],$admission['b_fp_id']) ?>" target="_blank">Activate Messenger</a> <i class="fa fa-external-link-square" style="font-size: 0.8em;" aria-hidden="true"></i></p>
</div>
<?php } ?>




<div class="wizard-box">
	<p>That's all!</p>
	<p>Click "Next" to submit your application!</p>
    <?php if($admission['r_usd_price']>0){ ?>
	<p>The final remaining step is to pay <b><?= echo_price($admission['r_usd_price']); ?></b> via PayPal to reserve your seat.</p>
    <?php } ?>
</div>


<div class="wizard-box">
	<p style="text-align:center;"><b>Submitting Your Application...</b></p>
	<div id="application_result"><img src="/img/round_load.gif" class="loader" /></div>
</div>




<a id="btn_prev" href="javascript:move_ui(-1)" class="btn btn-primary" style="padding-left:10px;padding-right:12px; display:none;"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
<span id="btn_next"><a href="javascript:move_ui(1)" class="btn btn-primary">Next <i class="fa fa-chevron-right" aria-hidden="true"></i></a><span class="enter">or press <b>CTRL+ENTER</b></span></span>

<div style="text-align:right; margin:-20px 2px 0;"><b id="step_progress"></b></div>
<div class="progress" style="margin:auto 2px;">
	<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;"></div>
</div>


