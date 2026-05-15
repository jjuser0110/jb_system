<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Service::firstOrCreate([
            'name' => trim($request->name)
        ]);

        return redirect()->route('service.index')
            ->with('success', 'Service created');
    }
}
