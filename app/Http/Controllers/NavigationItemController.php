<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\NavigationItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NavigationItemController extends Controller
{
    public function index(): View
    {
        $items = NavigationItem::query()
            ->with('module')
            ->orderBy('section')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        return view('navigation-items.index', compact('items'));
    }

    public function create(): View
    {
        return view('navigation-items.create', [
            'modules' => Module::query()->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        NavigationItem::create($this->validated($request));

        return redirect()->route('navigation-items.index')->with('success', 'Entrada de navegación creada correctamente.');
    }

    public function edit(NavigationItem $navigationItem): View
    {
        return view('navigation-items.edit', [
            'navigationItem' => $navigationItem,
            'modules' => Module::query()->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, NavigationItem $navigationItem): RedirectResponse
    {
        $navigationItem->update($this->validated($request));

        return redirect()->route('navigation-items.index')->with('success', 'Entrada de navegación actualizada correctamente.');
    }

    public function destroy(NavigationItem $navigationItem): RedirectResponse
    {
        $navigationItem->delete();

        return redirect()->route('navigation-items.index')->with('success', 'Entrada de navegación eliminada correctamente.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'module_id' => ['nullable', 'exists:modules,id'],
            'section' => ['required', 'string', 'max:60'],
            'label' => ['required', 'string', 'max:100'],
            'route_name' => ['required', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:80'],
            'permission_action' => ['required', 'in:view,create,update,delete,approve,export,sync'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'admin_only' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
        ]) + [
            'admin_only' => $request->boolean('admin_only'),
            'active' => $request->boolean('active'),
        ];

        if (! Route::has($data['route_name'])) {
            throw ValidationException::withMessages([
                'route_name' => 'La ruta indicada no existe en la aplicación.',
            ]);
        }

        return $data;
    }
}
