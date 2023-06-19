<?php

namespace App\Http\Controllers;

use App\Models\Immeuble;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImmeubleRequest;
use App\Http\Requests\UpdateImmeubleRequest;


class ImmeubleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreImmeubleRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Immeuble $immeuble)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Immeuble $immeuble)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImmeubleRequest $request, Immeuble $immeuble)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Immeuble $immeuble)
    {
        //
    }
}
