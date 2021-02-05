<?php

namespace App\Http\Controllers;

use App\Services\UrlAlreadyExistsException;
use App\Services\UrlAnalyzerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UrlController extends Controller
{
    public function __construct(private UrlAnalyzerService $urlAnalyzerService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
     */
    public function index()
    {
        $urls = $this->urlAnalyzerService->getAllSavedUrls();
        $latestUrlChecks = $this->urlAnalyzerService->getLatestUrlChecksForUrlsList($urls);

        return view('index', [
            'urls' => $urls,
            'latestUrlChecks' => $latestUrlChecks,
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
            'url' => 'required|array',
            'url.name' => 'required|string|max:255|url',
        ]);

        if ($validator->fails()) {
            flash('Url is invalid')->error();
            /* @phpstan-ignore-next-line */
            return redirect()
                ->route('home')
                ->withInput();
        }

        try {
            $urlEntity = $this->urlAnalyzerService->analyze($request->get('url')['name']);
        } catch (UrlAlreadyExistsException $exception) {
            flash('Url already exists')->info();
            /* @phpstan-ignore-next-line */
            return redirect()
                ->route('urls.show', ['url' => $exception->existedUrl->id]);
        }

        flash('Url has been added')->success();
        /* @phpstan-ignore-next-line */
        return redirect()
            ->route('urls.show', ['url' => $urlEntity->id]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|null
     */
    public function show(string $id)
    {
        $url = $this->urlAnalyzerService->getSavedUrl((int) $id);
        if ($url === null) {
            abort(Response::HTTP_NOT_FOUND);
            /* @phpstan-ignore-next-line */
            return null; // php stan
        }

        $urlChecks = $this->urlAnalyzerService->getAllUrlChecks($url);

        return view('show', [
            'url' => $url,
            'urlChecks' => $urlChecks,
        ]);
    }

    public function storeCheck(string $id): ?RedirectResponse
    {
        $url = $this->urlAnalyzerService->getSavedUrl((int) $id);
        if ($url === null) {
            abort(Response::HTTP_NOT_FOUND);
            /* @phpstan-ignore-next-line */
            return null; // php stan
        }

        try {
            $this->urlAnalyzerService->createNewUrlCheck($url);
            flash('Url check has been added')->success();
            /* @phpstan-ignore-next-line */
            return redirect()
                ->route('urls.show', ['url' => $url->id]);
        } catch (\Exception $exception) {
            Log::error("Error when store url check", [
                'exception' => $exception,
                'url_id' => $url->id,
            ]);
            flash($exception->getMessage())->error();
            /* @phpstan-ignore-next-line */
            return redirect()
                ->route('urls.show', ['url' => $url->id]);
        }
    }
}
