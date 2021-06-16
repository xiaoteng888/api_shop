<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use App\Models\Product as modelProduct;

class ProductsController extends CommonProductsController
{
    
    public function getProductType()
    {
        return modelProduct::TYPE_NORMAL;
    }

    protected function customGrid(Grid $grid)
    {
        $grid->model()->with(['category']);
        $grid->column('id')->sortable();
        $grid->column('title');
        $grid->column('category.name','类目');
        $grid->column('on_sale')->display(function ($v){
            return $v ? '是' : '否';
        });
        $grid->column('price');
        $grid->column('rating');
        $grid->column('sold_count');
        $grid->column('review_count');
    }

    protected function customForm(Form $form)
    {
        // 普通商品没有额外的字段，因此这里不需要写任何代码
    } 

    /*protected function grid()
    {
        return Grid::make(new Product(), function (Grid $grid) {
            $grid->model()->where('type',modelProduct::TYPE_NORMAL)->with(['category']);
            $grid->column('id')->sortable();
            $grid->column('title');
            $grid->column('category.name','类目');
            $grid->column('on_sale');
            $grid->column('price');
            $grid->column('rating');
            $grid->column('sold_count');
            $grid->column('review_count');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('title');
            });
            $grid->disableViewButton();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
 /*   protected function form()
    {
        return Form::make(new Product('skus'), function (Form $form) {
            $form->display('id');
            // 在表单中添加一个名为 type，值为 Product::TYPE_NORMAL 的隐藏字段
            $form->hidden('type')->value(modelProduct::TYPE_NORMAL);
            $form->text('title');
            // 添加一个类目字段，与之前类目管理类似，使用 Ajax 的方式来搜索添加
            $form->select('category_id','类目')->options(function($id){
                $category = Category::find($id);
                if($category){
                    return [$category->id => $category->full_name];
                }
            })->ajax('/api/categories?is_directory=0');
            $form->editor('description')->rules('required');
            $form->image('image')->removable(false)->rules('required');
            $form->radio('on_sale')->options(['1'=>'是','0'=>'否'])->default('0');
            $form->hasMany('skus','SKU 列表',function(Form\NestedForm $form){
                $form->text('title','SKU 名称');
                $form->text('description', 'SKU 描述')->rules('required');
                $form->text('price', '单价')->rules('required|numeric|min:0.01');
                $form->text('stock', '剩余库存')->rules('required|integer|min:0');
            });
            //$form->display('price');
            // 定义事件回调，当模型即将保存时会触发这个回调
            $form->saving(function(Form $form){
                $form->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
                //dd($form->input());
            });
            
        });
    }*/
}
