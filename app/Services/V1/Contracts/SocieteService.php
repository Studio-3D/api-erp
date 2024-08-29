<?php
namespace App\Services\V1\Contracts;

interface SocieteService
{
    function createSociete(array $data);
    function getSocieteById(int $id);
    function getSocietes(array $filters, int $size, int $page);
}