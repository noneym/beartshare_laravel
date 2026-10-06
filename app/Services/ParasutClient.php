<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Paraşüt API v4 istemcisi (OAuth2 password grant). Ayarlar: config('services.parasut').
 */
class ParasutClient
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('services.parasut');
        foreach (['client_id', 'client_secret', 'username', 'password', 'company_id'] as $key) {
            if (empty($this->config[$key])) {
                throw new RuntimeException("Paraşüt ayarı eksik: PARASUT_" . strtoupper($key));
            }
        }
    }

    protected function token(): string
    {
        // Paraşüt token'ı 2 saat geçerli; biraz önce yenilenir
        return Cache::remember('parasut:token', now()->addMinutes(100), function () {
            $res = Http::asForm()->timeout(30)->post($this->config['base_url'] . '/oauth/token', [
                'grant_type' => 'password',
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'username' => $this->config['username'],
                'password' => $this->config['password'],
                'redirect_uri' => 'urn:ietf:wg:oauth:2.0:oob',
            ]);
            if (!$res->successful() || !$res->json('access_token')) {
                throw new RuntimeException('Paraşüt girişi başarısız (HTTP ' . $res->status() . ')');
            }
            return $res->json('access_token');
        });
    }

    public function get(string $path, array $query = []): array
    {
        $url = $this->config['base_url'] . "/v4/{$this->config['company_id']}/" . ltrim($path, '/');
        $res = Http::withToken($this->token())->acceptJson()->timeout(90)->retry(2, 2000, throw: false)->get($url, $query);
        if ($res->status() === 401) {
            Cache::forget('parasut:token');
            $res = Http::withToken($this->token())->acceptJson()->timeout(90)->get($url, $query);
        }
        if (!$res->successful()) {
            throw new RuntimeException("Paraşüt {$path}: HTTP {$res->status()} " . mb_substr($res->body(), 0, 200));
        }
        return $res->json();
    }

    /**
     * Tüm satış faturaları (müşteri, satırlar + ürün, e-belge dahil).
     * @return array{data: array, included: array<string, array>} included anahtarı "tip:id"
     */
    public function salesInvoices(?string $updatedSince = null): array
    {
        $data = [];
        $included = [];
        $query = ['page[size]' => 25, 'sort' => 'issue_date', 'include' => 'contact,details.product,active_e_document'];
        if ($updatedSince) {
            $query['filter[updated_at]'] = $updatedSince; // ileride artımlı senkron için
        }
        for ($page = 1; ; $page++) {
            $json = $this->get('sales_invoices', $query + ['page[number]' => $page]);
            array_push($data, ...($json['data'] ?? []));
            foreach ($json['included'] ?? [] as $inc) {
                $included[$inc['type'] . ':' . $inc['id']] = $inc;
            }
            if ($page >= ($json['meta']['total_pages'] ?? 1)) break;
        }

        return ['data' => $data, 'included' => $included];
    }

    /** e-Arşiv / e-Fatura PDF içeriği; $type: e_archives | e_invoices */
    public function eDocumentPdf(string $type, string $id): ?string
    {
        $url = $this->get("{$type}/{$id}/pdf")['data']['attributes']['url'] ?? null;
        if (!$url) return null;

        // S3 standart dışı Content-Encoding döndürüyor: çözmeden ham indir
        $res = Http::withOptions(['decode_content' => false])->timeout(90)->retry(2, 2000, throw: false)->get($url);
        $body = $res->body();

        return $res->successful() && str_starts_with($body, '%PDF') ? $body : null;
    }
}
