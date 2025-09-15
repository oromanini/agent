<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function __construct(private readonly BrandService $brandService)
    {}

    public function index(): View
    {
        $panelBrands = Brand::panels();
        $inverterBrands = Brand::inverters();

        return view('brands.index', compact('panelBrands', 'inverterBrands'));
    }

    public function store(Request $request, $type): JsonResponse
    {
        if (!in_array($type, ['panel', 'inverter'])) {
            abort(404, 'Tipo de marca inválido.');
        }

        $validated = $request->validate([
            'brand' => 'required|string|max:255|unique:brands,name,NULL,id,type,' . $type,
            'warranty' => 'required|integer',
            'linear_warranty' => 'nullable|integer',
            'overload' => 'nullable|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $validated['name'] = Str::upper($validated['brand']);
        unset($validated['brand']);
        $validated['type'] = $type;

        $brandNameSlug = Str::slug($validated['name']);
        $save_type = $type === 'panel' ? 'module' : 'inverter';

        if ($request->hasFile('logo')) {
            $extension = $request->file('logo')->getClientOriginalExtension();
            $fileName = "{$brandNameSlug}.{$extension}";
            $validated['logo'] = $request->file('logo')->storeAs($save_type . '_brand_logos', $fileName, 'public');
        }
        if ($request->hasFile('picture')) {
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileName = "{$brandNameSlug}.{$extension}";
            $validated['picture'] = $request->file('picture')->storeAs($save_type . '_brand_pictures', $fileName, 'public');
        }

        $brand = $this->brandService->createBrand($validated);

        return response()->json($brand, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);
        $type = $brand->type;

        $validated = $request->validate([
            'brand' => [
                'required', 'string', 'max:255',
                Rule::unique('brands', 'name')->where('type', $type)->ignore($brand->id)
            ],
            'brand_enum' => 'required|integer',
            'warranty' => 'required|integer',
            'linear_warranty' => 'nullable|integer',
            'overload' => 'nullable|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $validated['name'] = Str::upper($validated['brand']);
        unset($validated['brand']);

        $brandNameSlug = Str::slug($validated['name']);

        if ($request->hasFile('logo')) {
            if ($brand->logo) Storage::disk('public')->delete($brand->logo);
            $extension = $request->file('logo')->getClientOriginalExtension();
            $fileName = "{$brandNameSlug}.{$extension}";
            $validated['logo'] = $request->file('logo')->storeAs($type . '_brand_logos', $fileName, 'public');
        }

        if ($request->hasFile('picture')) {
            if ($brand->picture) Storage::disk('public')->delete($brand->picture);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileName = "{$brandNameSlug}.{$extension}";
            $validated['picture'] = $request->file('picture')->storeAs($type . '_brand_pictures', $fileName, 'public');
        }

        $this->brandService->update($brand, $validated);

        return response()->json($brand);
    }

    public function destroy(int $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);

        if ($brand->logo) Storage::disk('public')->delete($brand->logo);
        if ($brand->picture) Storage::disk('public')->delete($brand->picture);

        $this->brandService->delete($brand);

        return response()->json(null, 204);
    }
}
