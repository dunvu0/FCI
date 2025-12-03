<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex Results - SQL Lab</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>🔍 POKÉDEX DATA 🔍</h1>
            <p class="warning">⚠ ACTIVE DB: <?= htmlspecialchars(strtoupper($dbType)) ?> ⚠</p>
            <a href="/search?db=<?= htmlspecialchars($dbType) ?>" class="btn">◀ NEW SEARCH</a>
            <a href="/?db=<?= htmlspecialchars($dbType) ?>" class="btn">◀ HOME</a>
        </header>
        
        <?php if (isset($error)): ?>
            <div class="error-box">
                <h3>✕ SYSTEM ERROR</h3>
                <pre><?= htmlspecialchars($error) ?></pre>
            </div>
        <?php endif; ?>
        
        <?php if (isset($displayQuery)): ?>
            <div class="query-display">
                <strong>► EXECUTED QUERY:</strong><br>
                <?= htmlspecialchars($displayQuery) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($results) && is_array($results)): ?>
            <div class="result-box">
                <h3>◆ CAPTURED <?= count($results) ?> ENTRIES ◆</h3>
                
                <?php if (count($results) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <?php 
                                // Display column headers dynamically
                                $firstRow = $results[0];
                                foreach (array_keys($firstRow) as $column): 
                                ?>
                                    <th>◆ <?= htmlspecialchars(strtoupper($column)) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row): ?>
                                <tr>
                                    <?php foreach ($row as $value): ?>
                                        <td><?= htmlspecialchars($value ?? 'NULL') ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>✕ NO POKÉMON DATA FOUND IN POKÉDEX</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </div>
</body>
</html>
