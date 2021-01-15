<?php

namespace App\Repositories;

use App\Entities\Domain;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DomainRepository
{
    public function save(Domain $domain): Domain
    {
        $id = DB::table('domains')->insertGetId([
            'name' => $domain->name,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $domain->id = $id;

        return $domain;
    }

    public function find(int $id): ?Domain
    {
        $row = DB::table('domains')->find($id);
        if (!$row) {
            return null;
        }

        return $this->hydrateEntityFromQueryResult($row);
    }

    public function findByName(string $name): ?Domain
    {
        $row = DB::table('domains')->where('name', $name)->first();
        if (!$row) {
            return null;
        }

        return $this->hydrateEntityFromQueryResult($row);
    }

    private function hydrateEntityFromQueryResult(object $queryResult): Domain
    {
        $domain = new Domain();
        $domain->id = $queryResult->id;
        $domain->name = $queryResult->name;
        $domain->created_at = Carbon::parse($queryResult->created_at);
        $domain->updated_at = Carbon::parse($queryResult->updated_at);

        return $domain;
    }
}
