<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Http\Queries\CartItemQuery;
use App\Services\CartService;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\UserResource;

class CartController extends Controller
{
    protected $cartService;

    // 利用 Laravel 的自动解析功能注入 CartService 类
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function add(AddCartRequest $request)
    {
        $skuId = $request->input('sku_id');
        $amount = $request->input('amount');
        // 从数据库中查询该商品是否已经在购物车中
        $item = $this->cartService->add($skuId,$amount);
        return new CartItemResource($item);
    }

    public function index(Request $request,CartItemQuery $query)
    {
        $cartItems = $query->where('user_id',$request->user()->id)->get();
        return CartItemResource::collection($cartItems);
    }

    public function remove($sku = null)
    {
        $this->cartService->remove($sku);
        return response(null,204);
    }
}
