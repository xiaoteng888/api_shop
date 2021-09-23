<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\OrderItem;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Resources\OrderItemResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $favored = false;
        if($user = $request->user()){
            $favored = boolval($user->favoriteProducts()->find($request->product_id));
        }
        $data['favored'] = $favored;
        //用户评价
        $data['reviews'] = OrderItem::query()
                           ->with(['productSku','order.user'])
                           ->where('product_id',$request->product_id)
                           ->whereNotNull('reviewed_at')
                           ->orderBy('reviewed_at','desc')
                           ->get();                   
        return $data;
    }
}
