<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DomainsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex(): void
    {
        $this->persistDomain([
            'id' => 123,
            'name' => 'https://demo.ru',
        ]);
        $this->persistDomain([
            'id' => 456,
            'name' => 'https://demo2.example',
        ]);
        $this->persistDomainCheck([
            'domain_id' => 456,
            'created_at' => '2020-12-28 13:00',
        ]);

        $response = $this->get(route('domains.index'));
        $response->assertOk();
        $response->assertSee('https://demo.ru');
        $response->assertSee('https://demo2.example');
        $response->assertSee('2020-12-28 13:00');
    }

    public function testShow(): void
    {
        $this->persistDomain([
            'id' => 123,
            'name' => 'https://demo.ru',
        ]);
        $this->persistDomainCheck([
            'domain_id' => 123,
            'created_at' => '2020-12-28 13:00',
        ]);

        $response = $this->get(route('domains.show', ['domain' => 123]));
        $response->assertOk();
        $response->assertSee('https://demo.ru');
        $response->assertSee('2020-12-28 13:00');
    }

    public function testShowNotFound(): void
    {
        $this->persistDomain([
            'id' => 123,
            'name' => 'https://demo.ru',
        ]);

        $response = $this->get(route('domains.show', ['domain' => 111]));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testStore(): void
    {
        $data = [
            'domain' => [
                'name' => 'https://example.com/path'
            ],
        ];

        $response = $this->post(route('domains.store'), $data);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('domains', [
            'name' => 'https://example.com',
        ]);
    }

    public function testStoreDuplicate(): void
    {
        $this->persistDomain([
            'id' => 123,
            'name' => 'https://unique.example',
        ]);

        $response = $this->post(route('domains.store'), [
            'domain' => ['name' => 'https://unique.example']
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('domains.show', ['domain' => '123']));
    }

    public function testStoreInvalidUrl(): void
    {
        $response = $this->post(route('domains.store'), [
            'domain' => ['name' => 'invalid url']
        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect();
    }

    public function testStoreCheck(): void
    {
        $this->persistDomain([
            'id' => 123,
            'name' => 'https://unique.example',
        ]);

        $response = $this->post(route('domains.storeCheck', ['domain' => '123']), []);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('domains.show', ['domain' => '123']));
    }

    private function persistDomain(array $data): void
    {
        DB::table('domains')->insert($data);
    }

    private function persistDomainCheck(array $data): void
    {
        DB::table('domain_checks')->insert($data);
    }
}
