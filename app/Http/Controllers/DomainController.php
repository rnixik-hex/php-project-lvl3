<?php

namespace App\Http\Controllers;

use App\Entities\Domain;
use App\Repositories\DomainRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DomainController extends Controller
{
    public function __construct(private DomainRepository $domainRepository)
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $domain = new Domain();
        $domain->name = $request->domain['name'];
        // @TODO: move to a service
        $this->domainRepository->save($domain);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(string $id)
    {
        $domain = $this->domainRepository->find((int) $id);
        if (!$domain) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return view('show', ['domain' => $domain]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
