<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\CouponCode;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Models\CouponCode as ModelCouponCode;
use Dcat\Admin\Layout\Content;

class CouponCodesController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new CouponCode(), function (Grid $grid) {
            $grid->model()->orderBy('created_at','desc');
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('code');
            $grid->column('description','描述');
            $grid->column('usage','用量')->display(function($v){
                return $this->used .'/'.$this->total;
            });
            $grid->column('enabled','是否启用')->display(function($v){
                return $v ? '是' : '否';
            });
            $grid->column('created_at')->datetime();
            // 禁用详情按钮
            $grid->disableViewButton();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

/*    public function show($id,Content $content)
    {  
        return $content->header('订单');
    }
*/
    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new CouponCode(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('code');
            $show->field('type');
            $show->field('value');
            $show->field('total');
            $show->field('used');
            $show->field('min_amount');
            $show->field('not_before');
            $show->field('not_after');
            $show->field('enabled');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new CouponCode(), function (Form $form) {
            $form->display('id');
            $form->text('name')->rules('required');
            $form->text('code')->rules(function($form){
                // 如果 $form->model()->id 不为空，代表是编辑操作
                if($id = $form->model()->id){
                    return 'nullable|unique:coupon_codes,code,'.$id.',id';
                }else{
                    return 'nullable|unique:coupon_codes';
                }
            });
            $form->radio('type','类型')->options(ModelCouponCode::$typeMap)->rules('required')->default(ModelCouponCode::TYPE_FIXED);
            $form->text('value','折扣')->rules(function($form){
                if(request()->input('type') === ModelCouponCode::TYPE_PERCENT){
                    // 如果选择了百分比折扣类型，那么折扣范围只能是 1 ~ 99
                    return 'required|numeric|between:1,99';
                }else{
                    // 否则只要大等于 0.01 即可
                    return 'required|numeric|min:0.01';
                }
            });
            $form->text('total','总量')->rules('required|numeric|min:0');
            $form->text('min_amount','使用最低金额')->rules('required|numeric|0');
            $form->datetime('not_before','开始时间');
            $form->datetime('not_after','结束时间');
            $form->radio('enabled','启用')->options(['1'=>'是','0'=>'否'])->rules('required')->default(1);
            
            $form->saving(function(Form $form){
                if(!$form->code){
                    $form->code = ModelCouponCode::findAvailableCode();
                }
            });
        });

    }
}
