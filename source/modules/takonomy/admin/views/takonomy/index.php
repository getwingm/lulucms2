<?php

use yii\helpers\Html;
use source\core\grid\GridView;
use source\LuLu;
use source\core\lib\Common;
use source\models\Takonomy;
use source\models\TakonomyCategory;
use source\libs\Constants;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\TakonomySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$category=LuLu::getGetValue('category');
$categoryModel = TakonomyCategory::findOne(['id'=>$category]);

$this->title=$categoryModel['name'];
$this->addBreadcrumbs([
		['分类管理',['/takonomy']],
		$categoryModel['name'],
]);

?>
<?php $this->toolbars([
    Html::a('返回', ['/takonomy/takonomy-category/index'], ['class' => 'btn btn-xs btn-primary mod-site-save']),
    Html::a('新建', ['create','category'=>$category], ['class' => 'btn btn-xs btn-primary mod-site-save']),
]);?>


 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
              'class'=>'source\core\grid\IdColumn',
            ],     
            [
	
				'attribute'=>'name',
				'format'=>'html',
                'width'=>'auto',
				 'value'=>function ($model,$key,$index,$column){
						return str_repeat(Constants::TabSize, $model->level).Html::a($model->name,['takonomy/update','id'=>$model->id]);
				    }
			],
           
            // 'description',
            // 'contents',
            
            [
            'attribute'=>'url_alias',
                'width'=>'250px',
            ],
            [
                'class'=>'source\core\grid\SortColumn',
            ],
            ['class' => 'source\core\grid\ActionColumn',
				'queryParams'=>['view'=>['category'=>$category]]
            ],
        ],
    ]); ?>

