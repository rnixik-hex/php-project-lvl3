<?php

namespace App\Http\Controllers;

use App\Services\DomainAnalyzerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class DomainController extends Controller
{
    public function __construct(private DomainAnalyzerService $domainAnalyzerService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
     */
    public function index()
    {
        $domains = $this->domainAnalyzerService->getAllSavedDomains();
        $latestDomainChecks = $this->domainAnalyzerService->getLatestDomainChecksForDomainsList($domains);

        return view('index', [
            'domains' => $domains,
            'latestDomainChecks' => $latestDomainChecks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Routing\Redirector|RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|array',
            'domain.name' => 'required|string|max:255|url',
        ]);

        if ($validator->fails()) {
            /* @phpstan-ignore-next-line */
            return redirect()
                ->route('home')
                ->with('error', 'Url is invalid')
                ->withInput();
        }

        $domainEntity = $this->domainAnalyzerService->analyze($request->get('domain')['name']);

        /* @phpstan-ignore-next-line */
        return redirect()
            ->route('domains.show', ['domain' => $domainEntity->id])
            ->with('success', 'Domain has been added');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|null
     */
    public function show(string $id)
    {
        $domain = $this->domainAnalyzerService->getSavedDomain((int) $id);
        if (!$domain) {
            abort(Response::HTTP_NOT_FOUND);
            return null; // php stan
        }

        $domainChecks = $this->domainAnalyzerService->getAllDomainChecks($domain);

        return view('show', [
            'domain' => $domain,
            'domainChecks' => $domainChecks,
        ]);
    }

    public function storeCheck(string $id): ?RedirectResponse
    {
        $domain = $this->domainAnalyzerService->getSavedDomain((int) $id);
        if (!$domain) {
            abort(Response::HTTP_NOT_FOUND);
            return null; // php stan
        }

        $this->domainAnalyzerService->createNewDomainCheck($domain);

        /* @phpstan-ignore-next-line */
        return redirect()
            ->route('domains.show', ['domain' => $domain->id])
            ->with('success', 'Domain check has been added');
    }
}
