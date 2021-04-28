<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use App\Models\Order;
use App\Services\OrderService;

class OrdersController extends Controller
{
    public function store(OrderRequest $request,OrderService $orderService)
    {
    	$user = $request->user();
        $remark = $request->input('remark');
        $address = UserAddress::find($request->input('address_id'));
        $items = $request->input('items');
        return $orderService->store($user,$address,$remark,$items);
    }

    public function index(Request $request)
    {
    	$orders = Order::query()->with(['items.product','items.productSku'])->where('user_id',$request->user()->id)->orderBy('created_at','desc')->paginate();
    	return view('orders.index',compact('orders'));
    }

    public function show(Order $order,Request $request)
    {
    	$this->authorize('own',$order);
    	return view('orders.show',['order' => $order->load('items.product','items.productSku')]);
    }
}
