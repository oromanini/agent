<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Financing;
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

        $model = (new $modelPath)::create($request->all());
        $proposal->$lowerModel()->associate($model);
        $proposal->update();
    }

    public function update(string $model, int $proposalId, Request $request): void
    {
        $lowerModel = strtolower($model);
        $proposal = Proposal::find($proposalId);

        DB::transaction(function () use ($request, $proposal, $lowerModel, $model) {
            is_null($proposal->$lowerModel)
                ? $this->store(model: $model, proposal: $proposal, request: $request)
                : $this->updateModel(model: $proposal->$lowerModel, data: $request->all());
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

            if (isset($proposal->$lowerModel->$name)) {
                $proposal->$lowerModel->$name = $file->store("public/$matchFolder/$proposal->id");
            }
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

    private function updateModel(object $model, array $data): void
    {
        $model->update($data);

        if ($model instanceof Contract) {
            $financing = $model->proposal->financing;
            $inspection = $model->proposal->inspection;

            ($model->status->is_final && $financing->status->is_final && $inspection->status->is_final)
                && $this->sendToHomologation($model->proposal);

        } elseif ($model instanceof Financing) {
            $inspection = $model->proposal->inspection;
            $contract = $model->proposal->contract;

            ($model->status->is_final && $contract->status->is_final && $inspection->status->is_final)
                && $this->sendToHomologation($model->proposal);

        } else {
            $contract = $model->proposal->contract;
            $financing = $model->proposal->financing;

            ($model->status->is_final && $contract->status->is_final && $financing->status->is_final)
                && $this->sendToHomologation($model->proposal);
        }
    }

    private function sendToHomologation(Proposal $proposal): void
    {
        $homologationService = new HomologationService();

        $homologationService->store($proposal);
    }
}
