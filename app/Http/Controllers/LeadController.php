<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lead;
use App\Http\Resources\LeadResource;
use App\Contracts\LeadScoringServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LeadController extends Controller
{
    public function show(string $uuid)
    {
        // Busca el lead por UUID
        $lead = Lead::where('uuid', $uuid)->first();

        if (!$lead) {
            return response()->json(['message' => 'Lead no encontrado'], Response::HTTP_NOT_FOUND);
        }

        // Retorna el lead encontrado
        return (new LeadResource($lead))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email',
            'phone' => 'nullable|string|max:20',
        ]);

        // Crear el lead
        $lead = Lead::create($validated);

        // Obtener y asignar el score al lead
        $lead->score = app(LeadScoringServiceInterface::class)->getLeadScore($lead);
        $lead->save();

        // Crear el cliente asociado al lead
        $client = Client::create([
            'lead_id' => $lead->id,
        ]);        

        return (new LeadResource($lead))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(Request $request, string $uuid)
    {
        // Verifica si la solicitud está vacía
        if ($request->isMethod('put') && !$request->all()) {
            return response()->json([
                'message' => 'Se debe enviar al menos un dato.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Validar los datos proporcionados
        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:leads,email,' . $uuid . ',uuid',
            'phone' => 'sometimes|nullable|string|max:20',
        ]);
    
        // Buscar el lead por UUID
        $lead = Lead::where('uuid', $uuid)->first();
    
        if (!$lead) {
            return response()->json(['message' => 'Lead no encontrado'], Response::HTTP_NOT_FOUND);
        }
    
        // Sobrescribir explícitamente el campo 'phone' con null si no se envía
        if (!$request->has('phone')) {
            $validated['phone'] = null;
        }    
          

        // Actualizar solo los campos enviados
        $lead->update(array_merge($validated, [
            'phone' => $request->has('phone') ? $request->input('phone') : null,
            'score' => $request->has('score') ? $request->input('score') : null,
        ]));  

        // Si alguno de los campos relevantes cambió (name, email, phone), recalcular el score
        if ($request->hasAny(['name', 'email', 'phone'])) {
            $lead->score = app(LeadScoringServiceInterface::class)->getLeadScore($lead);
        }

        // Si el 'score' está en la solicitud, mantenemos el valor enviado (incluso si es null)
        if ($request->has('score')) {
            $lead->score = $request->input('score');
        }    

        // Guardar el lead con el nuevo valor de score
        $lead->save();        
                
        return (new LeadResource($lead->refresh()))->response()->setStatusCode(Response::HTTP_OK);
    }                 
    
    public function destroy(string $uuid)
    {
        // Busca el lead por UUID
        $lead = Lead::where('uuid', $uuid)->first();

        if (!$lead) {
            return response()->json(['message' => 'Lead no encontrado'], Response::HTTP_NOT_FOUND);
        }

        // Elimina el lead y el cliente asociado por cascada
        $lead->delete();

        return response()->json(['message' => 'Lead eliminado correctamente'], Response::HTTP_OK);
    }    
}

