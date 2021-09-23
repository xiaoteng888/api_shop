<?php

namespace App\Http\Queries;

use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Models\Installment;

class InstallmentQuery extends QueryBuilder
{
	public function __construct()
	{
		parent::__construct(Installment::query());
		$this->allowedIncludes('user','order','items');
	}
}