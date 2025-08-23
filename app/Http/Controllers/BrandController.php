<?php

namespace App\Http\Controllers;

use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\ModuleBrand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(): View
    {
        $moduleBrands = ModuleBrand::orderBy('brand')->get();
        $inverterBrands = InverterBrand::orderBy('brand')->get();

        return view('brands.index', compact('moduleBrands', 'inverterBrands'));
    }

    public function store(Request $request, $type): JsonResponse
    {
        $model = $this->getModelInstance($type);

        $request->merge(['brand' => Str::upper($request->input('brand'))]);

        $rules = [
            'brand' => ['required', 'string', 'max:255', Rule::unique($model->getConnectionName() . '.' . $model->getTable())],
            'warranty' => 'required|integer|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ];

        if ($type === 'inverter') {
            $rules['overload'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store($type . '_brand_logos', 'public');
        }
        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store($type . '_brand_pictures', 'public');
        }

        $brand = $model->create($validated);

        $brand->logo_url = $brand->logo ? Storage::url($brand->logo) : null;
        $brand->picture_url = $brand->picture ? Storage::url($brand->picture) : null;

        return response()->json($brand, 201);
    }

    public function update(Request $request, string $type, int $id): JsonResponse
    {
        $model = $this->getModelInstance($type);
        $brand = $model->findOrFail($id);

        $request->merge(['brand' => Str::upper($request->input('brand'))]);

        $rules = [
            'brand' => ['required', 'string', 'max:255', Rule::unique($brand->getConnectionName() . '.' . $brand->getTable())->ignore($brand->id)],
            'warranty' => 'required|integer|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ];

        if ($type === 'inverter') {
            $rules['overload'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        $brand->fill($validated);

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $brand->logo = $request->file('logo')->store($type . '_brand_logos', 'public');
        }

        if ($request->hasFile('picture')) {
            if ($brand->picture) {
                Storage::disk('public')->delete($brand->picture);
            }
            $brand->picture = $request->file('picture')->store($type . '_brand_pictures', 'public');
        }

        $brand->save();

        $brand->logo_url = $brand->logo ? Storage::url($brand->logo) : null;
        $brand->picture_url = $brand->picture ? Storage::url($brand->picture) : null;

        return response()->json($brand);
    }

    public function destroy($type, $id): JsonResponse
    {
        $model = $this->getModelInstance($type);
        $brand = $model->findOrFail($id);

        if ($brand->logo) Storage::disk('public')->delete($brand->logo);
        if ($brand->picture) Storage::disk('public')->delete($brand->picture);

        $brand->delete();

        return response()->json(null, 204);
    }

    public function toggleActive($type, $id): JsonResponse
    {
        try {
            $model = $this->getModelInstance($type);
            $brand = $model->findOrFail($id);
            $brand->active = !$brand->active;
            $brand->save();
            return response()->json($brand);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function getModelInstance($type): ModuleBrand|InverterBrand
    {
        if ($type === 'module') {
            return new ModuleBrand();
        } elseif ($type === 'inverter') {
            return new InverterBrand();
        }
        abort(404, 'Tipo de marca inválido.');
    }
}
