<?php

namespace source\models;

use Yii;
use source\libs\TreeHelper;
use source\libs\Constants;

/**
 * This is the model class for table "{{%takonomy}}".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $url_alias
 * @property string $description
 * @property integer $contents
 * @property integer $sort_num
 * @property string $category_id
 */
class Takonomy extends \source\core\base\BaseActiveRecord
{
	
    public static function getOneOrDefault($takonomy)
    {
        $takonomyModel=Takonomy::findOne(['id'=>$takonomy]);
        
        if($takonomyModel===null)
        {
            $takonomyModel=['id'=>null,'name'=>'所有'];
            
        }
       return $takonomyModel;
    }
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%takonomy}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'parent_id', 'name', 'sort_num'], 'required'],
            [['parent_id', 'contents', 'sort_num'], 'integer'],
            [['name', 'url_alias','category_id'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'parent_id' => '父结点',
            'category_id' => '分类',
            'name' => '名称',
            'url_alias' => '别名',
            'description' => '描述',
            'contents' => '内容数量',
            'sort_num' => '排序',
        ];
    }
    
    private $_level;
    public function getLevel()
    {
    	return $this->_level;
    }
    public function setLevel($value)
    {
    	$this->_level = $value;
    }
    
    public function getLevelName()
    {
    	return str_repeat(Constants::TabSize, $this->level).$this->name;
    }
    
    private $_parentIds;
    public function getParentIds()
    {
        if($this->_parentIds===null)
        {
            $this->_parentIds=TreeHelper::getParentIds(Menu::className(), $this->parent_id);
        }
        return $this->_parentIds;
    }
    
    private $_childrenIds;
    public function getChildrenIds()
    {
        if($this->_childrenIds===null)
        {
            $this->_childrenIds= TreeHelper::getChildrenIds(Menu::className(), $this->id);
        }
        return $this->_childrenIds;
    }
    
    
    private static function getArrayTreeInternal($category, $parentId = 0, $level = 0)
    {
    	$takonomies = self::findAll(['category_id'=>$category,'parent_id'=>$parentId],'sort_num asc');
    	
    	$dataList=[];
    	foreach ($takonomies as $takonomy)
    	{
    		$takonomy->level=$level;
    		$dataList[$takonomy['id']]=$takonomy;
    		$temp = self::getArrayTreeInternal($category,$takonomy->id, $level + 1);
    		$dataList = array_merge($dataList, $temp);
    	}
    	
    	return $dataList;
    }
    
    public static function getArrayTree($category)
    {
    	return self::getArrayTreeInternal($category,0,0);
    }
    
    public function beforeDelete()
    {
        $childrenIds = $this->getChildrenIds();
        self::deleteAll(['id'=>$childrenIds]);
        return true;
    }
}
