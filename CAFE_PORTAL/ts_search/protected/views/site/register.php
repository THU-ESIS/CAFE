<?php
$this->pageTitle=Yii::app()->name . ' - Register';
$this->breadcrumbs=array(
		'Register',
);
?>
<?php if(Yii::app()->user->hasFlash('register')): ?>

<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('register'); ?>
</div>

<?php endif; ?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'frontuser-login-form-register-form',
    // Please note: When you enable ajax validation, make sure the corresponding
    // controller action is handling ajax validation correctly.
    // See class documentation of CActiveForm for details on this,
    // you need to use the performAjaxValidation()-method described there.
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'username'); ?>
        <?php echo $form->textField($model,'username'); ?>
        <?php echo $form->error($model,'username'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'password'); ?>
        <?php echo $form->textField($model,'password'); ?>
        <?php echo $form->error($model,'password'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'repassword'); ?>
        <?php echo $form->textField($model,'repassword'); ?>
        <?php echo $form->error($model,'repassword'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email'); ?>
        <?php echo $form->error($model,'email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'verifyCode'); ?>
        <?php echo $form->textField($model,'verifyCode',array('class'=>'verify-code-input')); ?>
        <?php $this->widget('CCaptcha',array('showRefreshButton'=>false,'clickableImage'=>true,'imageOptions'=>array('alt'=>'click to refresh','title'=>'click to refresh','style'=>'cursor:pointer;vertical-align:middle;','align'=>"middle"))); ?>
        <?php echo $form->error($model,'verifyCode'); ?>
    </div>


    <div class="row buttons">
        <?php echo CHtml::submitButton('Submit'); ?>
    </div>

<?php $this->endWidget(); ?>

</div>