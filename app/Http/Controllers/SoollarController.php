<?php

namespace App\Http\Controllers;

use App\Models\Kit;
use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\KitsManager;
use App\Packages\SoolarApiPackage\SoollarApiManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SoollarController extends Controller
{
    public function __construct(
        private readonly SoollarApiManager $soolarApiManager,
        private readonly KitsManager $kitsManager,
    ) {}

    public function index(): JsonResponse
    {
        $errors = [];
        $results = [];
        ini_set('max_execution_time', 300);
        foreach (ProductCategoriesEnum::cases() as $category) {
            foreach (WarehouseEnum::cases() as $warehouse) {
                try {
                    $this->soolarApiManager->handle(
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
                'message' => 'Ocorreram erros durante a atualização dos itens.',
                'errors' => $errors,
                'results' => $results
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            $kitsCount = $this->kitsManager->handle();

            // Verifique se a quantidade de kits é zero para retornar um erro
            if ($kitsCount === 0) {
                return response()->json([
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'O processo foi concluído, mas nenhum kit foi cadastrado.',
                    'total' => $kitsCount
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Todas as combinações de kits foram atualizadas com sucesso.',
                'total' => $kitsCount
            ], Response::HTTP_OK);

        } catch (\Throwable $exception) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Erro ao atualizar os kits: ' . $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
                'total' => 0
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
