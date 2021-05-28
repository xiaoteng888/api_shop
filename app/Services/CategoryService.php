<?php
namespace App\Services;

use App\Models\Category;


class CategoryService
{
	// 这是一个递归方法
    // $parentId 参数代表要获取子类目的父类目 ID，null 代表获取所有根类目
    // $allCategories 参数代表数据库中所有的类目，如果是 null 代表需要从数据库中查询
    public function getCategoryTree($parentId = null,$categories = null)
    {
    	if(is_null($categories)){
    		$categories = Category::all();
    	}
    	return $categories
    	       ->where('parent_id',$parentId)
    	       ->map(function(Category $category) use($categories){
	    	       	$data = ['id' => $category->id,'name'=>$category->name];
	    	       	if(!$category->is_directory){
	    	       		return $data;
	    	       	}
    	       	    $data['children'] = $this->getCategoryTree($category->id,$categories);
    	       	    return $data;
    	       });
    }
}