<?php

namespace App\Jobs;

use App\Models\Pricing\ExternalConsultantsCommissionCost;
use App\Models\ProposalValueHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateProposalsValueHistoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ProposalValueHistory $proposalValueHistory;

    public function handle(): void
    {
        (ProposalValueHistory::all())->each(function ($valueHistory) {
            try {
                $this->proposalValueHistory = $valueHistory;
                $this->updateOldValueHistory();
            } catch (\Throwable $exception) {
                Log::info("Erro ao atualizar VALUE_HISTORY_ID {$valueHistory->id}: {$exception->getMessage()}");
            }
        });
    }

    private function updateOldValueHistory(): void
    {
        $isOld = !is_null($this->proposalValueHistory->final_price)
        && is_null($this->proposalValueHistory->card_final_price)
        && is_null($this->proposalValueHistory->cash_final_price);

        $notHaveCommission = is_null($this->proposalValueHistory->commission);

        if ($isOld) {
            $this->proposalValueHistory->card_final_price = $this->proposalValueHistory->final_price;
            $this->proposalValueHistory->cash_final_price = $this->proposalValueHistory->final_price;

            $this->proposalValueHistory->card_initial_price = $this->proposalValueHistory->initial_price;
            $this->proposalValueHistory->cash_initial_price = $this->proposalValueHistory->initial_price;

            $this->proposalValueHistory->commission = $this->setNewVersionCommission();
            $this->proposalValueHistory->commission_percent = 0;
            $this->proposalValueHistory->update();
            Log::info("Value history with ID {$this->proposalValueHistory->id} has been updated to new pricing format!");
        }
    }

    private function setNewVersionCommission(): string
    {
        $oldCommissionPercent = $this->proposalValueHistory->commission_percent;

        $commission = [
            ExternalConsultantsCommissionCost::STANDARD_KEY => $oldCommissionPercent,
            ExternalConsultantsCommissionCost::CREDIT_CARD_KEY => $oldCommissionPercent,
        ];

        return json_encode($commission);
    }
}
