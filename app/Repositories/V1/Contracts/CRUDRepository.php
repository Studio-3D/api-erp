<?php
namespace App\Repositories\V1\Contracts;

interface CRUDRepository
{
    function create(array $data);
    function update($id, array $data);
    function find($id);
    function all(array $filters, int $size, int $page);
}