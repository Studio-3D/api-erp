<?php

namespace App\Http\Controllers;

use App\Http\Helpers\DatabaseHelper;
use App\Http\Helpers\RoleHelper;
use App\Http\Requests\StoreSourceRequest;
use App\Models\Source;
use Database\Seeders\SourceSeeder;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $source=Source::on('temp')->get();
            return response()->json(['source',$source],200);
        }
       else  return response()->json(['error'=>'Unauthorized'], 401);
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
    public function store(StoreSourceRequest $request)
    {
        if(RoleHelper::ACSup()){
            DatabaseHelper::Config();
            $source=new Source();
            $source->setConnection('temp');
            $source->source=$request->source;
            $source->save();
            return response()->json(['$source'=>$source],200);
        }
        else return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
