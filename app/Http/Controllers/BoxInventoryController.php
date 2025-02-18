<?php

namespace App\Http\Controllers;

use App\Models\BoxInventory;
use Illuminate\Http\Request;

class BoxInventoryController extends Controller
{
    public function index()
    {
        return BoxInventory::all();
    }

    public function store(Request $request)
    {
        return BoxInventory::create($request->all());
    }

    public function show(BoxInventory $boxInventory)
    {
        return $boxInventory;
    }

    public function update(Request $request, BoxInventory $boxInventory)
    {
        $boxInventory->update($request->all());
        return $boxInventory;
    }

    public function destroy(BoxInventory $boxInventory)
    {
        $boxInventory->delete();
        return response()->noContent();
    }
}