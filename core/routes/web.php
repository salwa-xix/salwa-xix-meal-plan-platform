<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('app', [
        'mealPlan' => null,
        'error' => null,
    ]);
});

Route::get('/generate-meal', function (Request $request) {
    $apiKey = env('GEMINI_API_KEY');

    if (!$apiKey) {
        return view('app', [
            'mealPlan' => null,
            'error' => 'GEMINI_API_KEY is missing in .env',
        ]);
    }

    $name = $request->input('name', 'patient');
    $age = $request->input('age', 'not specified');
    $calories = $request->input('calories', 'not specified');
    $dietType = $request->input('diet_type', 'balanced');
    $mealsPerDay = $request->input('meals_per_day', 4);
    $excludedFoods = $request->input('excluded_foods', 'none');
    $allergies = $request->input('allergies', 'none');

    $prompt = "Create a healthy one-day meal plan for {$name}.
Age: {$age}
Calories target: {$calories}
Diet type: {$dietType}
Meals per day: {$mealsPerDay}
Excluded foods: {$excludedFoods}
Allergies: {$allergies}

Return ONLY valid JSON in this exact format:
[
  {\"meal\":\"Breakfast\",\"food\":\"...\",\"details\":\"...\"},
  {\"meal\":\"Snack\",\"food\":\"...\",\"details\":\"...\"},
  {\"meal\":\"Lunch\",\"food\":\"...\",\"details\":\"...\"},
  {\"meal\":\"Dinner\",\"food\":\"...\",\"details\":\"...\"}
]
Do not add markdown. Do not add explanation.";

    $response = Http::post(
        "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
        [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
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
        return view('app', [
            'mealPlan' => null,
            'error' => 'Gemini returned data, but it was not in the expected table format.',
        ]);
    }

    return view('app', [
        'mealPlan' => $mealPlan,
        'error' => null,
    ]);
});