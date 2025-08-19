<?php

namespace App\Http\Controllers;

use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\ModuleBrand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index()
    {
        $moduleBrands = ModuleBrand::orderBy('brand')->get();
        $inverterBrands = InverterBrand::orderBy('brand')->get();

        return view('brands.index', compact('moduleBrands', 'inverterBrands'));
    }

    public function store(Request $request, $type)
    {
        $model = $this->getModelInstance($type);
        $connectionName = $model->getConnectionName();

        $validated = $request->validate([
            'brand' => ['required', 'string', 'max:255', Rule::unique($connectionName . '.' . $model->getTable(), 'brand')],
            'warranty' => 'required|integer|min:1',
        ]);

        $brand = $model->create($validated);

        return response()->json($brand, 201);
    }

    public function update(Request $request, $type, $id)
    {
        $model = $this->getModelInstance($type);
        $brand = $model->findOrFail($id);
        $connectionName = $model->getConnectionName();

        $validated = $request->validate([
            'brand' => ['required', 'string', 'max:255', Rule::unique($connectionName . '.' . $model->getTable(), 'brand')->ignore($brand->id)],
            'warranty' => 'required|integer|min:1',
        ]);

        $brand->update($validated);

        return response()->json($brand);
    }

    public function destroy($type, $id)
    {
        $model = $this->getModelInstance($type);
        $brand = $model->findOrFail($id);
        $brand->delete();

        return response()->json(null, 204);
    }

    public function toggleActive($type, $id)
    {
        try {
            $model = $this->getModelInstance($type);
            $brand = $model->findOrFail($id);

            $brand->active = !$brand->active;
            $brand->save();

            return response()->json($brand);
        } catch (\Exception $e) {
            // Retorna uma resposta de erro 500 com a mensagem da exceção
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function getModelInstance($type)
    {
        if ($type === 'module') {
            return new ModuleBrand();
        } elseif ($type === 'inverter') {
            return new InverterBrand();
        }
        abort(404, 'Tipo de marca inválido.');
    }
}
