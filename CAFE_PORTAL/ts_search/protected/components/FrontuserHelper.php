<?php
class FrontuserHelper{
    public static function login($username,$password){
        $user = Frontuser::model()->find('username=:username AND pwd=:pwd',array(':username'=>$username,':pwd'=>$password));
        if($user){
            $user->logintime = time();
            $user->save();
            self::dologin($user->id);
            return true;
        }
        else{
            return false;
        }
    }
    public static function dologin($uid){
        Yii::app()->session['frontuid']=$uid;
    }
    public static function logout(){
        if(self::checkLogin()){
            unset(Yii::app()->session['frontuid']);
        }
    }
    public static function checkLogin(){
        return isset(Yii::app()->session['frontuid']);
    }
    public static function getLoginUser(){
        if(self::checkLogin()){
            $user = Frontuser::model()->findByPk(Yii::app()->session['frontuid']);
            return $user;
        }
        return null;
    }
}
?>