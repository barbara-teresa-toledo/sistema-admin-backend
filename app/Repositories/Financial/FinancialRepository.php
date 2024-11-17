<?php

namespace App\Repositories\Financial;

use App\Models\ClientAddress;
use App\Models\Financial;
use App\Models\ServiceOrder;
use App\Repositories\BaseRepository;

class FinancialRepository extends BaseRepository
{
    public function __construct(Financial $model)
    {
        parent::__construct($model);
    }

    public function getFinancialOperations(int $perPage, array $columns = ['*'], string $pageName = 'page', int $page = 1)
    {
        return $this->model->paginate($perPage, $columns, $pageName, $page)->toArray();
    }
}
