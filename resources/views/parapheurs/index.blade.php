<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GDF - Module Parapheurs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== PALETTE DE COULEURS ===== */
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

        /* ===== RESET & BASE ===== */
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

        /* ===== HEADER ===== */
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

        /* ===== BOUTON SUPER ADMIN ===== */
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

        /* ===== SIDEBAR ===== */
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

        /* ===== CONTENU PRINCIPAL ===== */
        .main-content {
            margin-top: 70px;
            margin-left: 260px;
            padding: 2rem;
            min-height: calc(100vh - 70px);
            overflow: visible !important;
        }

        /* ===== EN-TÊTE DU MODULE ===== */
        .module-header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--border);
        }

        .module-header h1 {
            color: var(--primary-dark);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .module-header p {
            color: var(--text);
            font-size: 1rem;
            max-width: 800px;
        }

        /* ===== BARRE D'OUTILS ===== */
        .toolbar {
            background: var(--white);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 3rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            background: var(--light);
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
        }

        .btn-primary {
            background: var(--info);
            color: var(--white);
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        /* ===== FILTRES ===== */
        .filters {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.7rem 1.5rem;
            border: 1px solid var(--border);
            background: var(--white);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-btn:hover {
            background: var(--light);
        }

        .filter-btn.active {
            background: var(--info);
            color: var(--white);
            border-color: var(--info);
        }

        .filter-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        /* ===== TABLEAU DES PARAPHEURS ===== */
        .data-table-container {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .table-header {
            padding: 1.5rem;
            background: var(--light);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h3 {
            color: var(--primary-dark);
            font-size: 1.2rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            padding: 1.2rem 1.5rem;
            text-align: left;
            color: var(--primary-dark);
            font-weight: 600;
            border-bottom: 2px solid var(--border);
            background: var(--light);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            font-size: 0.95rem;
        }

        .data-table tr:hover {
            background: var(--light);
        }

        /* ===== BADGES DE STATUT ===== */
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .status-attente {
            background: rgba(217, 119, 6, 0.15);
            color: var(--warning);
        }

        .status-cours {
            background: rgba(37, 99, 235, 0.15);
            color: var(--info);
        }

        .status-valide {
            background: rgba(5, 150, 105, 0.15);
            color: var(--success);
        }

        .status-rejete {
            background: rgba(220, 38, 38, 0.15);
            color: var(--danger);
        }

        .status-retard {
            background: rgba(220, 38, 38, 0.1);
            color: var(--danger);
            border: 1px dashed var(--danger);
        }

        /* ===== BOUTONS D'ACTION ===== */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--white);
            color: var(--text);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            background: var(--light);
            transform: translateY(-2px);
        }

        .btn-view:hover {
            color: var(--info);
            border-color: var(--info);
        }

        .btn-edit:hover {
            color: var(--warning);
            border-color: var(--warning);
        }

        .btn-delete:hover {
            color: var(--danger);
            border-color: var(--danger);
        }

        /* ===== PAGINATION ===== */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .page-btn {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border);
            background: var(--white);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .page-btn:hover {
            background: var(--light);
        }

        .page-btn.active {
            background: var(--info);
            color: var(--white);
            border-color: var(--info);
        }

        /* ===== STATISTIQUES RAPIDES ===== */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            background: var(--white);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-top: 4px solid;
        }

        .stat-box h4 {
            color: var(--text);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .stat-box .value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
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
            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            .search-box {
                min-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1.5rem;
            }
            .data-table {
                display: block;
                overflow-x: auto;
            }
            .filters {
                justify-content: center;
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

    <!-- ===== SIDEBAR ===== -->
    <nav class="sidebar" id="sidebar">
        <!-- SECTION 1 : NAVIGATION PRINCIPALE -->
        <div class="nav-section">
            <div class="nav-title">Navigation</div>
            <a href="/dashboard" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="/parapheurs" class="nav-item active">
                <i class="fas fa-file-signature"></i>
                <span>Parapheurs</span>
            </a>
            <a href="/statistiques" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Statistiques</span>
            </a>
        </div>

        <!-- SECTION 2 : ADMINISTRATION -->
        <div class="nav-section">
            <div class="nav-title">Administration</div>
            <a href="/administration/utilisateurs" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Utilisateurs</span>
            </a>
            <a href="/administration/roles" class="nav-item">
                <i class="fas fa-user-tag"></i>
                <span>Rôles & Permissions</span>
            </a>
            <a href="/administration/parametres" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Paramètres système</span>
            </a>
            <a href="/administration/audit" class="nav-item">
                <i class="fas fa-history"></i>
                <span>Journal d'audit</span>
            </a>
        </div>

        <!-- SECTION 3 : ACTIONS RAPIDES -->
        <div class="nav-section">
            <div class="nav-title">Actions rapides</div>
            <a href="/parapheurs/create" class="nav-item">
                <i class="fas fa-plus-circle"></i>
                <span>Nouveau parapheur</span>
            </a>
            <a href="/statistiques/export" class="nav-item">
                <i class="fas fa-download"></i>
                <span>Exporter les données</span>
            </a>
        </div>
    </nav>

    <!-- ===== CONTENU PRINCIPAL ===== -->
    <main class="main-content">
        <!-- En-tête du module -->
        <div class="module-header">
            <h1><i class="fas fa-file-signature"></i> Module Parapheurs</h1>
            <p>Gestion complète des courriers et documents administratifs - Suivi, validation et archivage</p>
        </div>

        <!-- Statistiques rapides -->
        <div class="quick-stats">
            <div class="stat-box" style="border-top-color: var(--warning);">
                <h4>En attente</h4>
                <div class="value">18</div>
                <div style="font-size: 0.8rem; color: var(--text);">dont 3 en retard</div>
            </div>
            <div class="stat-box" style="border-top-color: var(--info);">
                <h4>En cours</h4>
                <div class="value">12</div>
                <div style="font-size: 0.8rem; color: var(--text);">en traitement</div>
            </div>
            <div class="stat-box" style="border-top-color: var(--success);">
                <h4>Validés ce mois</h4>
                <div class="value">47</div>
                <div style="font-size: 0.8rem; color: var(--text);">+8% vs mois dernier</div>
            </div>
            <div class="stat-box" style="border-top-color: var(--danger);">
                <h4>Rejetés</h4>
                <div class="value">5</div>
                <div style="font-size: 0.8rem; color: var(--text);">nécessitent correction</div>
            </div>
        </div>

        <!-- Barre d'outils -->
        <div class="toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Rechercher un parapheur par référence, service ou objet...">
            </div>
            <button class="btn-primary" onclick="window.location.href='/parapheurs/create'">
                <i class="fas fa-plus"></i>
                Nouveau parapheur
            </button>
        </div>

        <!-- Filtres -->
        <div class="filters">
            <button class="filter-btn active">
                <i class="fas fa-clock"></i>
                En attente
                <span class="filter-badge">18</span>
            </button>
            <button class="filter-btn">
                <i class="fas fa-spinner"></i>
                En cours
                <span class="filter-badge">12</span>
            </button>
            <button class="filter-btn">
                <i class="fas fa-check-circle"></i>
                Validés
                <span class="filter-badge">47</span>
            </button>
            <button class="filter-btn">
                <i class="fas fa-times-circle"></i>
                Rejetés
                <span class="filter-badge">5</span>
            </button>
            <button class="filter-btn">
                <i class="fas fa-exclamation-triangle"></i>
                En retard
                <span class="filter-badge">3</span>
            </button>
            <button class="filter-btn">
                <i class="fas fa-filter"></i>
                Plus de filtres
            </button>
        </div>

        <!-- Tableau des parapheurs -->
        <div class="data-table-container">
            <div class="table-header">
                <h3>Liste des parapheurs</h3>
                <div style="font-size: 0.9rem; color: var(--accent);">
                    Affichage 1-10 sur 82 parapheurs
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Objet</th>
                        <th>Service</th>
                        <th>Créé le</th>
                        <th>Étape</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Ligne 1 -->
                    <tr>
                        <td><strong>#DRS-2025-0421</strong></td>
                        <td>Demande d'achat matériel informatique</td>
                        <td>Service Finances</td>
                        <td>11/12/2025</td>
                        <td>Directeur</td>
                        <td>
                            <span class="status-badge status-attente">
                                <i class="fas fa-clock"></i> En attente
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action btn-view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action btn-edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action btn-delete" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Ligne 2 -->
                    <tr>
                        <td><strong>#DRS-2025-0420</strong></td>
                        <td>Rapport d'activité trimestriel</td>
                        <td>Service Budget</td>
                        <td>10/12/2025</td>
                        <td>Chef de Service</td>
                        <td>
                            <span class="status-badge status-cours">
                                <i class="fas fa-spinner"></i> En cours
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action btn-view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action btn-edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Ligne 3 -->
                    <tr>
                        <td><strong>#DRS-2025-0419</strong></td>
                        <td>Demande de mission à l'étranger</td>
                        <td>Service Fiscal</td>
                        <td>09/12/2025</td>
                        <td>Gestionnaire</td>
                        <td>
                            <span class="status-badge status-retard">
                                <i class="fas fa-exclamation-triangle"></i> En retard
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action btn-view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action btn-edit" title="Relancer">
                                    <i class="fas fa-bell"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Ligne 4 -->
                    <tr>
                        <td><strong>#DRS-2025-0418</strong></td>
                        <td>Contrat de prestation de service</td>
                        <td>Service Audit</td>
                        <td>08/12/2025</td>
                        <td>Terminé</td>
                        <td>
                            <span class="status-badge status-valide">
                                <i class="fas fa-check-circle"></i> Validé
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action btn-view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action btn-edit" title="Archiver">
                                    <i class="fas fa-archive"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Ligne 5 -->
                    <tr>
                        <td><strong>#DRS-2025-0417</strong></td>
                        <td>Demande de congé exceptionnel</td>
                        <td>Service RH</td>
                        <td>07/12/2025</td>
                        <td>Rejeté</td>
                        <td>
                            <span class="status-badge status-rejete">
                                <i class="fas fa-times-circle"></i> Rejeté
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action btn-view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action btn-edit" title="Corriger">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <button class="page-btn">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn">4</button>
            <button class="page-btn">5</button>
            <button class="page-btn">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- Footer -->
        <div style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--border); color: var(--text); font-size: 0.9rem; text-align: center;">
            <p>Système de Gestion des Parapheurs DRS © 2025 - Module Parapheurs</p>
            <div style="display: flex; justify-content: center; gap: 1.5rem; margin-top: 0.8rem; color: var(--accent); font-size: 0.85rem;">
                <span>Total parapheurs : 82</span>
                <span>•</span>
                <span>Dernière mise à jour : 11/12/2025 15:42</span>
            </div>
        </div>
    </main>

    <script>
        // Menu mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Filtres
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Pagination
        document.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!this.querySelector('i')) {
                    document.querySelectorAll('.page-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Actions des boutons
        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Voir les détails du parapheur');
            });
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Modifier le parapheur');
            });
        });

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer ce parapheur ?')) {
                    alert('Parapheur supprimé');
                }
            });
        });
    </script>
</body>
</html>