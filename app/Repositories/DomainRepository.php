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

        $domain = new Domain();
        $domain->id = $row->id;
        $domain->name = $row->name;
        $domain->created_at = Carbon::parse($row->created_at);
        $domain->updated_at = Carbon::parse($row->updated_at);

        return $domain;
    }
}
