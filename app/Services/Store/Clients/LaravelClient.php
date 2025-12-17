<?php

declare(strict_types=1);

namespace App\Services\Store\Clients;

use App\Models\Store;
use Illuminate\Support\Facades\Http;

/**
 * Laravel API Client for connecting to other Laravel-based stores/applications
 *
 * This client supports connecting to any Laravel application that implements
 * a standard REST API for products, inventory, orders, and customers.
 */
class LaravelClient
{
    protected Store $store;

    protected string $baseUrl;

    protected string $apiToken;

    protected array $defaultHeaders;

    public function __construct(Store $store)
    {
        $this->store = $store;
        $this->baseUrl = rtrim($store->url, '/').'/api/v1';
        $this->apiToken = $store->integration?->access_token ?? '';
        $this->defaultHeaders = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Test the API connection
     */
    public function testConnection(): array
    {
        $response = $this->request('GET', '/health');

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'message' => $response->json('message', 'Connection test completed'),
        ];
    }

    /**
     * Get all products with pagination support
     */
    public function getProducts(int $perPage = 100, int $page = 1): array
    {
        $products = [];
        $currentPage = $page;

        do {
            $response = $this->request('GET', '/products', [
                'per_page' => $perPage,
                'page' => $currentPage,
            ]);

            if (! $response->successful()) {
                break;
            }

            $data = $response->json();
            $products = array_merge($products, $data['data'] ?? []);

            $lastPage = $data['meta']['last_page'] ?? $data['last_page'] ?? 1;
            $currentPage++;

        } while ($currentPage <= $lastPage);

        return $products;
    }

    /**
     * Get a single product by ID
     */
    public function getProduct(string $productId): ?array
    {
        $response = $this->request('GET', "/products/{$productId}");

        if ($response->successful()) {
            return $response->json('data') ?? $response->json();
        }

        return null;
    }

    /**
     * Create a new product
     */
    public function createProduct(array $data): ?array
    {
        $response = $this->request('POST', '/products', $data);

        if ($response->successful()) {
            return $response->json('data') ?? $response->json();
        }

        return null;
    }

    /**
     * Update a product
     */
    public function updateProduct(string $productId, array $data): bool
    {
        $response = $this->request('PUT', "/products/{$productId}", $data);

        return $response->successful();
    }

    /**
     * Delete a product
     */
    public function deleteProduct(string $productId): bool
    {
        $response = $this->request('DELETE', "/products/{$productId}");

        return $response->successful();
    }

    /**
     * Get inventory/stock levels
     */
    public function getInventory(array $filters = []): array
    {
        $response = $this->request('GET', '/inventory/stock', $filters);

        if ($response->successful()) {
            return $response->json('data') ?? $response->json() ?? [];
        }

        return [];
    }

    /**
     * Update stock for a product
     */
    public function updateStock(string $productId, int $quantity, string $action = 'set'): bool
    {
        $response = $this->request('POST', '/inventory/update-stock', [
            'product_id' => $productId,
            'quantity' => $quantity,
            'action' => $action, // 'set', 'add', 'subtract'
        ]);

        return $response->successful();
    }

    /**
     * Bulk update stock levels
     */
    public function bulkUpdateStock(array $items): array
    {
        $response = $this->request('POST', '/inventory/bulk-update-stock', [
            'items' => $items,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return ['success' => false, 'error' => $response->json('message', 'Bulk update failed')];
    }

    /**
     * Get all orders
     */
    public function getOrders(string $status = 'any', int $perPage = 100): array
    {
        $orders = [];
        $page = 1;

        do {
            $params = [
                'per_page' => $perPage,
                'page' => $page,
            ];

            if ($status !== 'any') {
                $params['status'] = $status;
            }

            $response = $this->request('GET', '/orders', $params);

            if (! $response->successful()) {
                break;
            }

            $data = $response->json();
            $orders = array_merge($orders, $data['data'] ?? []);

            $lastPage = $data['meta']['last_page'] ?? $data['last_page'] ?? 1;
            $page++;

        } while ($page <= $lastPage);

        return $orders;
    }

    /**
     * Get a single order
     */
    public function getOrder(string $orderId): ?array
    {
        $response = $this->request('GET', "/orders/{$orderId}");

        if ($response->successful()) {
            return $response->json('data') ?? $response->json();
        }

        return null;
    }

    /**
     * Create an order
     */
    public function createOrder(array $data): ?array
    {
        $response = $this->request('POST', '/orders', $data);

        if ($response->successful()) {
            return $response->json('data') ?? $response->json();
        }

        return null;
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(string $orderId, string $status): bool
    {
        $response = $this->request('PATCH', "/orders/{$orderId}/status", [
            'status' => $status,
        ]);

        return $response->successful();
    }

    /**
     * Get all customers
     */
    public function getCustomers(int $perPage = 100): array
    {
        $customers = [];
        $page = 1;

        do {
            $response = $this->request('GET', '/customers', [
                'per_page' => $perPage,
                'page' => $page,
            ]);

            if (! $response->successful()) {
                break;
            }

            $data = $response->json();
            $customers = array_merge($customers, $data['data'] ?? []);

            $lastPage = $data['meta']['last_page'] ?? $data['last_page'] ?? 1;
            $page++;

        } while ($page <= $lastPage);

        return $customers;
    }

    /**
     * Get a single customer
     */
    public function getCustomer(string $customerId): ?array
    {
        $response = $this->request('GET', "/customers/{$customerId}");

        if ($response->successful()) {
            return $response->json('data') ?? $response->json();
        }

        return null;
    }

    /**
     * Create a customer
     */
    public function createCustomer(array $data): ?array
    {
        $response = $this->request('POST', '/customers', $data);

        if ($response->successful()) {
            return $response->json('data') ?? $response->json();
        }

        return null;
    }

    /**
     * Update a customer
     */
    public function updateCustomer(string $customerId, array $data): bool
    {
        $response = $this->request('PUT', "/customers/{$customerId}", $data);

        return $response->successful();
    }

    /**
     * Register webhooks for real-time updates
     */
    public function registerWebhooks(array $events): array
    {
        $webhookUrl = url('/api/v1/webhooks/laravel/'.$this->store->id);

        $response = $this->request('POST', '/webhooks', [
            'url' => $webhookUrl,
            'events' => $events,
            'secret' => $this->store->integration?->webhook_secret ?? '',
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return ['success' => false, 'error' => $response->json('message', 'Failed to register webhooks')];
    }

    /**
     * Make an HTTP request to the API
     */
    protected function request(string $method, string $endpoint, array $data = [])
    {
        $url = $this->baseUrl.$endpoint;

        $http = Http::withHeaders($this->defaultHeaders);

        // Add authorization if token is available
        if ($this->apiToken) {
            $http = $http->withToken($this->apiToken);
        }

        return match (strtoupper($method)) {
            'GET' => $http->get($url, $data),
            'POST' => $http->post($url, $data),
            'PUT' => $http->put($url, $data),
            'PATCH' => $http->patch($url, $data),
            'DELETE' => $http->delete($url, $data),
            default => $http->get($url, $data),
        };
    }
}
