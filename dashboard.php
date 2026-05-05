<?php
// Simulação de dados do usuário
//$user_name = "";
//$user_level ="";
//$user_xp = ;
//$xp_needed = ;
//?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StudyRank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple-main: #a020f0;
            --blue-main: #0d6efd;
            --bg-light: #f8f9fa;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        /* Navbar Custom */
        .navbar {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 0.5rem 2rem;
        }
        .nav-link {
            font-weight: 500;
            color: #666;
            padding: 0.5rem 1.5rem !important;
            border-radius: 10px;
        }
        .nav-link.active {
            background-color: var(--purple-main);
            color: white !important;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
        }

        /* Stats Cards */
        .stat-card {
            border-radius: 15px;
            padding: 20px;
            color: white;
            height: 140px;
            position: relative;
            overflow: hidden;
            border: none;
        }
        .stat-card .icon-bg {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 1.5rem;
            opacity: 0.8;
        }
        .stat-card .label { font-size: 0.9rem; opacity: 0.9; }
        .stat-card .value { font-size: 2rem; font-weight: bold; margin-top: 5px; }
        .stat-card .subtext { font-size: 0.8rem; opacity: 0.8; }

        .bg-xp { background: #9d31ff; }
        .bg-streak { background: #ff5e00; }
        .bg-quizzes { background: #007bff; }
        .bg-badges { background: #00c853; }

        /* Progress Bar */
        .progress-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .progress { height: 12px; border-radius: 10px; background-color: #e9ecef; }
        .progress-bar { background: #ddd; }

        /* Challenge Cards */
        .card-challenge {
            background: white;
            border-radius: 15px;
            border: 1px solid #eee;
            padding: 20px;
            transition: transform 0.2s;
            height: 100%;
        }
        .card-challenge:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .btn-start {
            background: var(--purple-main);
            color: white;
            border-radius: 10px;
            border: none;
            padding: 8px 20px;
            font-weight: 600;
        }
        .xp-tag {
            color: var(--purple-main);
            font-weight: bold;
            font-size: 0.9rem;
        }

        /* Badge Cards */
        .badge-card {
            background: white;
            border-radius: 15px;
            border: 1px solid #eee;
            padding: 20px;
            text-align: center;
            opacity: 0.6; /* Grayed out effect from image */
            transition: opacity 0.3s;
        }
        .badge-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            filter: grayscale(100%);
        }
        .badge-card h6 { font-size: 0.95rem; margin-bottom: 5px; }
        .badge-card p { font-size: 0.8rem; color: #888; margin-bottom: 0; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg mb-4">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <span style="font-size: 24px; margin-right: 10px;">🏆</span>
            <div>
                <b class="d-block" style="line-height: 1;">StudyRank</b>
                <small style="font-size: 10px; color: #888;">Aprenda e compita</small>
            </div>
        </a>
        
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav gap-2">
                <li class="nav-item"><a class="nav-link active" href="#"><span class="me-1">🏠</span> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><span class="me-1">🏆</span> Ranking</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><span class="me-1">👤</span> Perfil</a></li>
            </ul>
        </div>

        <div class="user-info">
            <div class="text-end">
                <div class="fw-bold"><?php echo $user_name; ?></div>
                <div class="text-muted">Nível <?php echo $user_level; ?> • <?php echo $user_xp; ?> XP</div>
            </div>
            <div style="background: #fdf2e9; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                👨‍💻
            </div>
            <a href="#" class="ms-2 text-muted text-decoration-none small">Sair</a>
        </div>
    </div>
</nav>

<div class="container">
    <!-- Top Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card bg-xp">
                <div class="icon-bg">⚡</div>
                <div class="label">XP Total</div>
                <div class="value"><?php echo $user_xp; ?></div>
                <div class="subtext"><span class="badge bg-white text-primary">Nível 1</span></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-streak">
                <div class="icon-bg">🔥</div>
                <div class="label">Streak</div>
                <div class="value">0</div>
                <div class="subtext">dias consecutivos</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-quizzes">
                <div class="icon-bg">✅</div>
                <div class="label">Quizzes</div>
                <div class="value">0/5</div>
                <div class="subtext">0% completo</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-badges">
                <div class="icon-bg">🏅</div>
                <div class="label">Badges</div>
                <div class="value">0</div>
                <div class="subtext">conquistadas</div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="progress-container mb-5">
        <div class="d-flex justify-content-between mb-2">
            <span class="small fw-bold">Progresso para Nível 2</span>
            <span class="small text-muted"><?php echo $user_xp; ?>/<?php echo $xp_needed; ?> XP</span>
        </div>
        <div class="progress">
            <div class="progress-bar" style="width: 0%"></div>
        </div>
    </div>

    <!-- Desafios Disponíveis -->
    <h4 class="mb-4">Desafios Disponíveis</h4>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
                </div>
            </div>
        </div>
     
    </div>

    <!-- Badges Conquistadas -->
    <h4 class="mb-4">Badges Conquistadas</h4>
    <div class="row g-3 mb-5">
        <div class="col-md">
            <div class="badge-card">
                <div class="badge-icon">🎓</div>
                <h6>Estudante Iniciante</h6>
                <p>Complete 3 quizzes</p>
            </div>
        </div>
        <div class="col-md">
            <div class="badge-card">
                <div class="badge-icon">👨‍💻</div>
                <h6>Mestre dos Códigos</h6>
                <p>Complete todos os 5 quizzes</p>
            </div>
        </div>
        <div class="col-md">
            <div class="badge-card">
                <div class="badge-icon">🔥</div>
                <h6>Streak de Fogo</h6>
                <p>Mantenha um streak de 7 dias</p>
            </div>
        </div>
        <div class="col-md">
            <div class="badge-card">
                <div class="badge-icon">⭐</div>
                <h6>Expert em XP</h6>
                <p>Acumule 200 XP</p>
            </div>
        </div>
        <div class="col-md">
            <div class="badge-card">
                <div class="badge-icon">🏆</div>
                <h6>Top 3 Ranking</h6>
                <p>Alcance o top 3 do ranking semanal</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>