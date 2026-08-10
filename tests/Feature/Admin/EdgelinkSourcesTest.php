<?php

use App\Models\Hardware\Source;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates an edgelink source with vendor config keys', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.access-sources.store'), [
        'name' => 'Main Edgelink RTU',
        'identifier' => 'main-edgelink-rtu',
        'type' => 'edgelink',
        'endpoint' => '',
        'enabled' => '1',
        'edgelink_base_url' => 'https://10.5.1.60',
        'edgelink_password' => 'rtu-password',
        'edgelink_referer' => 'https://10.5.1.60',
        'edgelink_verify_tls' => '0',
        'edgelink_timeout_seconds' => '10',
        'metadata_json' => '{}',
    ]);

    $response->assertRedirect(route('admin.access-sources.index'));

    $source = Source::query()->where('identifier', 'main-edgelink-rtu')->firstOrFail();

    expect($source->type)->toBe('edgelink');
    expect($source->endpoint)->toBe('https://10.5.1.60');
    expect(data_get($source->config, 'base_url'))->toBe('https://10.5.1.60');
    expect(data_get($source->config, 'password'))->toBe('rtu-password');
    expect(data_get($source->config, 'referer'))->toBe('https://10.5.1.60');
    expect(data_get($source->config, 'verify_tls'))->toBeFalse();
    expect(data_get($source->config, 'timeout_seconds'))->toBe(10);
});
