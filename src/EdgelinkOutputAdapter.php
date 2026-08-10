<?php

declare(strict_types=1);

namespace OTGH\AccessControl\EdgelinkAdapter;

use App\Services\AccessControl\OutputAdapterInterface;
use OTGH\LaravelEdgelink\LaravelEdgelinkClient;

class EdgelinkOutputAdapter implements OutputAdapterInterface
{
    public function type(): string
    {
        return 'edgelink';
    }

    /**
     * @param  array<string,mixed>  $bindingConfig
     */
    public function read(string $channel, array $bindingConfig = []): mixed
    {
        return $this->makeClient($bindingConfig)->tags()->getField($channel, 'value');
    }

    /**
     * @param  array<string,mixed>  $bindingConfig
     */
    public function write(string $channel, mixed $value, array $bindingConfig = []): void
    {
        $this->makeClient($bindingConfig)->tags()->updateValue($channel, $value);
    }

    /**
     * @param  array<string,mixed>  $bindingConfig
     */
    private function makeClient(array $bindingConfig): LaravelEdgelinkClient
    {
        $runtimeConfig = $this->buildRuntimeConfig($bindingConfig);

        if ($runtimeConfig === []) {
            return LaravelEdgelinkClient::fromConfig();
        }

        return LaravelEdgelinkClient::fromConfig($runtimeConfig);
    }

    /**
     * @param  array<string,mixed>  $bindingConfig
     * @return array<string,mixed>
     */
    private function buildRuntimeConfig(array $bindingConfig): array
    {
        $edgelinkConfig = data_get($bindingConfig, 'edgelink');
        $config = is_array($edgelinkConfig) ? $edgelinkConfig : $bindingConfig;

        $baseUrl = $this->nullableString(data_get($config, 'base_url'))
            ?? $this->nullableString(data_get($config, 'endpoint'));

        $password = $this->nullableString(data_get($config, 'password'));
        $referer = $this->nullableString(data_get($config, 'referer')) ?? $baseUrl;

        if ($baseUrl === null || $password === null) {
            return [];
        }

        return [
            'base_url' => $baseUrl,
            'password' => $password,
            'referer' => $referer,
            'verify_tls' => (bool) data_get($config, 'verify_tls', false),
            'timeout_seconds' => max(1, (int) data_get($config, 'timeout_seconds', 10)),
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (! is_scalar($value)) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
