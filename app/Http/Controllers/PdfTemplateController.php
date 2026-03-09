<?php

namespace App\Http\Controllers;

use App\Models\PdfTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PdfTemplateController extends Controller
{
    public function edit(): View
    {
        abort_unless(auth()->user()->isAdmin, 403);

        $template = PdfTemplate::activeProposalTemplate();

        return view('pdf_templates.edit', compact('template'));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'html' => ['required', 'string'],
            'css' => ['nullable', 'string'],
        ]);

        PdfTemplate::query()->where('type', 'proposal')->update(['is_active' => false]);

        PdfTemplate::query()->create([
            'name' => $validated['name'],
            'type' => 'proposal',
            'html' => $validated['html'],
            'css' => $validated['css'] ?? null,
            'is_active' => true,
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('pdf-templates.edit')
            ->with('message', 'Template de proposta atualizado com sucesso.');
    }
}
