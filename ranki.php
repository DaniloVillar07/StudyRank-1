<?php
// Simulação de dados do utilizador
//$user_name = "welli";
//$user_level = 1;
//$user_xp = 0;

// Simulação de dados do ranking (normalmente viria do SQL)


$user_current_pos = "Não ranqueado";
$user_current_xp = 0;
?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking - StudyRank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple-gradient: linear-gradient(90deg, #a020f0, #0d6efd);
            --bg-light: #f8f9fa;
            --purple-main: #a020f0;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
        }

        /* Barra de Navegação */
        .navbar {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 0.5rem 2rem;
        }
        .navbar-brand b {
            color: #333;
            font-size: 1.2rem;
        }
        .nav-link {
            font-weight: 500;
            color: #666;
            padding: 0.5rem 1.2rem !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-link.active {
            background-color: var(--purple-main);
            color: white !important;
            border-radius: 10px;
        }
        .user-nav-info {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.85rem;
        }
        .avatar-box {
            background: #fdf2e9;
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Banner de Posição */
        .user-rank-banner {
            background: var(--purple-gradient);
            border-radius: 15px;
            padding: 20px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        /* Lista de Ranking */
        .ranking-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .ranking-title {
            background: var(--purple-gradient);
            color: white;
            padding: 15px 25px;
            font-weight: bold;
        }

        .ranking-item {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            border-bottom: 1px solid #f0f0f0;
        }

        .rank-number { width: 40px; font-weight: bold; color: #666; }
        .rank-avatar { 
            width: 45px; height: 45px; background: #f0f0f0; 
            border-radius: 10px; display: flex; align-items: center; 
            justify-content: center; margin-right: 15px; font-size: 1.2rem;
        }

        .user-details { flex-grow: 1; }
        .user-name { font-weight: 600; margin-bottom: 0; font-size: 0.9rem; }
        .user-xp-sub { font-size: 0.75rem; color: #888; }

        .xp-bar {
            width: 150px;
            height: 8px;
            background: #eee;
            border-radius: 10px;
            overflow: hidden;
            margin-left: 15px;
        }
        .xp-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #a020f0, #0d6efd);
        }

        /* Banner Desafio */
        .challenge-banner {
            background: linear-gradient(135deg, #ff5f6d, #ffc371);
            border-radius: 20px;
            padding: 25px;
            color: white;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .challenge-timer {
            background: white;
            color: #333;
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.85rem;
            margin-top: 10px;
            display: inline-block;
        }

        /* Cards de Dica/Estratégia/Meta */
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            border: 1px solid #eee;
            height: 100%;
        }
        .info-card img, .info-card span.emoji {
            font-size: 1.5rem;
            margin-bottom: 10px;
            display: block;
        }
    </style>
</head>
<body>

<!-- Barra de Navegação -->
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
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">🏠 Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="ranking.php">🏆 Ranking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">👤 Perfil</a>
                </li>
            </ul>
        </div>

        <div class="user-nav-info">
            <div class="text-end">
                <div class="fw-bold"><?php echo $user_name; ?></div>
                <div class="text-muted" style="font-size: 0.75rem;">Nível <?php echo $user_level; ?> • <?php echo $user_xp; ?> XP</div>
            </div>
            <div class="avatar-box">👨‍💻</div>
            <a href="index.php" class="text-decoration-none text-muted ms-2">Sair</a>
        </div>
    </div>
</nav>

<div class="container py-2">
    <div class="text-center mb-4">
        <h3 class="fw-bold">🏆 Ranking Semanal</h3>
        <p class="text-muted small">Competição reinicia toda segunda-feira</p>
    </div>

    <!-- Posição do Utilizador Logado -->
    <div class="user-rank-banner">
        <div class="d-flex align-items-center gap-3">
            <div style="font-size: 2rem;">👨‍💻</div>
            <div>
                <small class="opacity-75">Sua posição</small>
                <h5 class="mb-0 fw-bold"><?php echo $user_current_pos; ?></h5>
            </div>
        </div>
        <div class="text-end">
            <small class="opacity-75">XP desta semana</small>
            <h3 class="mb-0 fw-bold"><?php echo $user_current_xp; ?></h3>
        </div>
    </div>

    <div class="ranking-container">
        <div class="ranking-title">Top 10 da Semana</div>
        
        <?php foreach ($rankData as $user): ?>
        <div class="ranking-item">
            <div class="rank-number"><?php echo $user['pos']; ?></div>
            <div class="rank-avatar"><?php echo $user['avatar']; ?></div>
            <div class="user-details">
                <p class="user-name"><?php echo htmlspecialchars($user['name']); ?></p>
                <span class="user-xp-sub"><?php echo $user['xp']; ?> XP esta semana</span>
            </div>
            <div class="xp-bar">
                <div class="xp-bar-fill" style="width: <?php echo $user['perc']; ?>%"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Desafio Semanal -->
    <div class="challenge-banner">
        <div style="font-size: 2.5rem;">🔥</div>
        <div>
            <h6 class="fw-bold mb-1">Desafio da Semana</h6>
            <p class="small mb-0 opacity-90">Complete todos os 5 quizzes até domingo para ganhar um bónus de 50 XP!</p>
            <div class="challenge-timer">
            </div>
        </div>
    </div>

    <!-- Rodapé de Dicas -->
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div class="info-card">
                <span class="emoji">📚</span>
                <h6 class="fw-bold">Dica</h6>
                <p class="small text-muted mb-0">Estude todo dia para manter seu streak</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <span class="emoji">⚡</span>
                <h6 class="fw-bold">Estratégia</h6>
                <p class="small text-muted mb-0">Complete quizzes difíceis para mais XP</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <span class="emoji">🎯</span>
                <h6 class="fw-bold">Meta</h6>
                <p class="small text-muted mb-0">Alcance o top 3 para ganhar badge especial</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>