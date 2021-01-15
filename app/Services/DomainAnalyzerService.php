<?php

namespace App\Services;

use App\Entities\Domain;
use App\Repositories\DomainRepository;

class DomainAnalyzerService
{
    public function __construct(private DomainRepository $domainRepository)
    {
    }

    public function analyze(string $url): Domain
    {
        $urlData = parse_url($url);
        $normalizedDomainName = "{$urlData['scheme']}://{$urlData['host']}";
        $existedDomain = $this->domainRepository->findByName($normalizedDomainName);
        if ($existedDomain) {
            return $existedDomain;
        }

        $domain = new Domain();
        $domain->name = $normalizedDomainName;

        return $this->domainRepository->save($domain);
    }

    public function getSavedDomain(int $id): ?Domain
    {
        return $this->domainRepository->find((int) $id);
    }
}
