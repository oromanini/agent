<?php

namespace App\Http\Controllers;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\SoolarApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SoollarController extends Controller
{
    public function __construct(private readonly SoolarApiService $soolarApiService)
    {}

    public function index(): JsonResponse
    {
        $errors = [];
        $results = [];

        foreach (ProductCategoriesEnum::cases() as $category) {
            foreach (WarehouseEnum::cases() as $warehouse) {
                try {
                    $this->soolarApiService->handle(
                        category: $category,
                        warehouse: $warehouse,
                    );
                    $results[] = "Atualização bem-sucedida para a categoria '{$category->value}' no armazém '{$warehouse->value}'.";

                } catch (\Throwable $exception) {
                    $errors[] = "Erro ao atualizar a categoria '{$category->value}' no armazém '{$warehouse->value}': {$exception->getMessage()}";
                }
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Ocorreram erros durante a atualização.',
                'errors' => $errors,
                'results' => $results
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Todas as combinações de kits foram atualizadas com sucesso.',
            'results' => $results
        ], Response::HTTP_OK);
    }
}

