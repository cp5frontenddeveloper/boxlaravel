<?php

namespace App\Http\Controllers;

use App\Models\BoxStatus;
use Illuminate\Http\Request;

class BoxStatusController extends Controller
{
    public function index()
    {
        return BoxStatus::all();
    }

    public function store(Request $request)
    {
        return BoxStatus::create($request->all());
    }

    public function show(BoxStatus $boxStatus)
    {
        return $boxStatus;
    }

    public function update(Request $request, BoxStatus $boxStatus)
    {
        $boxStatus->update($request->all());
        return $boxStatus;
    }

    public function destroy(BoxStatus $boxStatus)
    {
        $boxStatus->delete();
        return response()->noContent();
    }
}