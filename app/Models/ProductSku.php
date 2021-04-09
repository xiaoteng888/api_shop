<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'product_skus';
    
    protected $fillable=['title', 'description', 'price', 'stock'];

    public function product()
    {
    	return $this->belongsTo(Product::class,'product_id','id');
    }
}
