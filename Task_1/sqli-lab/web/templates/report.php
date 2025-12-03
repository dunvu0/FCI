<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GYM 3: Elite Four - SQL Lab</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>🏆 GYM 3: ELITE FOUR CHALLENGE 🏆</h1>
            <p class="warning">⚠ ACTIVE DB: <?= htmlspecialchars(strtoupper($dbType)) ?> | STACKED QUERIES & RCE ⚠</p>
            <a href="/?db=<?= htmlspecialchars($dbType) ?>" class="btn">◀ RETURN HOME</a>
        </header>
        
        <?php if (isset($success)): ?>
            <div class="success-box">
                <h3>◆ MISSION COMPLETE! ◆</h3>
                <p><?= htmlspecialchars($success) ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error-box">
                <h3>✕ ATTACK FAILED</h3>
                <pre><?= htmlspecialchars($error) ?></pre>
            </div>
        <?php endif; ?>
        
        <?php if (isset($displayQuery)): ?>
            <div class="query-display">
                <strong>► EXECUTED QUERY:</strong><br>
                <?= htmlspecialchars($displayQuery) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="/report?db=<?= htmlspecialchars($dbType) ?>">
            <h2>═══ SUBMIT BATTLE REPORT ═══</h2>
            
            <div class="form-group">
                <label for="title">► REPORT TITLE:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="content">► REPORT CONTENT:</label>
                <textarea id="content" name="content" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="btn">▶ SUBMIT</button>
        </form>
        
        <div class="hint">
            <h3>═══ PROFESSOR'S NOTES: STACKED QUERIES ═══</h3>
            

    </div>
</body>
</html>
