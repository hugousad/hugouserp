<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ApiDocumentation extends Component
{
    public string $activeSection = 'overview';

    public array $sections = [
        'overview' => 'Overview',
        'authentication' => 'Authentication',
        'products' => 'Products',
        'inventory' => 'Inventory',
        'orders' => 'Orders',
        'customers' => 'Customers',
        'webhooks' => 'Webhooks',
        'errors' => 'Error Handling',
    ];

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user || ! $user->can('api.docs.view')) {
            // Allow access if user has any store permission
            if (! $user || ! $user->can('stores.view')) {
                abort(403);
            }
        }
    }

    public function switchSection(string $section): void
    {
        $this->activeSection = $section;
    }

    public function getEndpointsProperty(): array
    {
        return [
            'products' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/products',
                    'description' => 'List all products with pagination',
                    'params' => [
                        'per_page' => 'Number of items per page (default: 15, max: 100)',
                        'page' => 'Page number',
                        'search' => 'Search by name or SKU',
                        'category_id' => 'Filter by category ID',
                        'module_id' => 'Filter by module ID',
                    ],
                    'response' => '{"data": [...], "meta": {"current_page": 1, "last_page": 10, "total": 150}}',
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/products',
                    'description' => 'Create a new product',
                    'body' => [
                        'name' => 'required|string|max:255',
                        'sku' => 'required|string|max:100|unique',
                        'cost' => 'nullable|numeric|min:0',
                        'default_price' => 'nullable|numeric|min:0',
                        'category_id' => 'nullable|exists:product_categories,id',
                        'module_id' => 'required|exists:modules,id',
                    ],
                    'response' => '{"data": {"id": 1, "name": "...", ...}, "message": "Product created successfully"}',
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/products/{id}',
                    'description' => 'Get a single product by ID',
                    'response' => '{"data": {"id": 1, "name": "...", "sku": "...", ...}}',
                ],
                [
                    'method' => 'PUT',
                    'endpoint' => '/api/v1/products/{id}',
                    'description' => 'Update a product',
                    'body' => [
                        'name' => 'string|max:255',
                        'sku' => 'string|max:100|unique',
                        'cost' => 'numeric|min:0',
                        'default_price' => 'numeric|min:0',
                    ],
                    'response' => '{"data": {...}, "message": "Product updated successfully"}',
                ],
                [
                    'method' => 'DELETE',
                    'endpoint' => '/api/v1/products/{id}',
                    'description' => 'Delete a product (soft delete)',
                    'response' => '{"message": "Product deleted successfully"}',
                ],
            ],
            'inventory' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/inventory/stock',
                    'description' => 'Get current stock levels',
                    'params' => [
                        'product_id' => 'Filter by product ID',
                        'warehouse_id' => 'Filter by warehouse ID',
                        'low_stock' => 'Show only low stock items (true/false)',
                    ],
                    'response' => '{"data": [{"product_id": 1, "current_quantity": 100, ...}]}',
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/inventory/update-stock',
                    'description' => 'Update stock for a single product',
                    'body' => [
                        'product_id' => 'required|exists:products,id',
                        'quantity' => 'required|integer',
                        'action' => 'required|in:set,add,subtract',
                        'reason' => 'nullable|string|max:255',
                    ],
                    'response' => '{"success": true, "old_quantity": 50, "new_quantity": 100}',
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/inventory/bulk-update-stock',
                    'description' => 'Bulk update stock for multiple products',
                    'body' => [
                        'items' => 'required|array',
                        'items.*.product_id' => 'required|exists:products,id',
                        'items.*.quantity' => 'required|integer',
                        'items.*.action' => 'required|in:set,add,subtract',
                    ],
                    'response' => '{"success": true, "updated": 5, "failed": 0, "results": [...]}',
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/inventory/movements',
                    'description' => 'Get stock movement history',
                    'params' => [
                        'product_id' => 'Filter by product ID',
                        'from_date' => 'Start date (Y-m-d)',
                        'to_date' => 'End date (Y-m-d)',
                    ],
                    'response' => '{"data": [{"id": 1, "product_id": 1, "qty": 10, "direction": "in", ...}]}',
                ],
            ],
            'orders' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/orders',
                    'description' => 'List all orders with pagination',
                    'params' => [
                        'per_page' => 'Number of items per page',
                        'status' => 'Filter by status (pending, processing, completed, cancelled)',
                        'from_date' => 'Start date (Y-m-d)',
                        'to_date' => 'End date (Y-m-d)',
                    ],
                    'response' => '{"data": [...], "meta": {...}}',
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/orders',
                    'description' => 'Create a new order',
                    'body' => [
                        'customer_id' => 'nullable|exists:customers,id',
                        'external_id' => 'nullable|string|max:100',
                        'items' => 'required|array|min:1',
                        'items.*.product_id' => 'required|exists:products,id',
                        'items.*.quantity' => 'required|numeric|min:0.0001',
                        'items.*.price' => 'required|numeric|min:0',
                        'shipping_address' => 'nullable|array',
                        'notes' => 'nullable|string',
                    ],
                    'response' => '{"data": {...}, "message": "Order created successfully"}',
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/orders/{id}',
                    'description' => 'Get a single order with items',
                    'response' => '{"data": {"id": 1, "status": "pending", "items": [...], ...}}',
                ],
                [
                    'method' => 'PATCH',
                    'endpoint' => '/api/v1/orders/{id}/status',
                    'description' => 'Update order status',
                    'body' => [
                        'status' => 'required|in:pending,processing,completed,cancelled',
                    ],
                    'response' => '{"data": {...}, "message": "Order status updated"}',
                ],
            ],
            'customers' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/customers',
                    'description' => 'List all customers with pagination',
                    'params' => [
                        'per_page' => 'Number of items per page',
                        'search' => 'Search by name, email, or phone',
                    ],
                    'response' => '{"data": [...], "meta": {...}}',
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/customers',
                    'description' => 'Create a new customer',
                    'body' => [
                        'name' => 'required|string|max:255',
                        'email' => 'nullable|email|unique:customers,email',
                        'phone' => 'nullable|string|max:50',
                        'address' => 'nullable|string|max:500',
                    ],
                    'response' => '{"data": {...}, "message": "Customer created successfully"}',
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/customers/{id}',
                    'description' => 'Get a single customer',
                    'response' => '{"data": {"id": 1, "name": "...", ...}}',
                ],
                [
                    'method' => 'PUT',
                    'endpoint' => '/api/v1/customers/{id}',
                    'description' => 'Update a customer',
                    'body' => [
                        'name' => 'string|max:255',
                        'email' => 'email|unique:customers,email',
                        'phone' => 'string|max:50',
                    ],
                    'response' => '{"data": {...}, "message": "Customer updated successfully"}',
                ],
                [
                    'method' => 'DELETE',
                    'endpoint' => '/api/v1/customers/{id}',
                    'description' => 'Delete a customer',
                    'response' => '{"message": "Customer deleted successfully"}',
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/customers/email/{email}',
                    'description' => 'Find customer by email',
                    'response' => '{"data": {...}}',
                ],
            ],
            'webhooks' => [
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/webhooks/shopify/{storeId}',
                    'description' => 'Shopify webhook endpoint',
                    'headers' => [
                        'X-Shopify-Topic' => 'Webhook topic (products/create, orders/create, etc.)',
                        'X-Shopify-Hmac-Sha256' => 'HMAC signature for verification',
                    ],
                    'topics' => ['products/create', 'products/update', 'products/delete', 'orders/create', 'orders/updated', 'inventory_levels/update'],
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/webhooks/woocommerce/{storeId}',
                    'description' => 'WooCommerce webhook endpoint',
                    'headers' => [
                        'X-WC-Webhook-Topic' => 'Webhook topic',
                        'X-WC-Webhook-Signature' => 'HMAC signature for verification',
                    ],
                    'topics' => ['product.created', 'product.updated', 'product.deleted', 'order.created', 'order.updated'],
                ],
            ],
        ];
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.api-documentation', [
            'endpoints' => $this->endpoints,
        ]);
    }
}
