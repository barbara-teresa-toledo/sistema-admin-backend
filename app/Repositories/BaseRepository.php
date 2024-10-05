<?php

namespace App\Repositories;

class BaseRepository
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function create(array $data): array
    {
        return $this->model->create($data)->toArray();
    }

    public function update(array $data, int $id): array
    {
        $model = $this->model->find($id);
        $model->update($data);

        return $model->toArray();
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    public function find(int $id): array
    {
        return $this->model->find($id)->toArray();
    }

    public function all(): array
    {
        return $this->model->all()->toArray();
    }

    public function findBy(array $criteria): array
    {
        return $this->model->where($criteria)->get()->toArray();
    }

    public function findOneBy(array $criteria): array
    {
        return $this->model->where($criteria)->first()->toArray();
    }

    public function paginate(int $perPage, array $columns = ['*'], string $pageName = 'page', int $page = 1): array
    {
        return $this->model->paginate($perPage, $columns, $pageName, $page)->toArray();
    }
}
