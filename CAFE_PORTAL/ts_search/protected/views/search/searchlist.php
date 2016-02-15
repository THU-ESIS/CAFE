<div style="padding-top:10px;">
<h1>Data Files</h1>
</div>
<table class="data-table">
	<tr>
		<th>institute</th>
		<th>model</th>
		<th>experiment</th>
		<th>modelingRealm</th>
		<th>variableName</th>
		<th>ensembleMember</th>
		<th>temporalStart</th>
		<th>temporalEnd</th>
		<th></th>
	</tr>
<?php
if($data){
	foreach ($data as $item){
		echo '<tr>';
		echo '<td>'.$item['institute'].'</td>';
		echo '<td>'.$item['model'].'</td>';
		echo '<td>'.$item['experiment'].'</td>';
		echo '<td>'.$item['modelingRealm'].'</td>';
		echo '<td>'.$item['variableName'].'</td>';
		echo '<td>'.$item['ensembleMember'].'</td>';
		echo '<td>'.$item['temporalStart'].'</td>';
		echo '<td>'.$item['temporalEnd'].'</td>';
		echo '<td>'.'<a href="#" data-content=\''.json_encode($item).'\' class="select-link">select</a>'.'</td>';
		echo '</tr>';
	}
}
else{
	echo '<tr>';
	echo '<td colspan="9">No Data</td>';
	echo '</tr>';
}
?>
</table>
<div class="pager">
	<ul id="pagination-digg">
<?php 
$startPage = 0;
if($page>5){
	$startPage = $page - 5;
}
$endPage = $pageCount - 1;
if($endPage > $page +5){
	$endPage = $page +5;
}
if($startPage>0){
	echo '<li><a href="#" data-page="0" class="pager-item">1</a></li>';
	echo '<li><a href="#" data-page="'.($startPage-1).'" class="pager-item">...</a></li>';
}
for ($i=$startPage; $i<=$endPage;$i++){
	if($page == $i){
		echo '<li class="active">'.($i + 1 ).'</li>';
	}
	else{
		echo '<li><a href="#" data-page="'.$i.'" class="pager-item">'.($i + 1 ).'</a></li>';
	}
}
if($endPage<$pageCount-1){
	echo '<li><a href="#" data-page="'.($endPage+1).'" class="pager-item">...</a></li>';
	echo '<li><a href="#" data-page="'.($pageCount-1).'" class="pager-item">'.$pageCount.'</a></li>';
}
?>
	</ul>
</div>
<!--
<div>
<br/>
<br/>
<?php echo $url; ?>
</div>
<div>
<br/>
<?php echo $debugContent; ?>
</div>
  -->