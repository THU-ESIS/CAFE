<?php

$this->pageTitle=Yii::app()->name . ' - My Search';
$this->breadcrumbs=array(
	'My Search',
);
?>

<h1>My Search</h1>

<?php if(Yii::app()->user->hasFlash('my search')): ?>

<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('my search'); ?>
</div>

<?php else: ?>

    我的搜索项目列表

<?php endif; ?>