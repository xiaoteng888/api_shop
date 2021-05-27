<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Category;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Illuminate\Http\Request;
use App\Models\Category as modelCategory;

class CategoryController extends AdminController
{
    protected $title = '商品类目';
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Category(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('is_directory','是否目录')->display(function($v){
                return $v ? '是' : '否';
            });
            $grid->column('level','层级');
            $grid->column('path','类目路径');
            $grid->disableViewButton();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    public function edit($id,Content $content)
    {
        return $content
             ->title($this->title())
             ->description($this->description['edit'] ?? trans('admin.edit'))
             ->body($this->form(true)->edit($id));
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
        return Show::make($id, new Category(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('parent_id');
            $show->field('is_directory');
            $show->field('level');
            $show->field('path');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($isEditing = false)
    {
        return Form::make(Category::with('parent'), function (Form $form)use($isEditing) {
            $form->display('id');
            $form->text('name','类目名称')->rules('required');
            // 如果是编辑的情况
            if($isEditing){
                // 不允许用户修改『是否目录』和『父类目』字段的值
            // 用 display() 方法来展示值，with() 方法接受一个匿名函数，会把字段值传给匿名函数并把返回值展示出来
                $form->display('is_directory','是否目录')->with(function($v){
                    return $v ? '是' : '否';
                });
                $form->display('parent.name','父类目');
            }else{
                $form->radio('is_directory','是否目录')->options(['1'=>'是','0'=>'否'])->default('0')->rules('required');
                // 定义一个名为父类目的下拉框
                $form->select('parent_id','父类目')->ajax('api/categories');
            }

        });
    }

    public function apiIndex(Request $request)
    {
        // 用户输入的值通过 q 参数获取
        $search = $request->input('q');
        $result = modelCategory::query()
                  ->where('is_directory',true)
                  ->where('name','like','%'.$search.'%')
                  ->paginate();
                  
        // 把查询出来的结果重新组装成需要的格式
        $result->setCollection($result->getCollection()->map(function(modelCategory $category){
            return ['id' => $category->id, 'text' => $category->full_name];
        }));
        
        return $result;        
    }
}
