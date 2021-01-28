<?php

namespace App\Entities;

use Carbon\Carbon;

class UrlCheck
{
    public int $id;
    public ?int $statusCode = null;
    public ?string $h1 = null;
    public ?string $keywords = null;
    public ?string $description = null;
    public Carbon $createdAt;
    public Carbon $updatedAt;

    public function __construct(public Url $url)
    {
    }
}
