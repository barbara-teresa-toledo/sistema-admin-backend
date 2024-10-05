<?php

namespace App\Repositories\ServiceOrder;

use App\Models\ClientAddress;
use App\Models\ServiceOrder;
use App\Repositories\BaseRepository;

class ServiceOrderRepository extends BaseRepository
{
    public function __construct(ServiceOrder $model)
    {
        parent::__construct($model);
    }

    public function findByDocument($document)
    {
        $document = encrypt($document);
        return $this->model->where('document', $document)->first();
    }

    public function getOrderWithClient(int $perPage, array $columns = ['*'], string $pageName = 'page', int $page = 1)
    {
        return $this->model->with('client', 'client.address')->paginate($perPage, $columns, $pageName, $page)->toArray();
    }
    public function getOrderWithClientById(int $id)
    {
        return $this->model->with('client', 'client.address')->find($id)->toArray();
    }
}
