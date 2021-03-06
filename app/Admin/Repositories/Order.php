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

}
