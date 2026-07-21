<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prabasini - Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: auto;
            padding: 40px 20px;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 50px;
        }
        
        .header h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .header p {
            font-size: 18px;
            opacity: 0.95;
        }
        
        .card-module {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
        }
        
        .card-module:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        
        .card-icon {
            font-size: 48px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .card-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .card-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .card-button:hover {
            transform: scale(1.05);
            text-decoration: none;
            color: white;
        }
        
        .features {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-top: 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .feature-item {
            display: flex;
            margin-bottom: 15px;
            align-items: center;
        }
        
        .feature-icon {
            font-size: 24px;
            color: #667eea;
            margin-right: 15px;
            width: 30px;
        }
        
        .feature-text {
            color: #333;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1><i class="fas fa-box"></i> Prabasini</h1>
            <p>Sistem Manajemen Program Alokasi Produk</p>
        </div>
        
        <div class="row g-4">
            <!-- PAP Management Card -->
            <div class="col-md-6">
                <div class="card-module">
                    <div class="card-icon">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <div class="card-title">Daftar PAP</div>
                    <div class="card-description">
                        Kelola data Program Alokasi Produk. Import dari Excel, lihat status, dan kirim via WhatsApp.
                    </div>
                    <a href="pap.php" class="card-button">
                        <i class="fas fa-arrow-right"></i> Masuk
                    </a>
                </div>
            </div>
            
            <!-- Settings Card (Future) -->
            <div class="col-md-6">
                <div class="card-module" style="opacity: 0.7; pointer-events: none;">
                    <div class="card-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="card-title">Pengaturan</div>
                    <div class="card-description">
                        Konfigurasi sistem, kelola pengguna, dan atur integrasi.
                    </div>
                    <button class="card-button" disabled>
                        <i class="fas fa-lock"></i> Segera Hadir
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Features Section -->
        <div class="features">
            <h3 style="margin-bottom: 25px; color: #333;">
                <i class="fas fa-star" style="color: #667eea; margin-right: 10px;"></i>
                Fitur Utama
            </h3>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-file-import"></i></div>
                        <div class="feature-text"><strong>Import Excel</strong> - Upload file .xls, .xlsx, atau .csv</div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fab fa-whatsapp"></i></div>
                        <div class="feature-text"><strong>Integrasi WhatsApp</strong> - Kirim pesan langsung via wa.me</div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-database"></i></div>
                        <div class="feature-text"><strong>Database Terpusat</strong> - Semua data tersimpan dengan aman</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="feature-text"><strong>Status Tracking</strong> - Pantau status pengiriman real-time</div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-mobile-alt"></i></div>
                        <div class="feature-text"><strong>Responsive Design</strong> - Aksesibel di desktop dan mobile</div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="feature-text"><strong>Aman & Stabil</strong> - Dibangun dengan teknologi terkini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
