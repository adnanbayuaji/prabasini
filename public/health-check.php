<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Health Check - Prabasini</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin-top: 30px;
        }
        .check-item {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .check-pass {
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .check-fail {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .check-warn {
            background: #fff3cd;
            border: 1px solid #ffeeba;
        }
        .badge-pass {
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-fail {
            background: #dc3545;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-warn {
            background: #ffc107;
            color: black;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        .info-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">🔍 System Health Check</h1>
        
        <div class="info-box">
            <strong>Halaman ini mengecek konfigurasi sistem Prabasini PAP Management System</strong>
        </div>
        
        <div id="results"></div>
        
        <div class="mt-4">
            <a href="index.php" class="btn btn-primary">← Kembali ke Dashboard</a>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const resultsDiv = document.getElementById('results');
            const checks = [];
            
            // 1. PHP Version
            const phpVersion = <?php echo json_encode(phpversion()); ?>;
            checks.push({
                name: 'PHP Version',
                status: phpVersion >= '7.4' ? 'pass' : 'fail',
                message: `PHP ${phpVersion}`,
                details: phpVersion >= '7.4' ? 'Requirements met (≥7.4)' : 'Please upgrade to PHP 7.4+'
            });
            
            // 2. Database
            const dbCheck = <?php
                ob_start();
                require_once '../config/database.php';
                $dbStatus = true;
                $dbMessage = 'Connected';
                try {
                    $pdo->query('SELECT 1');
                } catch (Exception $e) {
                    $dbStatus = false;
                    $dbMessage = $e->getMessage();
                }
                ob_end_clean();
                echo json_encode(['status' => $dbStatus, 'message' => $dbMessage]);
            ?>;
            checks.push({
                name: 'Database Connection',
                status: dbCheck.status ? 'pass' : 'fail',
                message: 'MySQL/MariaDB',
                details: dbCheck.message
            });
            
            // 3. File Permissions
            const uploadDir = '../public/uploads/';
            const isWritable = <?php echo is_writable('../public/uploads/') ? 'true' : 'false'; ?>;
            checks.push({
                name: 'Upload Directory Permission',
                status: isWritable ? 'pass' : 'warn',
                message: 'public/uploads/',
                details: isWritable ? 'Writable ✓' : 'Not writable - May cause upload issues'
            });
            
            // 4. Required Extensions
            const extensions = [
                { name: 'PDO', available: <?php echo extension_loaded('pdo') ? 'true' : 'false'; ?> },
                { name: 'PDO MySQL', available: <?php echo extension_loaded('pdo_mysql') ? 'true' : 'false'; ?> },
                { name: 'ZipArchive', available: <?php echo extension_loaded('zip') ? 'true' : 'false'; ?> },
                { name: 'DOM', available: <?php echo extension_loaded('dom') ? 'true' : 'false'; ?> },
                { name: 'SimpleXML', available: <?php echo extension_loaded('simplexml') ? 'true' : 'false'; ?> }
            ];
            
            extensions.forEach(ext => {
                checks.push({
                    name: `PHP Extension: ${ext.name}`,
                    status: ext.available ? 'pass' : 'warn',
                    message: ext.available ? 'Installed' : 'Not found',
                    details: ext.available ? 'Available' : 'May cause issues with Excel parsing'
                });
            });
            
            // 5. PAP Table
            const tableExists = <?php
                try {
                    $result = $pdo->query("SELECT 1 FROM pap LIMIT 1");
                    echo 'true';
                } catch (Exception $e) {
                    echo 'false';
                }
            ?>;
            checks.push({
                name: 'PAP Database Table',
                status: tableExists ? 'pass' : 'warn',
                message: 'pap table',
                details: tableExists ? 'Table exists' : 'Table will be created on first access'
            });
            
            // Render results
            let html = '';
            checks.forEach(check => {
                const statusClass = check.status === 'pass' ? 'check-pass' : check.status === 'fail' ? 'check-fail' : 'check-warn';
                const badgeClass = check.status === 'pass' ? 'badge-pass' : check.status === 'fail' ? 'badge-fail' : 'badge-warn';
                const statusText = check.status === 'pass' ? '✓ OK' : check.status === 'fail' ? '✗ FAIL' : '⚠ WARNING';
                
                html += `
                    <div class="check-item ${statusClass}">
                        <div>
                            <strong>${check.name}</strong><br>
                            <small class="text-muted">${check.details}</small>
                        </div>
                        <div class="${badgeClass}">${statusText}</div>
                    </div>
                `;
            });
            
            resultsDiv.innerHTML = html;
        });
    </script>
</body>
</html>
