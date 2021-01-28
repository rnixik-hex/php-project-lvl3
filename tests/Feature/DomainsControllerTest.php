<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
            'status_code' => 200,
            'h1' => 'Demo h1',
            'keywords' => 'some,key,word',
            'description' => 'My super website',
            'created_at' => '2020-12-28 13:00',
        ]);

        $response = $this->get(route('domains.show', ['domain' => 123]));
        $response->assertOk();
        $response->assertSee('https://demo.ru');
        $response->assertSee('2020-12-28 13:00');
        $response->assertSee('200');
        $response->assertSee('Demo h1');
        $response->assertSee('some,key,word');
        $response->assertSee('My super website');
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

    public function testStoreCheckOk(): void
    {
        $this->persistDomain([
            'id' => 123,
            'name' => 'https://unique.example',
        ]);

        $fakeHtml = <<<fake
            <meta name="keywords" content="html,php">
            <meta name="description" content="demo description">
            <h1>Hello world</h1>
        fake;

        Http::fake([
            '*' => Http::response($fakeHtml, 200),
        ]);

        $response = $this->post(route('domains.storeCheck', ['domain' => '123']), []);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('domains.show', ['domain' => '123']));
        $this->assertDatabaseHas('domain_checks', [
            'domain_id' => 123,
            'status_code' => 200,
            'h1' => 'Hello world',
            'keywords' => 'html,php',
            'description' => 'demo description',
        ]);
    }

    public function testStoreCheckStatusCode(): void
    {
        $this->persistDomain([
            'id' => 123,
            'name' => 'https://d403.example',
        ]);

        Http::fake([
            '*' => Http::response('Hello World', 403),
        ]);

        $response = $this->post(route('domains.storeCheck', ['domain' => '123']));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('domains.show', ['domain' => '123']));
        $this->assertDatabaseHas('domain_checks', [
            'domain_id' => 123,
            'status_code' => 403,
        ]);
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
