<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DomainsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $response = $this->get(route('domains.index'));
        $response->assertOk();
    }

    public function testShow()
    {
        DB::table('domains')->insert([
            'id' => 123,
            'name' => 'https://demo.ru',
        ]);

        $response = $this->get(route('domains.show', ['domain' => 123]));
        $response->assertOk();
    }

    public function testShowNotFound()
    {
        $this->persistDomain([
            'id' => 123,
            'name' => 'https://demo.ru',
        ]);

        $response = $this->get(route('domains.show', ['domain' => 111]));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testStore()
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

    public function testStoreDuplicate()
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

    public function testStoreInvalidUrl()
    {
        $response = $this->post(route('domains.store'), [
            'domain' => ['name' => 'invalid url']
        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect();
    }

    private function persistDomain(array $data)
    {
        DB::table('domains')->insert($data);
    }
}
