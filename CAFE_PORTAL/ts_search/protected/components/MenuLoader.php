<?php
class MenuLoader{
    private static $_usr = null;
    public static function getMainMenu(){
        return array(
				array('label'=>'Home', 'url'=>array('/site/index')),
				array('label'=>'Search', 'url'=>array('/search/form')),
				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>MenuLoader::isGuest()),
        		array('label'=>'Register', 'url'=>array('/site/register'), 'visible'=>MenuLoader::isGuest()),
                array('label'=>'My Tasks', 'url'=>array('/user/index'), 'visible'=>!MenuLoader::isGuest()),
				array('label'=>'Logout ('.self::getUserName().')', 'url'=>array('/site/logout'), 'visible'=>!MenuLoader::isGuest()),
			);
    }
    public static function isGuest(){
        if(!self::$_usr){
            self::$_usr = FrontuserHelper::getLoginUser();
        }
        if(self::$_usr){
            return false;
        }
        else{
            return true;
        }
    }
    public static function getUserName(){
        if(self::isGuest()){
            return 'Guest';
        }
        else{
            return self::$_usr->username;
        }
    }
    public static function getUserId(){
        if(self::isGuest()){
            return '0';
        }
        else{
            return self::$_usr->id;
        }
    }
}
?>