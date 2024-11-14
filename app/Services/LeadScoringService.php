<?php

namespace App\Services;

class LeadScoringService
{
    public function getLeadScore($lead)
    {
        // El score está en el rango de 0 a 999 (reemplazar por API externa)
        return rand(100, 999); 
    }
}
