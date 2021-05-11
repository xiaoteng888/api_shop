<?php

namespace App\Admin\Repositories;

use App\Models\Order as Model;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Show;
use Dcat\Admin\Grid;

class Order extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;

    public function arr($name)
    {
        return Order::$name;
    }

    public function aat(Model $model)
    {
    	return $model->user_id;
    }
}
