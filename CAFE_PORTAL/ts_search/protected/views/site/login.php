<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<h1>Login</h1>

<?php if(Yii::app()->user->hasFlash('login')): ?>

<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('login'); ?>
</div>

<?php else: ?>

<div id="login-mainbox">
    <div id="login-form-outer-box">
    <?php
    	$form = $this->beginWidget('CActiveForm', array(    
            'id'=>'login-form',    
            'enableAjaxValidation'=>true,    
            'enableClientValidation'=>true,
        ));
        $loginModel = new FrontuserLoginForm;
    ?>
        <div id="login-form-inner-box">
            <div class="centerDiv" id="login-form-title"></div>
            <div style="padding:0 30px;">
                <div class="form-line">
    			User name:
    				<div class="inputContainer">
                        <?php echo $form->textField($loginModel,'username',array('class'=>'loginInput')); ?>
    				</div>
    			</div>
    			<div class="form-line">
    			Password:
    				<div class="inputContainer">
                        <?php echo $form->passwordField($loginModel,'password',array('class'=>'loginInput')) ?>
    				</div>
    			</div>
    			<div class="form-line">
    			Security code:
    				<div class="inputContainer">
                        <?php echo $form->textField($loginModel,'verifyCode',array('class'=>'loginCodeInput')); ?>
                        <?php $this->widget('CCaptcha',array('showRefreshButton'=>false,'clickableImage'=>true,'imageOptions'=>array('alt'=>'click to refresh','title'=>'click to refresh','style'=>'cursor:pointer;vertical-align:bottom;','align'=>"middle"))); ?>
                        　　
    				</div>
    			</div>
            </div>
            
        </div>
        <div id="login-btn-box">
            <div style="text-align: center; line-height: 50px; padding-top:15px;">
                <?php echo CHtml::submitButton('Login',array('align'=>'middle','style'=>"vertical-align:middle; width:105px; color:#025691 ; line-height:50px; height:50px;",'class'=>"btn")); ?>
&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                    echo CHtml::link('Register',array('site/register'));
                ?>
            </div>
            
        </div>
    <?php
    	$this->endWidget(); 
    ?>
    </div>
    <div style="clear:both;"></div>
</div>
<div style="clear:both;"></div>
<!-- form -->

<?php endif; ?>