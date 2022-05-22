<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Services\PreInspectionService;
use Illuminate\Http\Request;

class PreInspectionController extends Controller
{
    protected $preInspectionService;

    public function __construct(PreInspectionService $preInspectionService)
    {
        $this->preInspectionService = $preInspectionService;
    }

    public function edit(Request $request, $id)
    {
        $proposal = Proposal::find($id);
        $data = $request->all();

        $message = $this->preInspectionService->update($proposal->preInspection, $data);
        session()->flash('message', $message);

        return redirect()->route('proposal.edit', [$proposal->id]);
    }
}
