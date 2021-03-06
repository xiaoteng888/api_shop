<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\ProductSku;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ProductSkusController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ProductSku(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('title');
            $grid->column('description');
            $grid->column('price');
            $grid->column('stock');
            $grid->column('product_id');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
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
        return Show::make($id, new ProductSku(), function (Show $show) {
            $show->field('id');
            $show->field('title');
            $show->field('description');
            $show->field('price');
            $show->field('stock');
            $show->field('product_id');
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
        return Form::make(new ProductSku(), function (Form $form) {
            $form->display('id');
            $form->text('title');
            $form->text('description');
            $form->text('price');
            $form->text('stock');
            $form->text('product_id');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
