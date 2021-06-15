<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Product;
use App\Models\Product as modelProduct;
use App\Models\CrowdfundingProduct;
use App\Models\Category;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class CrowdfundingProductsController extends AdminController
{
    protected $title = '众筹商品';
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Product(['crowdfunding']), function (Grid $grid) {
            // 只展示 type 为众筹类型的商品
            $grid->model()->where('type',modelProduct::TYPE_CROWDFUNDING);
            $grid->id('ID')->sortable();
            $grid->column('title','商品名称');
            $grid->column('on_sale','已上架')->display(function($v){
                return $v ? '是' : '否';
            });
            $grid->price('价格');
            // 展示众筹相关字段
            $grid->column('crowdfunding.target_amount','目标金额');
            $grid->column('crowdfunding.total_amount','目前金额');
            //$grid->column('crowdfunding.user_count');
            $grid->column('crowdfunding.end_at','结束时间');
            $grid->column('crowdfunding.status','状态')->display(function($v){
                return CrowdfundingProduct::$statusMap[$v];
            });
            $grid->disableViewButton();
            $grid->disableDeleteButton();
            $grid->disableBatchDelete();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new CrowdfundingProduct(), function (Show $show) {
            $show->field('id');
            $show->field('product_id');
            $show->field('target_amount');
            $show->field('total_amount');
            $show->field('user_count');
            $show->field('end_at');
            $show->field('status');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Product(['crowdfunding','skus']), function (Form $form) {
            // 在表单中添加一个名为 type，值为 Product::TYPE_CROWDFUNDING 的隐藏字段
            $form->hidden('type')->value(modelProduct::TYPE_CROWDFUNDING);
            $form->text('title','商品名称')->rules('required');
            $form->select('category_id','类目')->options(function($id){
                $category = Category::find($id);
                if($category){
                    return [$category->id => $category->full_name];
                }
            })->ajax('/api/categories?is_directory=0');
            $form->image('image','封面图片')->rules('required|image');
            $form->editor('description','商品描述')->rules('required');
            $form->radio('on_sale','上架')->options(['1'=>'是','0'=>'否'])->default('0');
            // 添加众筹相关字段
            $form->text('crowdfunding.target_amount','众筹目标金额')->rules('required|numeric|min:0.01');
            $form->datetime('crowdfunding.end_at','众筹结束时间')->rules('required|date');
            $form->hasMany('skus','商品SKU',function(Form\NestedForm $form){
                $form->text('title','SKU名称')->rules('required');
                $form->text('description','SKU描述')->rules('required');
                $form->text('price','单价')->rules('required|numeric|min:0.01');
                $form->text('stock','剩余库存')->rules('required|integer|min:0');
            });
            //$form->display('price');
            $form->saving(function(Form $form){
                $form->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME,0)->min('price');
            });
        });
    }
}
