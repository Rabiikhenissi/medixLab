<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * Get list of countries.
     *
     * @return JsonResponse
     */
    public function getCountries()
    {
        try {
            // fetch the country ISO codes from the external API
            $response = Http::timeout(20)
                ->get('https://countriesnow.space/api/v0.1/countries/iso');

            Log::info('COUNTRIES STATUS: '.$response->status());
            Log::info('COUNTRIES BODY: '.$response->body());

            if ($response->successful()) {

                $json = $response->json();

                if (isset($json['data']) && is_array($json['data'])) {
                    // normalize the payload to a flat list of countries
                    $countries = collect($json['data'])
                        ->map(function ($country) {

                            return [
                                'name' => $country['name'],
                                'Iso2' => strtoupper($country['Iso2']),
                                'Iso3' => strtoupper($country['Iso3']),
                            ];

                        })
                        ->values()
                        ->toArray();

                    return response()->json([
                        'success' => true,
                        'data' => $countries,
                    ]);
                }

            }

            throw new \Exception('Invalid API response');
        } catch (\Exception $e) {

            Log::error('COUNTRIES ERROR: '.$e->getMessage());

            // return an empty payload on failure
            return response()->json([
                'success' => false,
                'data' => [],
                'error' => 'Could not load country data',
            ]);

        }
    }

    /**
     * Get states/provinces for a given country code (ISO2).
     *
     * @return JsonResponse
     */
    public function getStates(string $country)
    {
        $country = strtoupper(trim($country));

        Log::info('Getting states for: '.$country);

        try {
            // fetch the states of the given country from the external API
            $response = Http::timeout(15)
                ->asJson()
                ->post(
                    'https://countriesnow.space/api/v0.1/countries/states',
                    [
                        'iso2' => $country,
                    ]
                );

            Log::info('STATE STATUS: '.$response->status());
            Log::info('STATE BODY: '.$response->body());

            if ($response->successful()) {

                $json = $response->json();

                if (
                    isset($json['data']['states']) &&
                    is_array($json['data']['states'])
                ) {

                    return response()->json([
                        'success' => true,
                        'data' => $json['data']['states'],
                    ]);

                }

            }

            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'No states found',
            ]);

        } catch (\Exception $e) {

            Log::error('STATE ERROR: '.$e->getMessage());

            // return an empty payload on failure
            return response()->json([
                'success' => false,
                'data' => [],
                'error' => 'Could not load state data',
            ]);

        }
    }
}
