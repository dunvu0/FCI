<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GYM 1: Blind Injection - Pokédex Lab</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>🔐 GYM 1: BLIND INJECTION 🔐</h1>
            <p class="warning">⚠ ACTIVE DB: <?= htmlspecialchars(strtoupper($dbType)) ?> ⚠</p>
            <a href="/?db=<?= htmlspecialchars($dbType) ?>" class="btn">◀ RETURN HOME</a>
        </header>
        
        <?php if (isset($success)): ?>
            <div class="success-box">
                <h3>◆ TRAINER CAPTURED! ◆</h3>
                <p><?= htmlspecialchars($success) ?></p>
                <?php if (isset($user)): ?>
                    <h4>═══ TRAINER DATA ═══</h4>
                    <table>
                        <tr>
                            <th>◆ FIELD</th>
                            <th>◆ VALUE</th>
                        </tr>
                        <?php foreach ($user as $key => $value): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($key) ?></strong></td>
                                <td><?= htmlspecialchars($value instanceof DateTime ? $value->format('Y-m-d H:i:s') : $value) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
                <a href="/logout?db=<?= htmlspecialchars($dbType) ?>" class="btn">▶ LOGOUT</a>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error-box">
                <h3>✕ ATTACK FAILED</h3>
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($displayQuery)): ?>
            <div class="query-display">
                <strong>► EXECUTED QUERY:</strong><br>
                <?= htmlspecialchars($displayQuery) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="/login?db=<?= htmlspecialchars($dbType) ?>">
            <h2>═══ TRAINER LOGIN ═══</h2>
            
            <div class="form-group">
                <label for="username">► TRAINER ID:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">► PASSWORD:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">▶ LOGIN</button>
        </form>
        
        <div class="hint">
            <h3>═══ PROFESSOR'S NOTES: BLIND INJECTION ═══</h3>
            
            <h4>◆ TEST TRAINERS:</h4>
            <ul>
                <li><strong>TRAINER:</strong> admin | <strong>PASS:</strong> admin123</li>
                <li><strong>TRAINER:</strong> john_doe | <strong>PASS:</strong> password123</li>
            </ul>
        </div>

    </div>
</body>
</html>
