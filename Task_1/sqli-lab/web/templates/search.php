<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GYM 2: Pokédex Search - SQL Lab</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>🔍 GYM 2: POKÉDEX SEARCH 🔍</h1>
            <p class="warning">⚠ ACTIVE DB: <?= htmlspecialchars(strtoupper($dbType)) ?> ⚠</p>
            <a href="/?db=<?= htmlspecialchars($dbType) ?>" class="btn">◀ RETURN HOME</a>
        </header>
        
        <form method="GET" action="/search">
            <input type="hidden" name="db" value="<?= htmlspecialchars($dbType) ?>">
            
            <div class="form-group">
                <label for="q">► SEARCH POKÉDEX:</label>
                <input type="text" id="q" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Enter Pokémon name or type...">
            </div>
            
            <button type="submit" class="btn">▶ SEARCH</button>
        </form>
        
        <div class="hint">
            <h3>═══ PROFESSOR'S NOTES: ERROR & UNION ATTACKS ═══</h3>
            

    </div>
</body>
</html>
