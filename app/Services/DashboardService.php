<?php

namespace App\Services;

use App\Models\Proposal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    private const SUB_DAYS = 60;

    public function getDashboardData(): array
    {
        return [
            // Dados para os Contadores
            'proposals' => $this->getProposalsCount(),
            'proposals_sent_count' => $this->getProposalsSentForApproval()['count'],
            'closed_proposals_count' => $this->getClosedProposals()['count'],
            'average_ticket' => $this->getAverageTicket(),
            'total_sales' => $this->getTotalSales(),

            // Dados para os Gráficos
            'comparison_chart_data' => $this->getProposalsComparisonData(),
            'value_by_day_chart_data' => $this->getProposalsValueByDay(),
            'ranking_chart_data' => $this->getRankingUsers()->pluck('proposals_count', 'name'),

            // Dados para as Tabelas/Listagens
            'proposals_sent_clients' => $this->getProposalsSentForApproval()['clients'],
            'closed_proposals_clients' => $this->getClosedProposals()['clients'],
            'ranking_users' => $this->getRankingUsers(),
        ];
    }

    private function getProposalsCount(): int
    {
        return Proposal::where('created_at', '>=', Carbon::now()->subDays(self::SUB_DAYS))
            ->distinct('client_id')
            ->count('client_id');
    }

    private function getProposalsSentForApproval(): array
    {
        $proposals = Proposal::whereNotNull('send_date')
            ->where('send_date', '>=', Carbon::now()->subDays(self::SUB_DAYS))
            ->with('client')
            ->get();

        return [
            'count' => $proposals->count(),
            'clients' => $proposals->pluck('client.name')
        ];
    }

    private function getClosedProposals(): array
    {
        $proposals = Proposal::whereHas('contract.status', function ($query) {
            $query->where('is_final', 1);
        })
            ->where('updated_at', '>=', Carbon::now()->subDays(self::SUB_DAYS))
            ->with('client')
            ->get();

        return [
            'count' => $proposals->count(),
            'clients' => $proposals->pluck('client.name')
        ];
    }

    private function getAverageTicket(): float
    {
        return Proposal::where('proposals.created_at', '>=', Carbon::now()->subDays(self::SUB_DAYS))
            ->join('proposal_value_histories', 'proposals.value_history_id', '=', 'proposal_value_histories.id')
            ->avg('proposal_value_histories.final_price') ?? 0;
    }

    private function getTotalSales(): float
    {
        return Proposal::where('proposals.created_at', '>=', Carbon::now()->subDays(self::SUB_DAYS))
            ->join('proposal_value_histories', 'proposals.value_history_id', '=', 'proposal_value_histories.id')
            ->sum('proposal_value_histories.final_price') ?? 0;
    }

    private function getRankingUsers()
    {
        return Proposal::select(DB::raw('count(*) as proposals_count'), 'users.name')
            ->where('proposals.created_at', '>=', Carbon::now()->subDays(self::SUB_DAYS))
            ->join('users', 'proposals.agent_id', '=', 'users.id')
            ->groupBy('users.name')
            ->orderBy('proposals_count', 'desc')
            ->limit(5)
            ->get();
    }

    // Novos métodos para os gráficos
    private function getProposalsComparisonData(): array
    {
        $totalProposals = Proposal::where('created_at', '>=', Carbon::now()->subDays(self::SUB_DAYS))->count();
        $proposalsSent = Proposal::whereNotNull('send_date')
            ->where('send_date', '>=', Carbon::now()->subDays(self::SUB_DAYS))
            ->count();
        $proposalsClosed = Proposal::whereHas('contract.status', function ($query) {
            $query->where('is_final', 1);
        })->where('updated_at', '>=', Carbon::now()->subDays(self::SUB_DAYS))
            ->count();

        return [
            'labels' => ['Emitidas', 'Aprovação', 'Fechadas'],
            'data' => [$totalProposals, $proposalsSent, $proposalsClosed],
        ];
    }

    private function getProposalsValueByDay(): \Illuminate\Support\Collection
    {
        return Proposal::select(DB::raw('DATE(proposals.created_at) as date'), DB::raw('SUM(proposal_value_histories.final_price) as total_value'))
            ->where('proposals.created_at', '>=', Carbon::now()->subDays(self::SUB_DAYS))
            ->join('proposal_value_histories', 'proposals.value_history_id', '=', 'proposal_value_histories.id')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
