<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0x4c5daf,
                'foreColor'=>0x44bec1,
                'maxLength'=>'6',       // 最多生成几个字符
                'minLength'=>'4',       // 最少生成几个字符
                'height'=>'22',
                'width'=>'60',
                'padding'=>'0',
                'testLimit'=>4,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		
        Yii::app()->getClientScript()->registerCssFile(Yii::app()->request->baseUrl."/css/login.css");
		// if it is ajax validation request
		//$this->layout = 'main-noscreen';
		// collect user input data
		$this->logincheck();
        
		$this->render('login');
	}
	
	public function actionRegister(){
		$tpl_data = array();
		$model = new FrontuserLoginForm('register'); 
		$tpl_data['model'] = $model;
		
		if(isset($_POST['FrontuserLoginForm'])){
			$model->attributes=$_POST['FrontuserLoginForm'];
			if(!$this->createAction('captcha')->validate($model->verifyCode,false)){
				$this->createAction('captcha')->getVerifyCode(true);
				Yii::app()->user->setFlash('register','Verify Code Error');
				$this->render('register',$tpl_data);
				return;
			}
			$this->createAction('captcha')->getVerifyCode(true);
			if($model->validate()){
				$user = new Frontuser();
				$user->username = $model->username;
				$user->pwd = $model->password;
				$user->email = $model->email;
				$user->status = 1;
				$user->regtime = time();
				$user->logintime = time();
				$user->save(false);
				FrontuserHelper::login($model->username,$model->password);
				$this->redirect(array('user/index'));
			}
		}
		
		$this->render('register',$tpl_data);
	}
	
    public function logincheck(){
        $model=new FrontuserLoginForm('login');

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
        if(isset($_POST['FrontuserLoginForm']))
		{
            //$code = $this->createAction('captcha')->getVerifyCode();
            //$this->createAction('captcha')->validate();
			$model->attributes=$_POST['FrontuserLoginForm'];//print_r($model->attributes);echo $model->username.' '.$model->verifyCode.' ';
			// validate user input and redirect to the previous page if valid
            if(!$this->createAction('captcha')->validate($model->verifyCode,false)){
                $this->createAction('captcha')->getVerifyCode(true);
                Yii::app()->user->setFlash('login','Verify Code Error');
                return;
            }
            $this->createAction('captcha')->getVerifyCode(true);
			if($model->validate() && $model->login()){
                
                $this->redirect(Yii::app()->user->returnUrl);
                Yii::app()->end();
			}else{
                Yii::app()->user->setFlash('login','User name OR Password Error.');
			}
				
		}
    }

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		FrontuserHelper::logout();
		$this->redirect(Yii::app()->homeUrl);
	}
    
    public function actionSearch(){
        
        $this->render('search');
    }
    public function actionMySearch(){
        
        $this->render('mysearch');
    }
}