<?php
class FrontuserLoginForm extends CFormModel
{
    public $username;
    public $password;
    public $verifyCode;
    public $email;
    public $repassword;
    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            array('username, password, verifyCode', 'required', 'on'=>'login'),
            array('username, password, repassword, email, verifyCode', 'required', 'on'=>'register'),
        	array('repassword', 'checkRepwd', 'on'=>'register'),
            //array('verifyCode', 'captcha','on'=>'login', 'allowEmpty'=>!CCaptcha::checkRequirements()),//校验验证码是否输入正确
            //array('verifyCode', 'captcha','on'=>'register', 'allowEmpty'=>!CCaptcha::checkRequirements()),//校验验证码是否输入正确
        );
    }
 
    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'username'=>'User name',
            'password'=>'Password',
            'verifyCode'=>'Verify code',
            'email'=>'Email',
            'repassword'=>'Repeat password',
        );
    }
    
    public function login(){
        return FrontuserHelper::login($this->username,$this->password);
    }
    
    public function checkRepwd($attribute,$params)
    {
    	if(empty($this->repassword) || empty($this->password)){
    		$this->addError('repassword','Password and Repeat Password is required.');
    		return;
    	}
    	if($this->repassword != $this->password){
    		$this->addError('repassword','Repeat Password is not the same with Password.');
    	}
    }
}
?>