<?php

namespace App\Http\Queries;

use App\Models\Product;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class ProductQuery extends QueryBuilder
{
	public function __construct()
	{
		parent::__construct(Product::query());
		$this->allowedIncludes('skus');
	}
}