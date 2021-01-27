<?php

namespace App\Repositories;

use App\Entities\DomainCheck;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class DomainChecksRepository
{
    public function __construct(private DomainRepository $domainRepository)
    {
    }

    public function save(DomainCheck $domainCheck): DomainCheck
    {
        $savedAt = Carbon::now();

        $id = DB::table('domain_checks')->insertGetId([
            'domain_id' => $domainCheck->domain->id,
            'status_code' => $domainCheck->statusCode,
            'h1' => $domainCheck->h1,
            'keywords' => $domainCheck->keywords,
            'description' => $domainCheck->description,
            'created_at' => $savedAt,
            'updated_at' => $savedAt,
        ]);

        $domainCheck->id = $id;
        $domainCheck->createdAt = $savedAt;
        $domainCheck->updatedAt = $savedAt;

        return $domainCheck;
    }

    public function find(int $id): ?DomainCheck
    {
        $row = DB::table('domains_checks')->find($id);
        if (!$row) {
            return null;
        }

        return $this->hydrateEntityFromQueryResult($row);
    }

    public function getAllForDomain(int $domainId): array
    {
        return DB::table('domain_checks')
            ->where('domain_id', $domainId)
            ->orderByDesc('id')
            ->get()
            ->map(fn($row) => $this->hydrateEntityFromQueryResult($row))
            ->toArray();
    }

    public function getLatestDomainChecksForDomainsList(array $domainsIds): array
    {
        return DB::table('domain_checks AS dc')
            ->whereIn('dc.domain_id', $domainsIds)
            ->joinSub(
                'SELECT domain_id, MAX(id) AS id
                        FROM domain_checks GROUP BY (domain_id)',
                'latest',
                'latest.id',
                'dc.id'
            )
            ->orderByDesc('dc.id')
            ->get()
            ->mapWithKeys(fn($row) => [$row->domain_id => $this->hydrateEntityFromQueryResult($row)])
            ->toArray();
    }

    private function hydrateEntityFromQueryResult(stdClass $queryResult): DomainCheck
    {
        $domain = $this->domainRepository->find($queryResult->domain_id);
        if (!$domain) {
            throw new \Exception("Cannot find domain by id = '{$queryResult->domain_id}' for #{$queryResult->id}");
        }

        $domainCheck = new DomainCheck($domain);
        $domainCheck->id = $queryResult->id;
        $domainCheck->statusCode = $queryResult->status_code;
        $domainCheck->h1 = $queryResult->h1;
        $domainCheck->keywords = $queryResult->keywords;
        $domainCheck->description = $queryResult->description;
        $domainCheck->createdAt = Carbon::parse($queryResult->created_at);
        $domainCheck->updatedAt = Carbon::parse($queryResult->updated_at);

        return $domainCheck;
    }
}
