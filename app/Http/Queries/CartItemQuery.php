<?php

namespace App\Http\Queries;

use App\Models\CartItem;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class CartItemQuery extends QueryBuilder
{
	public function __construct()
	{
		parent::__construct(CartItem::query());
        $this->allowedIncludes('user','productSku');
	}
}