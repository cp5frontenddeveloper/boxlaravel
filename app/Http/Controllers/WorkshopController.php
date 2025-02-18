<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    public function index()
    {
        return Workshop::all();
    }

    public function store(Request $request)
    {
        return Workshop::create($request->all());
    }

    public function show(Workshop $workshop)
    {
        return $workshop;
    }

    public function update(Request $request, Workshop $workshop)
    {
        $workshop->update($request->all());
        return $workshop;
    }

    public function destroy(Workshop $workshop)
    {
        $workshop->delete();
        return response()->noContent();
    }
}