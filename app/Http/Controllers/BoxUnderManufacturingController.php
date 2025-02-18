<?php
namespace App\Http\Controllers;

use App\Models\BoxUnderManufacturing;
use Illuminate\Http\Request;

class BoxUnderManufacturingController extends Controller
{
    public function index()
    {
        return BoxUnderManufacturing::all();
    }

    public function store(Request $request)
    {
        return BoxUnderManufacturing::create($request->all());
    }

    public function show(BoxUnderManufacturing $boxUnderManufacturing)
    {
        return $boxUnderManufacturing;
    }

    public function update(Request $request, BoxUnderManufacturing $boxUnderManufacturing)
    {
        $boxUnderManufacturing->update($request->all());
        return $boxUnderManufacturing;
    }

    public function destroy(BoxUnderManufacturing $boxUnderManufacturing)
    {
        $boxUnderManufacturing->delete();
        return response()->noContent();
    }
}