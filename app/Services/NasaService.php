<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NasaService
{
    private string $baseUrl;
    private string $apiKey;
    private const ENDPOINTS = ["CME", "FLR", "SEP", "CIR", "HSS", "WSAEnlilSim", "Notifications"];

    public function __construct()
    {
        $this->baseUrl = "https://api.nasa.gov/DONKI";
        $this->apiKey = env('NASA_API_KEY');
    }

    private function fetchData(string $endpoint, string $startDate, string $endDate): array
    {
        $response = Http::get("{$this->baseUrl}/{$endpoint}", [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'api_key' => $this->apiKey
        ]);

        return $response->successful() ? $response->json() : [];
    }

    private function extractFromResponse(array $data, string $key, ?callable $callback = null): array
    {
        $results = [];

        foreach ($data as $entry) {
            if (isset($entry[$key])) {
                foreach ($entry[$key] as $item) {
                    $value = $callback ? $callback($item) : $item;
                    $results[$value] = true;
                }
            }
        }

        return array_keys($results);
    }

    public function getInstruments(string $startDate, string $endDate): array
    {
        $instruments = [];

        foreach (self::ENDPOINTS as $endpoint) {
            $data = $this->fetchData($endpoint, $startDate, $endDate);
            $instruments = array_merge($instruments, $this->extractFromResponse($data, 'instruments', fn($inst) => $inst['displayName']));
        }

        return array_values(array_unique($instruments));
    }

    public function getLinkedEvents(string $startDate, string $endDate): array
    {
        $activityIDs = [];

        foreach (self::ENDPOINTS as $endpoint) {
            $data = $this->fetchData($endpoint, $startDate, $endDate);
            $activityIDs = array_merge($activityIDs, $this->extractFromResponse($data, 'linkedEvents', fn($event) => preg_replace('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}-/', '', $event['activityID'])));
        }

        return array_values(array_unique($activityIDs));
    }

    public function getInstrumentsUsage(string $startDate, string $endDate): array
    {
        $instrumentsCount = [];
        $totalAppearances = 0;

        foreach (self::ENDPOINTS as $endpoint) {
            $data = $this->fetchData($endpoint, $startDate, $endDate);

            foreach ($data as $entry) {
                if (isset($entry['instruments'])) {
                    foreach ($entry['instruments'] as $instrument) {
                        $displayName = $instrument['displayName'];
                        $instrumentsCount[$displayName] = ($instrumentsCount[$displayName] ?? 0) + 1;
                        $totalAppearances++;
                    }
                }
            }
        }

        if ($totalAppearances === 0) {
            return ['instruments_use' => []];
        }

        $instrumentsUsage = [];
        foreach ($instrumentsCount as $instrument => $count) {
            $instrumentsUsage[$instrument] = round($count / $totalAppearances, 4);
        }

        return ['instruments_use' => $instrumentsUsage];
    }

    public function getInstrumentActivityUsage(string $instrument, string $startDate, string $endDate): array
    {
        $activityCount = [];
        $totalUses = 0;

        foreach (self::ENDPOINTS as $endpoint) {
            $data = $this->fetchData($endpoint, $startDate, $endDate);

            foreach ($data as $entry) {
                if (isset($entry['instruments'])) {
                    $instrumentUsed = collect($entry['instruments'])->pluck('displayName')->contains($instrument);

                    if ($instrumentUsed && isset($entry['activityID'])) {
                        $activityID = preg_replace('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}-/', '', $entry['activityID']);
                        $activityCount[$activityID] = ($activityCount[$activityID] ?? 0) + 1;
                        $totalUses++;
                    }
                }
            }
        }

        if ($totalUses === 0) {
            return ['instrument_activity' => [$instrument => []]];
        }

        $activityUsage = [];
        foreach ($activityCount as $activity => $count) {
            $activityUsage[$activity] = round($count / $totalUses, 4);
        }

        return ['instrument_activity' => [$instrument => $activityUsage]];
    }
}
