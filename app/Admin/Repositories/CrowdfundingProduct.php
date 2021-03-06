<?php

namespace App\Admin\Repositories;

use App\Models\CrowdfundingProduct as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class CrowdfundingProduct extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
