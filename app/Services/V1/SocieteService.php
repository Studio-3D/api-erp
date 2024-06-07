<?php
namespace App\Services\V1;

interface SocieteService
{
    function createSociete(array $data);
    function getSocieteById(int $id);
}