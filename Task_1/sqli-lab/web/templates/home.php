<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Lab</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>⚡ SQLI POKÉDEX LAB ⚡</h1>
            <p class="warning">⚠ WARNING: INTENTIONALLY VULNERABLE - EDUCATION ONLY! ⚠</p>
        </header>
        
        <div class="db-info">
            <h2>═══ POKÉDEX DATABASE STATUS ═══</h2>
            <p><strong>► TYPE:</strong> <?= htmlspecialchars(strtoupper($dbInfo['type'])) ?></p>
            <p><strong>► VERSION:</strong> <?= htmlspecialchars($dbInfo['version']) ?></p>
            <p><strong>► STATUS:</strong> <span class="<?= $dbInfo['connected'] ? 'connected' : 'disconnected' ?>">
                <?= $dbInfo['connected'] ? '◆ ONLINE' : '✕ OFFLINE' ?>
            </span></p>
        </div>
        
        <div class="db-selector">
            <h3>═══ SELECT YOUR POKÉBALL ═══</h3>
            <div class="db-buttons">
                <a href="?db=mysql" class="btn <?= $_GET['db'] === 'mysql' || !isset($_GET['db']) ? 'active' : '' ?>">◉ MYSQL</a>
                <a href="?db=sqlite" class="btn <?= $_GET['db'] === 'sqlite' ? 'active' : '' ?>">◉ SQLITE</a>
                <a href="?db=pgsql" class="btn <?= $_GET['db'] === 'pgsql' ? 'active' : '' ?>">◉ PGSQL</a>
                <a href="?db=mssql" class="btn <?= $_GET['db'] === 'mssql' ? 'active' : '' ?>">◉ MSSQL</a>
            </div>
        </div>
        
        <div class="features">
            <h2>═══ TRAINING ZONES ═══</h2>
            
            <div class="feature-card">
                <h3>🔐 GYM 1: BLIND INJECTION</h3>
                <ul>
                    <li>Boolean-based blind SQLi</li>
                    <li>Time-based blind SQLi</li>
                    <li>Authentication bypass</li>
                </ul>
                <a href="/login?db=<?= htmlspecialchars($_GET['db'] ?? 'mysql') ?>" class="btn">▶ ENTER GYM</a>
            </div>
            
            <div class="feature-card">
                <h3>🔍 GYM 2: ERROR & UNION ATTACKS</h3>
                <ul>
                    <li>Error-based SQLi</li>
                    <li>UNION-based SQLi</li>
                    <li>Information schema enumeration</li>
                </ul>
                <a href="/search?db=<?= htmlspecialchars($_GET['db'] ?? 'mysql') ?>" class="btn">▶ ENTER GYM</a>
            </div>
            
            <div class="feature-card">
                <h3>📊 GYM 3: ADVANCED</h3>
                <ul>
                    <li>Stacked queries</li>
                    <li>Out-of-band exfiltration</li>
                    <li>SQL injection to RCE</li>
                </ul>
                <a href="/report?db=<?= htmlspecialchars($_GET['db'] ?? 'mysql') ?>" class="btn">▶ ENTER GYM</a>
            </div>
        </div>
        
        <footer>
            <p>◆ PROFESSOR OAK'S SQL LAB ◆ FROM FCI WITH LUV:3 ◆</p>
        </footer>
    </div>
</body>
</html>
