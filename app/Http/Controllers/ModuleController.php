<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(): View
    {
        $modules = Module::withCount('permissions')
            ->with(['navigationItems' => fn ($query) => $query
                ->where('active', true)
                ->orderBy('section')
                ->orderBy('sort_order')])
            ->where('active', true)
            ->orderBy('group_name')
            ->orderBy('sort_order')
            ->get();

        return view('modules.index', compact('modules'));
    }

    public function update(Request $request, Module $module): RedirectResponse
    {
        $request->validate([
            'active' => ['required', 'boolean'],
        ]);

        $module->active = $request->boolean('active');
        $module->save();

        return redirect()->route('modules.index')->with('success', 'Módulo actualizado correctamente.');
    }
}
