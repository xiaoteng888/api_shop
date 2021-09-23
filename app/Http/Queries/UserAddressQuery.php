<?php 

namespace App\Http\Queries;

use App\Models\UserAddress;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class UserAddressQuery extends QueryBuilder
{
	public function __construct()
	{
		parent::__construct(UserAddress::query());
        $this->allowedIncludes('user');
	}
}