<?php

namespace App\Repositories;

use App\Entities\Url;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class UrlRepository
{
    public function save(Url $url): Url
    {
        $savedAt = Carbon::now();

        $id = DB::table('urls')->insertGetId([
            'name' => $url->name,
            'created_at' => $savedAt,
            'updated_at' => $savedAt,
        ]);

        $url->id = $id;
        $url->createdAt = $savedAt;
        $url->updatedAt = $savedAt;

        return $url;
    }

    public function find(int $id): ?Url
    {
        $row = DB::table('urls')->find($id);
        if ($row === null) {
            return null;
        }

        return $this->hydrateEntityFromQueryResult($row);
    }

    public function findByName(string $name): ?Url
    {
        /** @var stdClass|null $row */
        $row = DB::table('urls')->where('name', $name)->first();
        if ($row === null) {
            return null;
        }

        return $this->hydrateEntityFromQueryResult($row);
    }

    public function getAll(): array
    {
        return DB::table('urls')
            ->orderByDesc('id')
            ->get()
            ->map(fn($row) => $this->hydrateEntityFromQueryResult($row))
            ->toArray();
    }

    private function hydrateEntityFromQueryResult(stdClass $queryResult): Url
    {
        $url = new Url();
        $url->id = $queryResult->id;
        $url->name = $queryResult->name;
        $url->createdAt = Carbon::parse($queryResult->created_at);
        $url->updatedAt = Carbon::parse($queryResult->updated_at);

        return $url;
    }
}
