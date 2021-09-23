<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Http\Resources\ProductResource;
use App\Http\Resources\OrderItemResource;
use App\Services\CategoryService;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\FavoriteResource;
use App\Exceptions\InvalidRequestException;
use App\Http\Queries\ProductQuery;

class ProductsController extends Controller
{
    public function index(Request $request,ProductQuery $query)
    {
        //ProductResource::wrap('data');//不分页获取所有数据的时候增加data和分页保持一致
        //创建一个查询构建器
        $builder = $query->where('on_sale',true);
        
        // 判断是否有提交 search 参数，如果有就赋值给 $search 变量
        // search 参数用来模糊搜索商品
        if($search = $request->input('search','')){
            $like = '%'.$search.'%';
            // 模糊搜索商品标题、商品详情、SKU 标题、SKU描述
            $builder->where(function($query) use($like){
                $query->where('title','like',$like)
                ->orWhere('description','like',$like)
                ->orWhereHas('skus',function($query) use($like){
                      $query->where('title','like',$like)
                      ->orWhere('description','like',$like);
                });
            });
        }
        // 如果有传入 category_id 字段，并且在数据库中有对应的类目
        if($request->input('category_id') && $category = Category::find($request->input('category_id'))){
            // 如果这是一个父类目
            if($category->is_directory){
                $builder->whereHas('category',function($query) use($category){
                    $query->where('path','like',$category->path.$category->id.'-%');
                });
            }else{
                $builder->where('category_id',$category->id);
            }
        }

        // 是否有提交 order 参数，如果有就赋值给 $order 变量
        // order 参数用来控制商品的排序规则
        if($order = $request->input('order','')){
            // 是否是以 _asc 或者 _desc 结尾
            if(preg_match('/^(.+)_(asc|desc)$/', $order, $m)){
                // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
                if(in_array($m[1], ['price','sold_count','rating'])){
                    $builder->orderBy($m[1],$m[2]);
                }
            }
        }

        $products = $builder->paginate(16);
        
        return ProductResource::collection([
               'products' => $products,
               'filters'=>[
                   'search' => $search,
                   'order' => $order,
                ],
               'category' => $category ?? null,
        ]);
    }

    public function categories(Request $request,CategoryService $service)
    {
        if($request->parent_id && !intval($request->parent_id)){
            throw new InvalidRequestException('分类ID请输入整数',401);
        }
        $categories = $service->getCategoryTree($request->parent_id);
        CategoryResource::wrap('data'); 
        return CategoryResource::collection($categories);
    }

    public function show($product_id,Request $request,ProductQuery $query)
    {
        $product = Product::find($product_id);
        //判断商品是否已经上架，如果没有上架则抛出异常。
        if(!$product->on_sale){
            throw new InvalidRequestException("商品未上架",401); 
        }
        
        $product = $query->findOrFail($product_id);
        return new ProductResource($product);
    }

    public function favor(Product $product,Request $request)
    {
        $user = $request->user();
        if($user->favoriteProducts()->find($product->id)){
            abort(403,'已经收藏过了');
        }
        $user->favoriteProducts()->attach($product);
        return response()->json(['msg' => '收藏成功'])->setStatusCode(201);
    }

    public function disfavor(Product $product,Request $request)
    {
        $user = $request->user();
        if($user->favoriteProducts()->find($product->id)){
            $user->favoriteProducts()->detach($product);
            return response()->json(['msg' => '取消收藏成功'])->setStatusCode(200);
        }
        abort(403,'没有收藏该商品');
    }

    public function favorites(Request $request)
    {
        $favorites = $request->user()->favoriteProducts()->paginate();
        return FavoriteResource::collection($favorites);
    }
}
