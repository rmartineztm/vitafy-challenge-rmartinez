<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lead;
use App\Services\LeadScoringService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    protected $scoringService;

    public function __construct(LeadScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    public function store(Request $request)
    {
        // Crear el lead
        $lead = Lead::create($request->all());

        // Obtener y asignar el score al lead
        $lead->score = $this->scoringService->getLeadScore($lead);
        $lead->save();

        // Crear el cliente asociado al lead
        $client = Client::create([
            'lead_id' => $lead->id,
        ]);        

        return response()->json(['lead' => $lead, 'client' => $client], 201);
    }

    // Otros métodos
}

