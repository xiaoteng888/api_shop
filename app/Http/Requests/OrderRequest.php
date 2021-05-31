<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\ProductSku;

class OrderRequest extends Request
{
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address_id' => [
                'required',
                Rule::exists('user_addresses','id')->where('user_id',$this->user()->id),
            ],
            'items' => ['required','array'],
            'items.*.sku_id' => [
                // 检查 items 数组下每一个子数组的 sku_id 参数
                'required',
                function($attribute,$value,$fail){
                    if(!$sku = ProductSku::find($value)){
                        return $fail('该商品不存在！');
                    }
                    if(!$sku->product->on_sale){
                        return $fail('该商品已下架！');
                    }
                    if($sku->stock === 0){
                        return $fail('该商品已售完！');
                    }
                    /*$amount = $sku->cartItems()->where('user_id',$this->user()->id)->first(); 
                    if($sku->stock < $amount->amount && $amount->amount > 0){
                        return $fail('库存不足!');
                    }*/
                    // 获取当前索引
                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                    $index = $m[1];
                    $amount = $this->input('items')[$index]['amount'];
                    if($amount > 0 && $amount > $sku->stock){
                        return $fail('库存不足!');
                    }
                },
            ],
            'items.*.amount' => ['required','integer','min:1'],
        ];
    }

    public function messages()
    {
        return [
            'address_id.required' => '请选择收货地址',
            'items.required' => '请选择一个商品',
            'items.*.amount.min' => '商品数量必须大于等于1',
        ];
    }
}
