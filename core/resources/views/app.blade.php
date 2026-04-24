<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Plan Platform</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 40px; background: #f7f9fc;">

    <h1 style="font-size: 40px; margin-bottom: 10px;">Meal Plan Platform </h1>

    <p style="font-size: 20px; margin-bottom: 20px;">
        Welcome to your smart nutrition assistant.
    </p>

   <form method="GET" action="/generate-meal" style="margin-bottom: 30px; background:white; padding:20px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">

    <label>Patient Name</label><br>
    <input type="text" name="name" placeholder="Enter patient name" style="width:100%; padding:10px; margin:8px 0 15px;"><br>

    <label>Age</label><br>
    <input type="number" name="age" placeholder="Enter age" style="width:100%; padding:10px; margin:8px 0 15px;"><br>

    <label>Calories</label><br>
    <input type="number" name="calories" placeholder="Example: 1800" style="width:100%; padding:10px; margin:8px 0 15px;"><br>

    <label>Diet Type</label><br>
    <select name="diet_type" style="width:100%; padding:10px; margin:8px 0 15px;">
        <option value="">Select diet type</option>
        <option value="balanced">Balanced</option>
        <option value="high protein">High Protein</option>
        <option value="low carb">Low Carb</option>
        <option value="vegetarian">Vegetarian</option>
        <option value="keto">Keto</option>
    </select><br>

    <label>Meals Per Day</label><br>
    <input type="number" name="meals_per_day" placeholder="Example: 4" style="width:100%; padding:10px; margin:8px 0 15px;"><br>

    <label>Excluded Foods</label><br>
    <input type="text" name="excluded_foods" placeholder="Example: fish, mushrooms" style="width:100%; padding:10px; margin:8px 0 15px;"><br>

    <label>Allergies</label><br>
    <input type="text" name="allergies" placeholder="Example: nuts, milk" style="width:100%; padding:10px; margin:8px 0 15px;"><br>

    <button
        type="submit"
        style="
            padding: 12px 20px;
            background: #2d6cdf;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        "
    >
        Generate Meal Plan
    </button>
</form>

    @if($error)
        <p style="color: red; font-size: 18px; margin-bottom: 20px;">
            {{ $error }}
        </p>
    @endif

    @if($mealPlan)
        <table
            border="1"
            cellpadding="12"
            cellspacing="0"
            style="
                width: 100%;
                border-collapse: collapse;
                background: white;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            "
        >
            <thead style="background: #2861cd; color: white;">
                <tr>
                    <th>Meal</th>
                    <th>Food</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mealPlan as $item)
                    <tr>
                        <td>{{ $item['meal'] ?? '-' }}</td>
                        <td>{{ $item['food'] ?? '-' }}</td>
                        <td>{{ $item['details'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>