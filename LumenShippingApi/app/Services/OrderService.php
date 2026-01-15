<?php

namespace App\Services;

use App\Traits\ConsumesExternalService;

class OrderService
{
    use ConsumesExternalService;

    /**
     * The base uri to be used to consume the orders service
     * @var string
     */
    public $baseUri;

    /**
     * The secret to be used to consume the orders service
     * @var string
     */
    public $secret;

    public function __construct()
    {
        $this->baseUri = env('ORDERS_SERVICE_BASE_URL');
        $this->secret = env('ORDERS_SERVICE_SECRET');
    }

    /**
     * Get a single order from the orders service
     * @return array
     */
    public function obtainOrder($order)
    {
        return $this->performRequest('GET', "/orders/{$order}");
    }
}
