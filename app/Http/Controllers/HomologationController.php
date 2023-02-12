<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\HomologationRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomologationController extends Controller
{
    public function __construct(
        private readonly HomologationRepository $homologationRepository
    ) {
    }

    public function index(Request $request): View
    {
        $homologations = $this->homologationRepository->filter($request->all());
        $agents = User::all();

        return view('homologation.index', compact('homologations', 'agents'));
    }
}
