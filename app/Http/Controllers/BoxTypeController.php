<?php
namespace App\Http\Controllers;

use App\Models\BoxType;
use Illuminate\Http\Request;

class BoxTypeController extends Controller
{
    public function index()
    {
        return BoxType::all();
    }

    public function store(Request $request)
    {
        return BoxType::create($request->all());
    }

    public function show(BoxType $boxType)
    {
        return $boxType;
    }

    public function update(Request $request, BoxType $boxType)
    {
        $boxType->update($request->all());
        return $boxType;
    }

    public function destroy(BoxType $boxType)
    {
        $boxType->delete();
        return response()->noContent();
    }
}