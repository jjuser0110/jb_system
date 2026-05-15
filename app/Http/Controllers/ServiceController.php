<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * LISTING
     */
    public function index()
    {
        $services = Service::latest()->get();

        return view('services.index', compact('services'));
    }

    /**
     * CREATE FORM
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Service::firstOrCreate([
            'name' => trim($request->name)
        ]);

        return redirect()->route('services.index')
            ->with('success', 'Service created');
    }

    /**
     * EDIT FORM
     */
    public function edit(Service $service)
    {
        return view('services.create', compact('service'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|unique:services,name,' . $service->id
        ]);

        $service->update([
            'name' => trim($request->name)
        ]);

        return redirect()
            ->route('services.index')
            ->with('success', 'Service updated successfully');
    }

    /**
     * DELETE
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()
            ->route('services.index')
            ->with('success', 'Service deleted successfully');
    }
}