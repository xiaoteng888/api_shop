<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use App\Models\Category;
use App\Admin\Repositories\Product;

abstract class CommonProductsController extends AdminController
{
	// 定义一个抽象方法，返回当前管理的商品类型
	abstract function getProductType();

	protected function grid()
	{
		$grid = new Grid(new Product());
		// 筛选出当前类型的商品，默认 ID 倒序排序
		$grid->model()->where('type',$this->getProductType())->orderBy('id','desc');
		//调用自定义方法
		$this->customGrid($grid);

		$grid->disableViewButton();
        $grid->disableDeleteButton();
        $grid->disableBatchDelete();
        return $grid;
	}

	// 定义一个抽象方法，各个类型的控制器将实现本方法来定义列表应该展示哪些字段
	abstract protected function customGrid(Grid $grid);

	protected function form()
    {
    	$form = new Form(new Product(['skus','crowdfunding']));
    	// 在表单页面中添加一个名为 type 的隐藏字段，值为当前商品类型
    	$form->hidden('type')->value($this->getProductType());
    	$form->text('title','商品名称')->rules('required');
    	$form->select('category_id','类目')->options(function ($id){
    		$category = Category::find($id);
    		if($category){
    			return [$category->id => $category->full_name];
    		}
    	})->ajax('/api/categories?is_directory=0');
    	$form->image('image','封面图片')->rules('required|image');
        $form->editor('description','商品描述')->rules('required');
        $form->radio('on_sale','上架')->options(['1'=>'是','0'=>'否'])->default('0');
        // 调用自定义方法
        $this->customForm($form);
        $form->hasMany('skus','SKU 列表',function(Form\NestedForm $form){
                $form->text('title','SKU 名称');
                $form->text('description', 'SKU 描述')->rules('required');
                $form->text('price', '单价')->rules('required|numeric|min:0.01');
                $form->text('stock', '剩余库存')->rules('required|integer|min:0');
            });
        $form->saving(function (Form $form){
        	$form->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME,0)->min('price') ? : 0;
        });
        return $form;
    }

    // 定义一个抽象方法，各个类型的控制器将实现本方法来定义表单应该有哪些额外的字段
    abstract protected function customForm(Form $form);
}