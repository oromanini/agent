<?php

namespace App\Services;

use App\Models\Proposal;
use App\Services\Contracts\ApprovalInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class ApprovalService implements ApprovalInterface
{
    public const FINANCING = 'Financing';
    public const CONTRACT = 'Contract';
    public const INSPECTION = 'Inspection';
    public const MODEL_PATH = "App\\Models\\";

    private const CONTRACT_FILE = 'contracts';
    private const SIGNED_CONTRACT_FILE = 'signed_contracts';
    private const PROOF_OF_INCOME = 'proof_of_income';

    public function store(string $model, Proposal $proposal, Request $request): void
    {
        $lowerModel = strtolower($model);
        $modelPath = self::MODEL_PATH . $model;

        $savedModel = (new $modelPath)::create($request->all());
        $proposal->$lowerModel()->associate($savedModel);
        $proposal->update();
        ApprovalService::sendToHomologation($proposal);

    }

    public function update(string $model, int $proposalId, Request $request): void
    {
        $lowerModel = strtolower($model);
        $proposal = Proposal::find($proposalId);

        DB::transaction(function () use ($request, $proposal, $lowerModel, $model) {
            is_null($proposal->$lowerModel)
                ? $this->store(model: $model, proposal: $proposal, request: $request)
                : $proposal->$lowerModel->update($request->all());
        });

        $this->checkFiles(
            model: $model,
            proposal: $proposal,
            request: $request
        );
    }

    public function checkFiles(string $model, Proposal $proposal, Request $request): void
    {
        $lowerModel = strtolower($model);

        foreach ($request->allFiles() as $name => $file) {

            $matchFolder = $this->matchFolder(filename: $name);

            (isset($proposal->$lowerModel->$name))
                && $proposal->$lowerModel->$name = $file->store("public/$matchFolder/$proposal->id");

        }

        DB::transaction(function () use ($proposal, $lowerModel) {
            $proposal->$lowerModel->update();
        });
    }

    private function matchFolder(string $filename): string
    {
        return match ($filename) {
            'file' => self::CONTRACT_FILE,
            'signed_file' => self::SIGNED_CONTRACT_FILE,
            'proof_of_income' => self::PROOF_OF_INCOME . "/financing",
            default => $filename,
        };
    }

    public static function sendToHomologation(Proposal $proposal): void
    {
        $homologationService = new HomologationService();

        $conditionToAccept =
            (isset($proposal->inspection) && $proposal->inspection->status->is_final)
            && (isset($proposal->financing) && $proposal->financing->status->is_final)
            && isset($proposal->contract) && ($proposal->contract->status->is_final);

        $conditionToAccept && $homologationService->store($proposal);
    }
}
