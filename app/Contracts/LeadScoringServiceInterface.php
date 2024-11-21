<?php

namespace App\Contracts;

interface LeadScoringServiceInterface
{
    /**
     * Define el contrato para obtener el score de un lead.
     *
     * @param mixed $lead
     * @return int
     */
    public function getLeadScore($lead): int;
}
