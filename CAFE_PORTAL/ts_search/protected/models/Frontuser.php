<?php

/**
 * This is the model class for table "{{frontuser}}".
 *
 * The followings are the available columns in table '{{frontuser}}':
 * @property integer $id
 * @property string $username
 * @property string $pwd
 * @property string $email
 * @property integer $status
 * @property integer $regtime
 * @property integer $logintime
 */
class Frontuser extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Frontuser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{frontuser}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, pwd, email', 'required','message'=>'{attribute}不能为空'),
			array('status, regtime, logintime', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>100),
			array('pwd', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, pwd, status, regtime, logintime', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => '用户名',
			'pwd' => '密码',
            'email' => '邮箱',
			'status' => '可用',
			'regtime' => '注册时间',
			'logintime' => '登录时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('pwd',$this->pwd,true);
        $criteria->compare('email',$this->email,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('regtime',$this->regtime);
		$criteria->compare('logintime',$this->logintime);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}