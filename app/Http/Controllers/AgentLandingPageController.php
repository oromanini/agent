<?php

namespace App\Http\Controllers;

use App\Models\CrmAgentLead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgentLandingPageController extends Controller
{
    public function show(): View
    {
        return view('landingpage');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:30',
        ]);

        CrmAgentLead::create([
            ...$data,
            'status' => 'novo',
            'created_by' => null,
        ]);

        return redirect()
            ->route('landingpage.show')
            ->with('success', 'Recebemos seu cadastro! Nosso time entrará em contato em breve.');
    }
}
