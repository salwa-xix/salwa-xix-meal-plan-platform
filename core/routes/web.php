<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('app', [
        'mealPlan' => null,
        'error' => null,
    ]);
});


Route::get('/generate-meal', function () {
    $apiKey = env('GEMINI_API_KEY');

    if (!$apiKey) {
        return view('home', [
            'mealPlan' => null,
            'error' => 'GEMINI_API_KEY is missing in .env',
        ]);
    }

    $response = Http::post(
        "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
        [
            "contents" => [
                [
                    "parts" => [
                        [
                            "text" => 'Give me a simple healthy one-day meal plan.
Return ONLY valid JSON in this exact format:
[
  {"meal":"Breakfast","food":"...","details":"..."},
  {"meal":"Snack","food":"...","details":"..."},
  {"meal":"Lunch","food":"...","details":"..."},
  {"meal":"Dinner","food":"...","details":"..."}
]
Do not add markdown. Do not add explanation.'
                        ]
                    ]
                ]
            ]
        ]
    );

    $data = $response->json();

    if (!$response->successful()) {
        $message = $data['error']['message'] ?? 'Something went wrong while contacting Gemini.';

        return view('app', [
            'mealPlan' => null,
            'error' => $message,
        ]);
    }

    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

    $text = trim($text);
    $text = preg_replace('/^```json\s*/', '', $text);
    $text = preg_replace('/^```\s*/', '', $text);
    $text = preg_replace('/\s*```$/', '', $text);

    $mealPlan = json_decode($text, true);

    if (!is_array($mealPlan)) {
        return view('home', [
            'mealPlan' => null,
            'error' => 'Gemini returned data, but it was not in the expected table format.',
        ]);
    }

    return view('app', [
        'mealPlan' => $mealPlan,
        'error' => null,
    ]);
});