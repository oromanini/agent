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
        $moduleBrands = Brand::panels();
        $inverterBrands = Brand::inverters();

        return view('brands.index', compact('moduleBrands', 'inverterBrands'));
    }

    public function store(Request $request, $type): JsonResponse
    {
        if (!in_array($type, ['panel', 'inverter'])) {
            abort(404, 'Tipo de marca inválido.');
        }

        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'warranty' => 'required|integer',
            'linear_warranty' => 'nullable|integer',
            'overload' => 'nullable|numeric',
            'active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $brandNameSlug = Str::slug($validated['brand']);

        if ($request->hasFile('logo')) {
            $extension = $request->file('logo')->getClientOriginalExtension();
            $fileName = "{$brandNameSlug}.{$extension}";
            $validated['logo'] = $request->file('logo')->storeAs($type . '_brand_logos', $fileName, 'public');
        }
        if ($request->hasFile('picture')) {
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileName = "{$brandNameSlug}.{$extension}";
            $validated['picture'] = $request->file('picture')->storeAs($type . '_brand_pictures', $fileName, 'public');
        }

        $brand = $this->brandService->createBrand($validated, $type);

        return response()->json($brand, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);
        $type = $brand->type;

        $request->merge(['name' => Str::upper($request->input('name'))]);

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('brands')->where('type', $type)->ignore($brand->id)
            ],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $brandNameSlug = Str::slug($validated['name']);

        if ($request->hasFile('logo')) {
            if ($brand->logo_path) Storage::disk('public')->delete($brand->logo_path);
            $extension = $request->file('logo')->getClientOriginalExtension();
            $fileName = "{$brandNameSlug}.{$extension}";
            $validated['logo_path'] = $request->file('logo')->storeAs($type . '_brand_logos', $fileName, 'public');
        }

        if ($request->hasFile('picture')) {
            if ($brand->picture_path) Storage::disk('public')->delete($brand->picture_path);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileName = "{$brandNameSlug}.{$extension}";
            $validated['picture_path'] = $request->file('picture')->storeAs($type . '_brand_pictures', $fileName, 'public');
        }

        $this->brandService->update($brand, $validated);

        return response()->json($brand);
    }

    public function destroy(int $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);

        if ($brand->logo_path) Storage::disk('public')->delete($brand->logo_path);
        if ($brand->picture_path) Storage::disk('public')->delete($brand->picture_path);

        $this->brandService->delete($brand);

        return response()->json(null, 204);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);
        $brand->save();
        return response()->json($brand);
    }
}
