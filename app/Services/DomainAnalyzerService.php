<?php

namespace App\Services;

use App\Entities\Domain;
use App\Entities\DomainCheck;
use App\Repositories\DomainChecksRepository;
use App\Repositories\DomainRepository;
use DiDom\Document;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        try {
            $response = Http::get($domain->name);
            $domainCheck->statusCode = $response->status();
            if ($response->successful()) {
                $domainCheck = $this->fillDomainCheckEntityWithDataFromBody($domainCheck, $response->body());
            }
        } catch (\Exception $exception) {
            Log::error("Cannot resolve host when storing domain check", [
                'exception' => $exception,
                'domain_id' => $domain->id,
            ]);
        }

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

    private function fillDomainCheckEntityWithDataFromBody(DomainCheck $domainCheck, string $responseBody): DomainCheck
    {
        $document = new Document($responseBody);

        $domainCheck->h1 = optional($document->first('h1'))->text();
        $domainCheck->keywords = optional($document->first('meta[name="keywords"]'))->attr('content');
        $domainCheck->description = optional($document->first('meta[name="description"]'))->attr('content');

        return $domainCheck;
    }
}
