<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkshopRequest;
use App\Http\Requests\UpdateWorkshopRequest;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WorkshopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Workshops/Index', [
            'workshops' => Workshop::latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Workshops/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkshopRequest $request): RedirectResponse
    {
        Workshop::create($request->validated());

        return redirect()->route('admin.workshops.index')
            ->with('message', 'Workshop created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Workshop $workshop): Response
    {
        return Inertia::render('Workshops/Show', [
            'workshop' => $workshop,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workshop $workshop): Response
    {
        return Inertia::render('Workshops/Edit', [
            'workshop' => $workshop,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkshopRequest $request, Workshop $workshop): RedirectResponse
    {
        $workshop->update($request->validated());

        return redirect()->route('admin.workshops.index')
            ->with('message', 'Workshop updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workshop $workshop): RedirectResponse
    {
        $workshop->delete();

        return redirect()->route('admin.workshops.index')
            ->with('message', 'Workshop deleted successfully.');
    }
}
