<?php
$this->pageTitle=Yii::app()->name . ' - My Tasks';
$this->breadcrumbs=array(
		'My Tasks',
);
?>
<style type="text/css">
.field b{
	display:inline-block;
	width:100px;
	text-align:right;
	padding-right: 10px;
}
.error-info{
	padding: 10px 20px;
}
.error-info strong{
	font-size: 18px;
	line-height: 40px;
}
.img-box{
	
}
.img-box img{
	border: 1px solid #ccc;
	margin: 5px;
	padding: 1px;
}
.img-box img.small{
	max-width: 200px;
}
.img-box img.big{
	max-width: 890px;
}
.down-box{
	
}
.down-box a{
	display: inline-block;
	text-decoration: none;
	padding: 5px 20px;
	border: 1px solid #ccc;
	margin: 5px;
}
</style>
<script type="text/javascript">
function change_img_size(obj){
	var img = $(obj).find('img');
	if(img.hasClass('small')){
		img.removeClass('small').addClass('big');
	}
	else{
		img.removeClass('big').addClass('small');
	}
}
</script>
<h1>
Task : <?php echo $userTask->task_name;?>
</h1>

<table class="data-table" id="selected-table">
	<tr>
		<th></th>
		<th>institute</th>
		<th>model</th>
		<th>experiment</th>
		<th>modelingRealm</th>
		<th>variableName</th>
		<th>ensembleMember</th>
		<th></th>
	</tr>
	<TBODY id="selected-table-body">
		<?php 
		$params = json_decode($userTask->params,true);
		$counter = 0;
		foreach($params['models'] as $item){
			$counter ++ ;
			echo '<tr>';
			echo '<td>'.$counter."</td>";
			echo '<td>'.$item['institute']."</td>";
			echo '<td>'.$item['model']."</td>";
			echo '<td>'.$item['experiment']."</td>";
			echo '<td>'.$item['modelingRealm']."</td>";
			echo '<td>'.$item['variableName']."</td>";
			echo '<td>'.$item['ensembleMember']."</td>";
			echo '</tr>';
		}
		?>
	</TBODY>
</table>
<div class="field">
	<b>Function:</b>
	<?php
	$ncl_types = EnumData::NCL_Types();
	echo $ncl_types[$userTask->ncl_name];?>
</div>
<div class="field">
	<b>Time:</b>
	<?php
	echo $params['nclScript']['temporalStart'].' - '.$params['nclScript']['temporalEnd'];?>
</div>
<div class="field">
	<b>Spatial Ranger:</b>
	<?php
	echo 'South '.$params['nclScript']['latMin'].' / North '.$params['nclScript']['latMax'] . ' / West ' . $params['nclScript']['lonMin'].' / East '.$params['nclScript']['lonMax'];?>
</div>
<div class="field">
	<b>Status:</b>
	<?php
	echo $statusList[$userTask->status];?>
</div>
<?php 
if(isset($arrTaskResult) && $arrTaskResult){
$taskData = $arrTaskResult['data'];
	$sub_task_counter=0;
	foreach($taskData as $data):
	?>
	<div class="field">
		<div class="field-title">
		<FONT color="red">Sub-Task<?php echo ++$sub_task_counter;?></FONT>
		<?php echo 'Model: '.$data['model']['model'].'  EnsembleMember: '.$data['model']['ensembleMember'];?>
		</div>
		<?php 
		if($data['status'] == 'finished'){
			$figs = array();
			$ncs = array();
			$txts = array();
			foreach ($data['resultFile'] as $file){
				switch ($file['type']){
					case 'fig':
						$figs[] = $file['url'];
						break;
					case 'nc':
						$ncs[] = $file['url'];
						break;
					case 'txt':
						$txts[] = $file['url'];
						break;
				}
			}
			if($figs){
				echo '<div class="img-box">';
				foreach ($figs as $url){
					echo '<a href="'.$url.'" onclick="change_img_size(this);return false;"><img src="'.$url.'" class="small"></a>';
				}
				echo '</div>';
			}
			if($ncs || $txts){
				echo '<div class="down-box">';
				$i = 0;
				foreach ($ncs as $url){
					$i++;
					echo '<a href="'.$url.'" target="_blank">Nc Result Download</a>';
				}
				foreach ($txts as $url){
					$i++;
					echo '<a href="'.$url.'" target="_blank">txt Result Download</a>';
				}
				echo '</div>';
			}
		}
		else{
			echo '<div class="error-info">';
			echo '<strong>'.$data['status'].'</strong><br/>';
			echo isset($data['failureCause'])?$data['failureCause']:'';
			echo '</div>';
		}
		?>
	</div>
	<?php 
	endforeach;
}?>
<?php 
if($userTask->status == 0 || $userTask->status == 1):
?>
<script type="text/javascript">
function load_this_status(){
	var ids = <?php echo $userTask->task_id;?>;
	$.ajax({
		type: "POST",
        url: "<?php echo Yii::app()->createUrl('user/ajaxGetStatus');?>",
        data: {'tasks':ids},
        dataType: "json",
        success: function(data){
            if(data && data.length>0 && (data[ids]==0 || data[ids]==1)){
                if(data.length>0 && (data[ids]==-1 || data[ids]==2)){
                	alert('Task status changed.');
                	location.reload();
                }
                else{
            		setTimeout('load_this_status();',10000);
                }
            }
            else{
            	setTimeout('load_this_status();',10000);
            }
        },
        error:function(){
            //alert('server error.');
        }
	});
}

$(document).ready(function(){load_this_status();});
</script>
<?php 
endif;
?>