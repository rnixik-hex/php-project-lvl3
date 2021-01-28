<?php

namespace App\Services;

use App\Entities\Url;
use App\Entities\UrlCheck;
use App\Repositories\UrlChecksRepository;
use App\Repositories\UrlRepository;
use DiDom\Document;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UrlAnalyzerService
{
    public function __construct(
        // phpcs:ignore
        private UrlRepository $urlRepository,
        // phpcs:ignore
        private UrlChecksRepository $urlChecksRepository,
    ) {
    }

    public function analyze(string $url): Url
    {
        $urlData = parse_url($url);
        if (!is_array($urlData) || !isset($urlData['scheme']) || !isset($urlData['host'])) {
            throw new \Exception("Cannot get url data from url: '$url'");
        }
        $normalizedUrlName = "{$urlData['scheme']}://{$urlData['host']}";
        $existedUrl = $this->urlRepository->findByName($normalizedUrlName);
        if ($existedUrl) {
            return $existedUrl;
        }

        $url = new Url();
        $url->name = $normalizedUrlName;

        return $this->urlRepository->save($url);
    }

    public function getSavedUrl(int $id): ?Url
    {
        return $this->urlRepository->find((int) $id);
    }

    public function getAllSavedUrls(): array
    {
        return $this->urlRepository->getAll();
    }

    public function createNewUrlCheck(Url $url): UrlCheck
    {
        $urlCheck = new UrlCheck($url);

        try {
            $response = Http::get($url->name);
            $urlCheck->statusCode = $response->status();
            if ($response->successful()) {
                $urlCheck = $this->fillUrlCheckEntityWithDataFromBody($urlCheck, $response->body());
            }
        } catch (\Exception $exception) {
            Log::error("Cannot resolve host when storing url check", [
                'exception' => $exception,
                'url_id' => $url->id,
            ]);
        }

        return $this->urlChecksRepository->save($urlCheck);
    }

    public function getAllUrlChecks(Url $url): array
    {
        return $this->urlChecksRepository->getAllForUrl($url->id);
    }

    public function getLatestUrlChecksForUrlsList(array $urls): array
    {
        return $this->urlChecksRepository->getLatestUrlChecksForUrlsList(array_column($urls, 'id'));
    }

    private function fillUrlCheckEntityWithDataFromBody(UrlCheck $urlCheck, string $responseBody): UrlCheck
    {
        $document = new Document($responseBody);

        $urlCheck->h1 = optional($document->first('h1'))->text();
        $urlCheck->keywords = optional($document->first('meta[name="keywords"]'))->attr('content');
        $urlCheck->description = optional($document->first('meta[name="description"]'))->attr('content');

        return $urlCheck;
    }
}
