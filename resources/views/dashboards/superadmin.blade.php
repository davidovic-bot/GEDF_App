<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GDF - Tableau de bord Super Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== STYLES (identique à avant) ===== */
        :root {
            --primary-dark: #0f172a;
            --primary: #1e293b;
            --secondary: #334155;
            --accent: #475569;
            --success: #059669;
            --warning: #d97706;
            --danger: #dc2626;
            --info: #2563eb;
            --light: #f1f5f9;
            --white: #ffffff;
            --border: #cbd5e1;
            --text-dark: #0f172a;
            --text: #475569;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', 'Inter', system-ui, sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: var(--primary-dark);
            color: var(--white);
            padding: 0 2rem;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-bottom: 1px solid var(--secondary);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--success) 0%, var(--info) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--white);
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        .user-menu-container {
            position: relative;
        }

        .user-button {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: var(--white);
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-button:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .user-role {
            color: #60a5fa;
            font-weight: 600;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        /* ===== SIDEBAR (NOUVEAU AVEC LIENS FONCTIONNELS) ===== */
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            width: 260px;
            height: calc(100vh - 70px);
            background: var(--primary);
            border-right: 1px solid var(--secondary);
            padding: 1.5rem 0;
            overflow-y: auto;
            z-index: 999;
            transition: all 0.3s ease;
        }

        .nav-section {
            padding: 0 1.2rem;
            margin-bottom: 2rem;
        }

        .nav-title {
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            font-weight: 600;
            padding-left: 0.5rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.9rem 1rem;
            color: #e2e8f0;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.4rem;
            transition: all 0.2s ease;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--white);
            border-left-color: var(--info);
        }

        .nav-item.active {
            background: rgba(37, 99, 235, 0.15);
            color: var(--white);
            border-left-color: var(--info);
            font-weight: 600;
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .main-content {
            margin-top: 70px;
            margin-left: 260px;
            padding: 2rem;
            min-height: calc(100vh - 70px);
            overflow: visible !important;
        }

        .dashboard-title {
            margin-bottom: 2.5rem;
        }

        .dashboard-title h1 {
            color: var(--primary-dark);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .dashboard-title p {
            color: var(--text);
            font-size: 1rem;
            max-width: 800px;
            line-height: 1.6;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 12px;
            padding: 1.8rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            border-top: 4px solid var(--primary);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.2rem;
        }

        .stat-title {
            color: var(--text);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--light) 0%, #e2e8f0 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.4rem;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .stat-subvalue {
            font-size: 1rem;
            color: var(--text);
            margin-bottom: 1rem;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .trend-up {
            color: var(--success);
        }

        .trend-down {
            color: var(--danger);
        }

        .trend-neutral {
            color: var(--accent);
        }

        .footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            color: var(--text);
            font-size: 0.9rem;
            text-align: center;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        @media (max-width: 992px) {
            .menu-toggle {
                display: block;
            }
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1.5rem;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- ===== HEADER ===== -->
    <header class="main-header">
        <div class="logo-container">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="logo">GDF</div>
        </div>

        <!-- ===== BOUTON SUPER ADMIN ===== -->
        <div class="user-menu-container">
            <button class="user-button" id="userMenuButton">
                <div class="user-avatar">SA</div>
                <span class="user-role">Super Admin</span>
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    </header>

    <!-- ===== SIDEBAR (AVEC LIENS FONCTIONNELS) ===== -->
    <nav class="sidebar" id="sidebar">
        <!-- SECTION 1 : NAVIGATION PRINCIPALE -->
        <div class="nav-section">
            <div class="nav-title">Navigation</div>
            <a href="{{ route('dashboard.superadmin') }}" class="nav-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="{{ route('parapheurs.index') }}" class="nav-item">
                <i class="fas fa-file-signature"></i>
                <span>Parapheurs</span>
            </a>
            <a href="{{ route('statistiques.index') }}" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Statistiques</span>
            </a>
        </div>

        <!-- SECTION 2 : ADMINISTRATION -->
        <div class="nav-section">
            <div class="nav-title">Administration</div>
            <a href="{{ route('admin.utilisateurs') }}" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Utilisateurs</span>
            </a>
            <a href="{{ route('admin.roles') }}" class="nav-item">
                <i class="fas fa-user-tag"></i>
                <span>Rôles & Permissions</span>
            </a>
            <a href="{{ route('admin.parametres') }}" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Paramètres système</span>
            </a>
            <a href="{{ route('admin.audit') }}" class="nav-item">
                <i class="fas fa-history"></i>
                <span>Journal d'audit</span>
            </a>
        </div>

        <!-- SECTION 3 : ACTIONS RAPIDES -->
        <div class="nav-section">
            <div class="nav-title">Actions rapides</div>
            <a href="{{ route('parapheurs.create') }}" class="nav-item">
                <i class="fas fa-plus-circle"></i>
                <span>Nouveau parapheur</span>
            </a>
            <a href="/rapports/export" class="nav-item">
                <i class="fas fa-download"></i>
                <span>Exporter les données</span>
            </a>
        </div>
    </nav>

    <!-- ===== CONTENU PRINCIPAL (DASHBOARD) ===== -->
    <main class="main-content">
        <div class="dashboard-title">
            <h1>Tableau de bord Super Admin</h1>
            <p>Supervision complète du système de gestion des parapheurs DRS</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Parapheurs en attente</div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value">18</div>
                <div class="stat-subvalue">dont 3 en retard</div>
                <div class="stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i>
                    <span>+2 depuis hier</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Parapheurs validés</div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value">47</div>
                <div class="stat-subvalue">ce mois</div>
                <div class="stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i>
                    <span>+8% vs mois dernier</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Utilisateurs actifs</div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-value">24</div>
                <div class="stat-subvalue">sur 6 rôles</div>
                <div class="stat-trend trend-neutral">
                    <i class="fas fa-minus"></i>
                    <span>stable cette semaine</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Délai moyen</div>
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
                <div class="stat-value">2,8</div>
                <div class="stat-subvalue">jours</div>
                <div class="stat-trend trend-down">
                    <i class="fas fa-arrow-down"></i>
                    <span>-0,3 jour</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Système de Gestion des Parapheurs DRS © 2025</p>
            <div style="display: flex; justify-content: center; gap: 1.5rem; margin-top: 0.8rem; color: var(--accent); font-size: 0.85rem;">
                <span>Version 2.1.4</span>
                <span>•</span>
                <span>11 Décembre 2025</span>
            </div>
        </div>
    </main>

    <script>
        // Menu mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Navigation active
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                
                if (window.innerWidth <= 992) {
                    document.getElementById('sidebar').classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>