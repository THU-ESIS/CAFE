<?php

$this->pageTitle=Yii::app()->name . ' - Search';
$this->breadcrumbs=array(
	'Search',
);
?>

<h1>Search</h1>

<?php if(Yii::app()->user->hasFlash('search')): ?>

<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('search'); ?>
</div>

<?php else: ?>

    提交和搜索页面

<?php endif; ?>