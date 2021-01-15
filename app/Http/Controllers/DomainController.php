<?php

namespace App\Http\Controllers;

use App\Services\DomainAnalyzerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class DomainController extends Controller
{
    public function __construct(private DomainAnalyzerService $domainAnalyzerService )
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|array',
            'domain.name' => 'required|string|max:255|url',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('home')
                ->with('error', 'Url is invalid')
                ->withInput();
        }

        $domainEntity = $this->domainAnalyzerService->analyze($request->domain['name']);

        return redirect()
            ->route('domains.show', ['domain' => $domainEntity->id])
            ->with('success', 'Domain has been added');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(string $id)
    {
        $domain = $this->domainAnalyzerService->getSavedDomain((int) $id);
        if (!$domain) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return view('show', ['domain' => $domain]);
    }
}
