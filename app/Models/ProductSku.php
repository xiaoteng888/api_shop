<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InternalException;

class ProductSku extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'product_skus';
    
    protected $fillable=['title', 'description', 'price', 'stock'];

    public function product()
    {
    	return $this->belongsTo(Product::class,'product_id');
    }

    public function cartItems()
    {
    	return $this->hasMany(CartItem::class);
    }

    public function decreaseStock($amount)
    {
        if($amount < 0){
            throw new InternalException('减库存不能小于0');
        }
        return $this->where('id',$this->id)->where('stock','>=',$amount)->decrement('stock',$amount);
    }

    public function addStock($amount)
    {
        if($amount < 0){
            throw new InternalException('加库存不能小于0');
        }
        return $this->increment('stock',$amount);
    }
}
