<?php

//?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - StudyRank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple-main: #a020f0;
            --purple-gradient: linear-gradient(135deg, #a020f0, #6f42c1);
            --bg-light: #f8f9fa;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        /* Navbar */
        .navbar {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 0.5rem 2rem;
        }
        .nav-link {
            font-weight: 500;
            color: #666;
            padding: 0.5rem 1.2rem !important;
        }
        .nav-link.active {
            background-color: var(--purple-main);
            color: white !important;
            border-radius: 10px;
        }

        /* Profile Card */
        .profile-header-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        .profile-header-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 100px;
            background: var(--purple-gradient);
            z-index: 0;
        }
        .avatar-large {
            width: 120px;
            height: 120px;
            background: #fdf2e9;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            margin: 0 auto 20px;
            position: relative;
            z-index: 1;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .profile-info { position: relative; z-index: 1; }
        .level-badge {
            background: var(--purple-main);
            color: white;
            padding: 5px 15px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 10px;
        }

        /* Stats Grid */
        .stat-box {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            border: 1px solid #eee;
            transition: transform 0.2s;
        }
        .stat-box:hover { transform: translateY(-5px); }
        .stat-val { font-size: 1.5rem; font-weight: 700; color: var(--purple-main); display: block; }
        .stat-label { font-size: 0.8rem; color: #888; font-weight: 600; text-transform: uppercase; }

        /* Badges Section */
        .badge-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .badge-item {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            border: 1px solid #eee;
            transition: all 0.3s;
        }
        .badge-item.locked { opacity: 0.5; filter: grayscale(1); }
        .badge-icon { font-size: 2rem; margin-bottom: 10px; display: block; }
        .badge-name { font-weight: 700; font-size: 0.9rem; margin-bottom: 5px; }
        .badge-desc { font-size: 0.75rem; color: #999; line-height: 1.2; }

        .progress { height: 10px; border-radius: 10px; background: #eee; }
        .progress-bar { background: var(--purple-main); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg mb-4">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <span style="font-size: 24px; margin-right: 10px;">🏆</span>
            <div>
                <b class="d-block" style="line-height: 1;">StudyRank</b>
                <small style="font-size: 10px; color: #888;">Aprende e compita</small>
            </div>
        </a>
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav gap-2">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">🏠 Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="ranking.php">🏆 Ranking</a></li>
                <li class="nav-item"><a class="nav-link active" href="perfil.php">👤 Perfil</a></li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="index.php" class="btn btn-sm btn-outline-danger">Sair</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <!-- Coluna da Esquerda: Info Principal -->
        <div class="col-lg-4">
            <div class="profile-header-card">
                <div class="avatar-large">👨‍💻</div>
                <div class="profile-info">
                    <span class="level-badge">Nível <?php echo $user_level; ?></span>
                    <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($user_name); ?></h3>
                    <p class="text-muted small mb-4"><?php echo htmlspecialchars($user_email); ?></p>
                    
                    <div class="text-start mb-2 d-flex justify-content-between">
                        <small class="fw-bold">XP Atual</small>
                        <small class="text-muted"><?php echo $user_xp; ?> / <?php echo $xp_needed; ?> XP</small>
                    </div>
                    <div class="progress mb-4">
                        <div class="progress-bar" style="width: <?php echo $progress_percent; ?>%"></div>
                    </div>
                    
                    <button class="btn btn-primary w-100 border-0 py-2" style="background: var(--purple-main); border-radius: 12px;">Editar Perfil</button>
                </div>
            </div>
        </div>

        <!-- Coluna da Direita: Stats e Badges -->
        <div class="col-lg-8">
            <h5 class="fw-bold mb-3">Estatísticas Gerais</h5>
            <div class="row g-3 mb-5">
                <div class="col-md-4">
                    <div class="stat-box">
                        <span class="stat-val"><?php echo $user_streak; ?> 🔥</span>
                        <span class="stat-label">Dias de Streak</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <span class="stat-val"><?php echo $quizzes_done; ?> ✅</span>
                        <span class="stat-label">Quizzes Feitos</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <span class="stat-val"><?php echo $rank_position; ?> 🏆</span>
                        <span class="stat-label">Posição Global</span>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3">Emblemas e Conquistas</h5>
            <div class="badge-grid">
                <?php foreach ($badges as $badge): ?>
                    <div class="badge-item <?php echo ($badge['status'] == 'bloqueado') ? 'locked' : ''; ?>">
                        <span class="badge-icon"><?php echo $badge['icon']; ?></span>
                        <div class="badge-name"><?php echo $badge['name']; ?></div>
                        <div class="badge-desc"><?php echo $badge['desc']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>