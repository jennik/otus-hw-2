<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class HealthController
{
    public function index()
    {
        return new JsonResponse(['status' => 'OK']);
    }
}