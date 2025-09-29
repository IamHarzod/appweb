<?php

namespace App\Http\Controllers;

use App\Models\OderItem;
use App\Http\Requests\StoreOderItemRequest;
use App\Http\Requests\UpdateOderItemRequest;

class OderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       
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
    public function store(StoreOderItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OderItem $oderItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OderItem $oderItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOderItemRequest $request, OderItem $oderItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OderItem $oderItem)
    {
        //
    }
}
