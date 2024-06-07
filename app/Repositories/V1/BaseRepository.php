<?php
namespace App\Repositories\V1;

interface BaseRepository
{
    function create(array $data);
    function update($id, array $data);
    function find($id);
}