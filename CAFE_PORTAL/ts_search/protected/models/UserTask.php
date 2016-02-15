<?php

/**
 * This is the model class for table "{{user_task}}".
 *
 * The followings are the available columns in table '{{user_task}}':
 * @property integer $id
 * @property integer $user_id
 * @property string $task_id
 * @property integer $create_time
 * @property string $task_name
 * @property string $ncl_name
 * @property string $params
 * @property integer $status
 */
class UserTask extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_task}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, task_id, create_time, task_name, ncl_name', 'required'),
			array('user_id, create_time, status', 'numerical', 'integerOnly'=>true),
			array('task_name', 'length', 'max'=>255),
			array('ncl_name, task_id', 'length', 'max'=>50),
			array('params', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, task_id, create_time, task_name, ncl_name, params, status', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'task_id' => 'Task',
			'create_time' => 'Create Time',
			'task_name' => 'Task Name',
			'ncl_name' => 'Ncl Name',
			'params' => 'Params',
			'status' => 'Status',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('task_id',$this->task_id,true);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('task_name',$this->task_name,true);
		$criteria->compare('ncl_name',$this->ncl_name,true);
		$criteria->compare('params',$this->params,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function scopes()
	{
		return array(
				'bytime'=>array(
						'order'=>'create_time DESC',
				),
		);
	}
	
}
