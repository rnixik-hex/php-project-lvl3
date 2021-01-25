<?php

namespace App\Services;

use App\Entities\Domain;
use App\Entities\DomainCheck;
use App\Repositories\DomainChecksRepository;
use App\Repositories\DomainRepository;

class DomainAnalyzerService
{
    public function __construct(
        // phpcs:ignore
        private DomainRepository $domainRepository,
        // phpcs:ignore
        private DomainChecksRepository $domainChecksRepository,
    ) {
    }

    public function analyze(string $url): Domain
    {
        $urlData = parse_url($url);
        if (!is_array($urlData) || !isset($urlData['scheme']) || !isset($urlData['host'])) {
            throw new \Exception("Cannot get url data from url: '$url'");
        }
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

    public function getAllSavedDomains(): array
    {
        return $this->domainRepository->getAll();
    }

    public function createNewDomainCheck(Domain $domain): DomainCheck
    {
        $domainCheck = new DomainCheck($domain);

        return $this->domainChecksRepository->save($domainCheck);
    }

    public function getAllDomainChecks(Domain $domain): array
    {
        return $this->domainChecksRepository->getAllForDomain($domain->id);
    }

    public function getLatestDomainChecksForDomainsList(array $domains): array
    {
        return $this->domainChecksRepository->getLatestDomainChecksForDomainsList(array_column($domains, 'id'));
    }
}
