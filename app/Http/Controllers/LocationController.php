<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * Get list of countries.
     */
   public function getCountries()
{
    try {

        $response = Http::timeout(20)
            ->get('https://countriesnow.space/api/v0.1/countries/iso');


        Log::info("COUNTRIES STATUS: ".$response->status());
        Log::info("COUNTRIES BODY: ".$response->body());


        if ($response->successful()) {

            $json = $response->json();


            if(isset($json['data']) && is_array($json['data'])) {


                $countries = collect($json['data'])
                    ->map(function($country){

                        return [
                            'name'=>$country['name'],
                            'Iso2'=>strtoupper($country['Iso2']),
                            'Iso3'=>strtoupper($country['Iso3']),
                        ];

                    })
                    ->values()
                    ->toArray();


                return response()->json([
                    'success'=>true,
                    'data'=>$countries
                ]);
            }

        }


        throw new \Exception("Invalid API response");


    } catch(\Exception $e){


        Log::error("COUNTRIES ERROR: ".$e->getMessage());


        return response()->json([
            'success'=>false,
            'data'=>[],
            'error'=>'Could not load country data'
        ]);

    }
}
    /**
     * Get states/provinces for a given country code (ISO2).
     */
 public function getStates(string $country)
{
    $country = strtoupper(trim($country));

    Log::info("Getting states for: " . $country);

    try {

        $response = Http::timeout(15)
            ->asJson()
            ->post(
                'https://countriesnow.space/api/v0.1/countries/states',
                [
                    'iso2' => $country
                ]
            );


        Log::info("STATE STATUS: " . $response->status());
        Log::info("STATE BODY: " . $response->body());


        if ($response->successful()) {

            $json = $response->json();

            if (
                isset($json['data']['states']) &&
                is_array($json['data']['states'])
            ) {

                return response()->json([
                    'success' => true,
                    'data' => $json['data']['states']
                ]);

            }

        }


        return response()->json([
            'success'=>false,
            'data'=>[],
            'message'=>'No states found'
        ]);


    } catch(\Exception $e){

        Log::error("STATE ERROR: ".$e->getMessage());

        return response()->json([
            'success'=>false,
            'data'=>[],
            'error'=>'Could not load state data'
        ]);

    }
}
}
