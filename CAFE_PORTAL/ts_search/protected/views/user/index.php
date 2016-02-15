<?php
$this->pageTitle=Yii::app()->name . ' - My Tasks';
$this->breadcrumbs=array(
		'My Tasks',
);
?>
<style type="text/css">
#sidebar ul{
	list-style-type: none;
	list-style-position: outside;
	padding:0;
	margin:0;
}
#sidebar ul li{
	border-bottom: 1px #C9E0ED dashed;
	margin: 0 5px;
}
#sidebar ul li a{
	text-decoration: none;
	display:block;
	padding: 0 5px;
	line-height:24px;
}
#sidebar ul li a:hover{
	background:#C9E0ED;
}
#sidebar ul li a.current{
	background:#C9E0ED;
	font-weight: bold;
}
.center{
	text-align:center;
}
.task-status{
	display:block;
	line-height:30px;
	-moz-border-radius: 5px;      /* Gecko browsers */
    -webkit-border-radius: 5px;   /* Webkit browsers */
    border-radius:5px; 
	border: 1px solid;
	font-weight: bold;
}
.status-failed{
	background:#FF4500;
	color :#fff;
}
.status-created{
	
}
.status-running{
	background:#FFFF00;
}
.status-finished{
	background:#32CD32;
	color :#fff;
}
</style>
<h1>My Tasks</h1>

<?php if(Yii::app()->user->hasFlash('mytasks')): ?>

<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('mytasks'); ?>
</div>

<?php else: ?>
<!-- 
<div class="span-5">
	<div id="sidebar" style="border-right:1px solid #C9E0ED;">
		<ul>
			<?php
			echo '<li><a href="'.Yii::app()->createUrl('/user/index').'" ';
			if (empty($ncl_type)){
				echo 'class="current"';
			}
			echo '>All Functions</a></li>';
			$ncl_types = EnumData::NCL_Types();
			foreach ($ncl_types as $key=>$value){
				echo '<li><a href="'.Yii::app()->createUrl('/user/index',array('ncl'=>$key)).'" ';
				if ($key == $ncl_type){
					echo 'class="current"';
				}
				echo '>'.$value.'</a></li>';
			}
				?>
		</ul>
	</div>
</div>
 -->
<div class="last">
	<div id="content">

		<table class="data-table" id="mytasks-table">
		<tr>
			<th width='60' class="center"></th>
			<th class="center">Task Name</th>
			<th class="center" width='80' >Create Time</th>
			<th class="center">Params</th>
			<th class="center" width='100' >Status</th>
			<th width='80'></th>
		</tr>
		<TBODY id="mytasks-table-body">
			<?php 
			$ids_arr = array();
			foreach($tasks as $item){
				if($item->status == 0 || $item->status==1){
					$ids_arr[] = $item->task_id;
				}
				echo '<tr>';
				echo '<td id="item_'.$item->id.'_status" class="center">';
				echo '<span class="task-status status-'.$statusList[$item->status].'">'.$statusList[$item->status].'</span>';
				echo '</td>';
				echo '<td>';
				echo $item->task_name;
				echo '</td>';
				echo '<td>';
				if($item->task_name && $item->create_time){
					echo date('Y-m-d H:i:s',$item->create_time);
				}
				echo '</td>';
				echo '<td>';
				$data = json_decode($item->params,true);
				?>
			<div>
				<B>Function name:</B> <?php echo $item->ncl_name;?>
			</div>
			<div>
				<B>temporalStart:</B> <?php echo $data['nclScript']['temporalStart'];?> / 
				<B>temporalEnd:</B> <?php echo $data['nclScript']['temporalEnd'];?>
			</div>
			<div>
				<B>latMin:</B> <?php echo $data['nclScript']['latMin'];?> / <B>latMax:</B> <?php echo $data['nclScript']['latMax'];?>
			</div>
			<div>
				<B>lonMin:</B> <?php echo $data['nclScript']['lonMin'];?> / <B>lonMax:</B> <?php echo $data['nclScript']['lonMax'];?>
			</div>
			<?php 
				echo '</td>';
				echo '<td><span id="item_process_'.$item->id.'">'.$statusList[$item->status].'</span></td>';
				echo '<td id="item_'.$item->id.'_action" class="center">';
				echo CHtml::link('Detail',array('/user/task','id'=>$item->id));
				'</td>';
				echo '</tr>';
			}
			?>
		</TBODY>
		</table>
	</div>
</div>
<script type="text/javascript">
function load_this_list_status(){
	<?php if($ids_arr){?>
	var ids = '<?php echo implode(',', $ids_arr)?>';
	$.ajax({
		type: "POST",
        url: "<?php echo Yii::app()->createUrl('user/ajaxGetStatus')?>",
        data: {'tasks':ids},
        dataType: "json",
        success: function(data){
            if(data){
            	for(var id in data){
                	var text = '';
                	switch(data[id]){
                		case '-1':
                		case -1:
                    		text = 'failed';
                    		break;
                		case '0':
                    	case 0:
                    		text = 'created';
                    		break;
                    	case '1':
                        case 1:
                    		text = 'running';
                    		break;
                    	case '2':
                        case 2:
                    		text = 'finished';
                    		break;
                    	default:
                        	text = 'unknow';
                        	break;
                	}
                	var html = '<span class="task-status status-'+text+'">'+text+'</span>'
                	$('#item_'+id+'_status').html(html);
                	$('#item_process_'+id).html(text);
            	}
            	setTimeout('load_this_list_status();',8000);
            }
        },
        error:function(){
            alert('server error.');
        }
	});
	<?php }?>
}
			
$(document).ready(function(){
	load_this_list_status();
});
</script>
<?php endif;?>