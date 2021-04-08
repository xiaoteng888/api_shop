<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Users;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class UsersController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Users(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('email');
            $grid->column('email_verified_at')->display(function($v){
                return $v ? '是' : '否';
            });
            $grid->column('created_at')->format();
            $grid->column('updated_at')->format()->sortable();
            // 不在页面显示 `新建` 按钮，因为我们不需要在后台新建用户
            $grid->disableCreateButton();
            // 同时在每一行也不显示 `编辑` 按钮
            $grid->disableActions();

            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
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
        return Show::make($id, new Users(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('email');
            $show->field('email_verified_at');
            $show->field('password');
            $show->field('remember_token');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    /*protected function form()
    {
        return Form::make(new Users(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('email');
            $form->text('email_verified_at');
            $form->text('password');
            $form->text('remember_token');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }*/
}
