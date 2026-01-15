<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use App\Shipping;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShippingController extends Controller
{
    use ApiResponser;

    /**
     * The service to consume the order service
     * @var OrderService
     */
    public $orderService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Return the list of shippings
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $shippings = Shipping::all();
        return $this->successResponse($shippings);
    }

    /**
     * Create one new shipping
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'order_id' => 'required|integer|min:1',
            'address' => 'required|string',
            'shipping_method' => 'required|string',
            'cost' => 'required|numeric|min:0',
        ];

        $this->validate($request, $rules);

        // Verify order exists using OrderService
        // Uncomment this block when Orders Service is running
        /*
        try {
            $this->orderService->obtainOrder($request->order_id);
        } catch (\Exception $e) {
            return $this->errorResponse('The order does not exist or service unreachable', Response::HTTP_NOT_FOUND);
        }
        */

        $shipping = Shipping::create($request->all());

        return $this->successResponse($shipping, Response::HTTP_CREATED);
    }

    /**
     * Obtains and show one shipping
     * @return Illuminate\Http\Response
     */
    public function show($shipping)
    {
        $shipping = Shipping::findOrFail($shipping);
        return $this->successResponse($shipping);
    }

    /**
     * Get shipment for an order
     * @return Illuminate\Http\Response
     */
    public function showByOrder($order_id)
    {
        $shipping = Shipping::where('order_id', $order_id)->firstOrFail();
        return $this->successResponse($shipping);
    }

    /**
     * Update an existing shipping
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, $shipping)
    {
        $shipping = Shipping::findOrFail($shipping);

        $rules = [
            'order_id' => 'integer|min:1',
            'address' => 'string',
            'shipping_method' => 'string',
            'cost' => 'numeric|min:0',
            'status' => 'string',
            'tracking_number' => 'string',
        ];

        $this->validate($request, $rules);

        $shipping->fill($request->all());

        if ($shipping->isClean()) {
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $shipping->save();

        return $this->successResponse($shipping);
    }

    /**
     * Calculate shipping cost
     * @return Illuminate\Http\Response
     */
    public function calculate(Request $request)
    {
        $rules = [
            'address' => 'required|string',
            'shipping_method' => 'required|string',
        ];
        
        $this->validate($request, $rules);
        
        // Mock calculation
        $cost = 10.00;
        if ($request->shipping_method === 'express') {
            $cost += 15.00;
        }
        
        return $this->successResponse(['cost' => $cost]);
    }
}
