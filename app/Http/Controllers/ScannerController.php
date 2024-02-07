<?php

namespace App\Http\Controllers;

use App\Models\Scanner;
use App\Http\Requests\StoreScannerRequest;
use App\Http\Requests\UpdateScannerRequest;

class ScannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScannerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Scanner $scanner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScannerRequest $request, Scanner $scanner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scanner $scanner)
    {
        //
    }
}
