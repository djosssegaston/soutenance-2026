<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Arrondissement;
use App\Models\Commune;
use App\Models\Departement;
use App\Models\Pay;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function pays(): JsonResponse
    {
        return response()->json(
            Pay::where('actif', true)->orderBy('nom')->get(['id', 'nom', 'code', 'indicatif'])
        );
    }

    public function departements(Pay $pay): JsonResponse
    {
        return response()->json(
            $pay->departements()->orderBy('nom')->get(['id', 'pays_id', 'nom', 'code'])
        );
    }

    public function communes(Departement $departement): JsonResponse
    {
        return response()->json(
            $departement->communes()->orderBy('nom')->get(['id', 'departement_id', 'nom', 'code'])
        );
    }

    public function arrondissements(Commune $commune): JsonResponse
    {
        return response()->json(
            $commune->arrondissements()->orderBy('nom')->get(['id', 'commune_id', 'nom'])
        );
    }

    public function quartiers(Arrondissement $arrondissement): JsonResponse
    {
        return response()->json(
            $arrondissement->quartiers()->orderBy('nom')->get(['id', 'arrondissement_id', 'nom'])
        );
    }
}
