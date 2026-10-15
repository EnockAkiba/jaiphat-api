<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Contact;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(['data' => [
            'products' => Product::count(),
            'orders' => Order::count(),
            'appointments' => Appointment::where('status', 'pending')->count(),
            'messages' => Contact::whereNull('read_at')->count(),
            'subscribers' => NewsletterSubscriber::count(),
            'recentOrders' => Order::latest()->limit(5)->get()->map(fn (Order $o) => [
                'id' => $o->number,
                'customer' => ['name' => $o->customer_name],
                'total' => $o->total_cents === null ? null : round($o->total_cents / 100, 2),
                'createdAt' => $o->created_at?->toIso8601String(),
            ])->values(),
        ]]);
    }
}