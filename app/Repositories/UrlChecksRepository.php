<?php

namespace App\Repositories;

use App\Entities\UrlCheck;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class UrlChecksRepository
{
    public function __construct(private UrlRepository $urlRepository)
    {
    }

    public function save(UrlCheck $urlCheck): UrlCheck
    {
        $savedAt = Carbon::now();

        $id = DB::table('url_checks')->insertGetId([
            'url_id' => $urlCheck->url->id,
            'status_code' => $urlCheck->statusCode,
            'h1' => $urlCheck->h1,
            'keywords' => $urlCheck->keywords,
            'description' => $urlCheck->description,
            'created_at' => $savedAt,
            'updated_at' => $savedAt,
        ]);

        $urlCheck->id = $id;
        $urlCheck->createdAt = $savedAt;
        $urlCheck->updatedAt = $savedAt;

        return $urlCheck;
    }

    public function find(int $id): ?UrlCheck
    {
        $row = DB::table('url_checks')->find($id);
        if (!$row) {
            return null;
        }

        return $this->hydrateEntityFromQueryResult($row);
    }

    public function getAllForUrl(int $urlId): array
    {
        return DB::table('url_checks')
            ->where('url_id', $urlId)
            ->orderByDesc('id')
            ->get()
            ->map(fn($row) => $this->hydrateEntityFromQueryResult($row))
            ->toArray();
    }

    public function getLatestUrlChecksForUrlsList(array $urlsIds): array
    {
        return DB::table('url_checks AS dc')
            ->whereIn('dc.url_id', $urlsIds)
            ->joinSub(
                'SELECT url_id, MAX(id) AS id
                        FROM url_checks GROUP BY (url_id)',
                'latest',
                'latest.id',
                'dc.id'
            )
            ->orderByDesc('dc.id')
            ->get()
            ->mapWithKeys(fn($row) => [$row->url_id => $this->hydrateEntityFromQueryResult($row)])
            ->toArray();
    }

    private function hydrateEntityFromQueryResult(stdClass $queryResult): UrlCheck
    {
        $url = $this->urlRepository->find($queryResult->url_id);
        if (!$url) {
            throw new \Exception("Cannot find url by id = '{$queryResult->url_id}' for #{$queryResult->id}");
        }

        $urlCheck = new UrlCheck($url);
        $urlCheck->id = $queryResult->id;
        $urlCheck->statusCode = $queryResult->status_code;
        $urlCheck->h1 = $queryResult->h1;
        $urlCheck->keywords = $queryResult->keywords;
        $urlCheck->description = $queryResult->description;
        $urlCheck->createdAt = Carbon::parse($queryResult->created_at);
        $urlCheck->updatedAt = Carbon::parse($queryResult->updated_at);

        return $urlCheck;
    }
}
