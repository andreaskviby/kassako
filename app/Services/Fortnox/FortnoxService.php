<?php

namespace App\Services\Fortnox;

use App\Models\FortnoxConnection;
use App\Models\Team;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FortnoxService
{
    protected ?FortnoxConnection $connection = null;
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('fortnox.api_base_url');
    }

    public function forTeam(Team $team): self
    {
        $this->connection = $team->fortnoxConnection;

        if (!$this->connection) {
            throw new Exception('No Fortnox connection found for this team');
        }

        if ($this->connection->needsRefresh()) {
            $this->refreshAccessToken();
        }

        return $this;
    }

    protected function client(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => "Bearer {$this->connection->access_token}",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    public static function getAuthorizationUrl(string $state): string
    {
        $params = http_build_query([
            'client_id' => config('fortnox.client_id'),
            'redirect_uri' => config('fortnox.redirect_uri'),
            'scope' => config('fortnox.scopes'),
            'state' => $state,
            'response_type' => 'code',
        ]);

        return config('fortnox.auth_url') . '?' . $params;
    }

    public static function exchangeCodeForTokens(string $code): array
    {
        $response = Http::asForm()->post(config('fortnox.token_url'), [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => config('fortnox.client_id'),
            'client_secret' => config('fortnox.client_secret'),
            'redirect_uri' => config('fortnox.redirect_uri'),
        ]);

        if (!$response->successful()) {
            Log::error('Fortnox token exchange failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new Exception('Failed to exchange authorization code');
        }

        return $response->json();
    }

    protected function refreshAccessToken(): void
    {
        $response = Http::asForm()->post(config('fortnox.token_url'), [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->connection->refresh_token,
            'client_id' => config('fortnox.client_id'),
            'client_secret' => config('fortnox.client_secret'),
        ]);

        if (!$response->successful()) {
            $this->connection->markAsFailed('Token refresh failed');
            throw new Exception('Failed to refresh Fortnox token');
        }

        $data = $response->json();

        $this->connection->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? $this->connection->refresh_token,
            'token_expires_at' => now()->addSeconds($data['expires_in']),
        ]);

        $this->connection->refresh();
    }

    public function getCompanyInfo(): array
    {
        $response = $this->client()->get('companyinformation');

        if (!$response->successful()) {
            throw new Exception('Failed to fetch company information');
        }

        return $response->json('CompanyInformation');
    }

    public function getInvoices(array $params = []): array
    {
        $defaultParams = [
            'limit' => 500,
            'sortby' => 'invoicedate',
            'sortorder' => 'descending',
        ];

        $response = $this->client()->get('invoices', array_merge($defaultParams, $params));

        if (!$response->successful()) {
            throw new Exception('Failed to fetch invoices');
        }

        return $response->json('Invoices') ?? [];
    }

    public function getInvoice(string $documentNumber): array
    {
        $response = $this->client()->get("invoices/{$documentNumber}");

        if (!$response->successful()) {
            throw new Exception('Failed to fetch invoice');
        }

        return $response->json('Invoice');
    }

    public function getSupplierInvoices(array $params = []): array
    {
        $defaultParams = [
            'limit' => 500,
            'sortby' => 'invoicedate',
            'sortorder' => 'descending',
        ];

        $response = $this->client()->get('supplierinvoices', array_merge($defaultParams, $params));

        if (!$response->successful()) {
            throw new Exception('Failed to fetch supplier invoices');
        }

        return $response->json('SupplierInvoices') ?? [];
    }

    public function getOrders(array $params = []): array
    {
        $defaultParams = [
            'limit' => 500,
            'sortby' => 'orderdate',
            'sortorder' => 'descending',
        ];

        $response = $this->client()->get('orders', array_merge($defaultParams, $params));

        if (!$response->successful()) {
            throw new Exception('Failed to fetch orders');
        }

        return $response->json('Orders') ?? [];
    }

    public function getCustomers(array $params = []): array
    {
        $response = $this->client()->get('customers', array_merge(['limit' => 500], $params));

        if (!$response->successful()) {
            throw new Exception('Failed to fetch customers');
        }

        return $response->json('Customers') ?? [];
    }

    public function getAccounts(array $params = []): array
    {
        $response = $this->client()->get('accounts', $params);

        if (!$response->successful()) {
            throw new Exception('Failed to fetch accounts');
        }

        return $response->json('Accounts') ?? [];
    }

    public function getAccountBalance(int $accountNumber, ?int $year = null): array
    {
        $year = $year ?? now()->year;

        $response = $this->client()->get("balances/{$year}/{$accountNumber}");

        if (!$response->successful()) {
            return ['Balance' => 0];
        }

        return $response->json();
    }

    public function getCashBalance(): float
    {
        $totalCash = 0;

        foreach ([1910, 1920, 1930, 1940] as $account) {
            try {
                $balance = $this->getAccountBalance($account);
                $totalCash += floatval($balance['Balance'] ?? 0);
            } catch (Exception $e) {
                // Account might not exist, continue
            }
        }

        return $totalCash;
    }
}
