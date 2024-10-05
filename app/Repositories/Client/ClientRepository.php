<?php

namespace App\Repositories\Client;

use App\Models\ClientAddress;
use App\Repositories\BaseRepository;

class ClientRepository extends BaseRepository
{
    public function __construct(\App\Models\Client $model)
    {
        parent::__construct($model);
    }

    public function findByDocument($document)
    {
        $document = encrypt($document);
        return $this->model->where('document', $document)->first();
    }

    public function getClientWithAddress(int $perPage, array $columns = ['*'], string $pageName = 'page', int $page = 1)
    {
        return $this->model->with('address')->paginate($perPage, $columns, $pageName, $page)->toArray();
    }

    public function createClientWithAddress(array $data)
    {
        $address = $data['address'];
        $address = ClientAddress::create($address);
        $data['address_id'] = $address->id;
        unset($data['address']);
        return $this->create($data);
    }

    public function updateClientWithAddress(array $data, int $id)
    {
        $address = $data['address'];
        $address = ClientAddress::create($address);
        $data['address_id'] = $address->id;
        unset($data['address']);
        $this->update($data, $id);

        return $this->model->with('address')->where('id', $id)->get()->toArray();
    }

    public function getClientWithAddressById(int $id)
    {
        return $this->model->with('address')->find($id)->toArray();
    }

    public function getClientsByDocument(string $document)
    {
        $clients = $this->model->all();
        $clientsResults = $clients->filter(function ($client) use ($document) {
            return $client->document === $document;
        });

        return $clientsResults->toArray();
    }

    public function getClientByName(string $name)
    {
        $clients = $this->model->all();
        $clientsResults = $clients->filter(function ($client) use ($name) {
            if(strpos($client->name, $name) !== false) {
                return $client;
            }

            return null;
        });

        return $clientsResults->toArray();

    }
}
