<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NasaService;

class NasaController extends Controller
{
    private NasaService $nasaService;

    public function __construct(NasaService $nasaService)
    {
        $this->nasaService = $nasaService;
    }

    private function getValidatedDates(Request $request): array
    {
        return [
            'startDate' => $request->query('startDate', now()->subDays(30)->format('Y-m-d')),
            'endDate' => $request->query('endDate', now()->format('Y-m-d')),
        ];
    }

    public function getInstruments(Request $request)
    {
        $dates = $this->getValidatedDates($request);

        return response()->json([
            'instruments' => $this->nasaService->getInstruments($dates['startDate'], $dates['endDate'])
        ]);
    }

    public function getLinkedEvents(Request $request)
    {
        $dates = $this->getValidatedDates($request);

        return response()->json([
            'activityIDs' => $this->nasaService->getLinkedEvents($dates['startDate'], $dates['endDate'])
        ]);
    }

    public function getInstrumentsUsage(Request $request)
    {
        $dates = $this->getValidatedDates($request);

        return response()->json(
            $this->nasaService->getInstrumentsUsage($dates['startDate'], $dates['endDate'])
        );
    }

    public function getInstrumentActivityUsage(Request $request)
    {
        $validated = $request->validate([
            'instrument' => 'required|string',
            'startDate' => 'nullable|date_format:Y-m-d',
            'endDate' => 'nullable|date_format:Y-m-d|after_or_equal:startDate',
        ]);

        $startDate = $validated['startDate'] ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $validated['endDate'] ?? now()->format('Y-m-d');

        return response()->json(
            $this->nasaService->getInstrumentActivityUsage($validated['instrument'], $startDate, $endDate)
        );
    }
}
