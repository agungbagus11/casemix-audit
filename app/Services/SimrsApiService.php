<?php

namespace App\Services;

use App\Models\SimrsApiLog;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Throwable;

class SimrsApiService
{
    protected string $baseUrl;
    protected string $token;
    protected int $timeout;
    protected bool $verifySsl;

    public function __construct()
    {
        $this->baseUrl   = rtrim((string) config('services.simrs.base_url'), '/');
        $this->token     = (string) config('services.simrs.token');
        $this->timeout   = (int) config('services.simrs.timeout', 30);
        $this->verifySsl = (bool) config('services.simrs.verify_ssl', true);
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->asJson()
            ->timeout($this->timeout)
            ->withToken($this->token)
            ->withOptions([
                'verify' => $this->verifySsl,
            ]);
    }

    protected function logRequest(
        string $endpoint,
        string $method,
        array $requestPayload = [],
        mixed $responsePayload = null,
        ?int $httpStatus = null,
        bool $isSuccess = true,
        ?string $errorMessage = null
    ): void {
        try {
            SimrsApiLog::create([
                'endpoint'         => $endpoint,
                'method'           => strtoupper($method),
                'request_payload'  => empty($requestPayload) ? null : json_encode($requestPayload, JSON_UNESCAPED_UNICODE),
                'response_payload' => $responsePayload === null ? null : json_encode($responsePayload, JSON_UNESCAPED_UNICODE),
                'http_status'      => $httpStatus,
                'is_success'       => $isSuccess,
                'error_message'    => $errorMessage,
            ]);
        } catch (Throwable $e) {
            // Jangan sampai logging memutus proses utama
        }
    }

    public function get(string $endpoint, array $query = []): array
    {
        try {
            $response = $this->client()->get($endpoint, $query);
            $data = $response->json();

            $this->logRequest(
                endpoint: $endpoint,
                method: 'GET',
                requestPayload: $query,
                responsePayload: $data,
                httpStatus: $response->status(),
                isSuccess: $response->successful(),
                errorMessage: $response->successful() ? null : $response->body()
            );

            $response->throw();

            return is_array($data) ? $data : [];
        } catch (RequestException $e) {
            $response = $e->response;

            $this->logRequest(
                endpoint: $endpoint,
                method: 'GET',
                requestPayload: $query,
                responsePayload: $response?->json(),
                httpStatus: $response?->status(),
                isSuccess: false,
                errorMessage: $e->getMessage()
            );

            throw $e;
        }
    }

    public function post(string $endpoint, array $payload = []): array
    {
        try {
            $response = $this->client()->post($endpoint, $payload);
            $data = $response->json();

            $this->logRequest(
                endpoint: $endpoint,
                method: 'POST',
                requestPayload: $payload,
                responsePayload: $data,
                httpStatus: $response->status(),
                isSuccess: $response->successful(),
                errorMessage: $response->successful() ? null : $response->body()
            );

            $response->throw();

            return is_array($data) ? $data : [];
        } catch (RequestException $e) {
            $response = $e->response;

            $this->logRequest(
                endpoint: $endpoint,
                method: 'POST',
                requestPayload: $payload,
                responsePayload: $response?->json(),
                httpStatus: $response?->status(),
                isSuccess: false,
                errorMessage: $e->getMessage()
            );

            throw $e;
        }
    }

    public function put(string $endpoint, array $payload = []): array
    {
        try {
            $response = $this->client()->put($endpoint, $payload);
            $data = $response->json();

            $this->logRequest(
                endpoint: $endpoint,
                method: 'PUT',
                requestPayload: $payload,
                responsePayload: $data,
                httpStatus: $response->status(),
                isSuccess: $response->successful(),
                errorMessage: $response->successful() ? null : $response->body()
            );

            $response->throw();

            return is_array($data) ? $data : [];
        } catch (RequestException $e) {
            $response = $e->response;

            $this->logRequest(
                endpoint: $endpoint,
                method: 'PUT',
                requestPayload: $payload,
                responsePayload: $response?->json(),
                httpStatus: $response?->status(),
                isSuccess: false,
                errorMessage: $e->getMessage()
            );

            throw $e;
        }
    }

    public function delete(string $endpoint, array $payload = []): array
    {
        try {
            $response = $this->client()->delete($endpoint, $payload);
            $data = $response->json();

            $this->logRequest(
                endpoint: $endpoint,
                method: 'DELETE',
                requestPayload: $payload,
                responsePayload: $data,
                httpStatus: $response->status(),
                isSuccess: $response->successful(),
                errorMessage: $response->successful() ? null : $response->body()
            );

            $response->throw();

            return is_array($data) ? $data : [];
        } catch (RequestException $e) {
            $response = $e->response;

            $this->logRequest(
                endpoint: $endpoint,
                method: 'DELETE',
                requestPayload: $payload,
                responsePayload: $response?->json(),
                httpStatus: $response?->status(),
                isSuccess: false,
                errorMessage: $e->getMessage()
            );

            throw $e;
        }
    }

    public function fetchDischargesByDate(string $date): array
    {
        return $this->get('/discharge', [
            'date' => $date,
        ]);
    }

    public function fetchEncounterDetail(string $encounterId): array
    {
        return $this->get("/encounters/{$encounterId}");
    }

    public function fetchResume(string $encounterId): array
    {
        return $this->get("/encounters/{$encounterId}/resume");
    }

    public function fetchDiagnoses(string $encounterId): array
    {
        return $this->get("/encounters/{$encounterId}/diagnoses");
    }

    public function fetchProcedures(string $encounterId): array
    {
        return $this->get("/encounters/{$encounterId}/procedures");
    }

    public function fetchBilling(string $encounterId): array
    {
        return $this->get("/encounters/{$encounterId}/billing");
    }

    public function fetchSep(string $encounterId): array
    {
        return $this->get("/encounters/{$encounterId}/sep");
    }

    public function fetchDocuments(string $encounterId): array
    {
        return $this->get("/encounters/{$encounterId}/documents");
    }

    public function fetchLabs(string $encounterId): array
    {
        return $this->get("/encounters/{$encounterId}/labs");
    }

    public function fetchRadiology(string $encounterId): array
    {
        return $this->get("/encounters/{$encounterId}/radiology");
    }

    public function fetchOperationReport(string $encounterId): array
    {
        return $this->get("/encounters/{$encounterId}/operation-report");
    }

    public function fetchCppt(string $encounterId): array
    {
        return $this->get("/encounters/{$encounterId}/cppt");
    }
}