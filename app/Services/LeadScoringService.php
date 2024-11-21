<?php

namespace App\Services;

use App\Contracts\LeadScoringServiceInterface;

class LeadScoringService implements LeadScoringServiceInterface
{
    public function getLeadScore($lead): int
    {
        // El score está en el rango de 0 a 999 (reemplazar por API externa)
        return rand(100, 999); 
    }
}