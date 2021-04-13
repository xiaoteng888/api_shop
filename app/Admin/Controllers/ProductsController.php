<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Product;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ProductsController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Product(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('title');
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
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    /*protected function detail($id)
    {
        return Show::make($id, new Product(), function (Show $show) {
            $show->field('id');
            $show->field('title');
            $show->field('description');
            $show->field('image');
            $show->field('on_sale');
            $show->field('rating');
            $show->field('sold_count');
            $show->field('review_count');
            $show->field('price');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }*/

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Product('skus'), function (Form $form) {
            $form->display('id');
            $form->text('title');
            $form->editor('description');
            $form->image('image');
            $form->radio('on_sale')->options(['1'=>'是','0'=>'否'])->default('0');
            $form->hasMany('skus','SKU 列表',function(Form\NestedForm $form){
                $form->text('title','SKU 名称');
                $form->text('description', 'SKU 描述')->rules('required');
                $form->text('price', '单价')->rules('required|numeric|min:0.01');
                $form->text('stock', '剩余库存')->rules('required|integer|min:0');
            });
            // 定义事件回调，当模型即将保存时会触发这个回调
            $form->saving(function(Form $form){
                $form->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME,0)->min('price') ? : 0;
            });
            
        });
    }
}