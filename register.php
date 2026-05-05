<?php
// include 'db.php';
// Lógica de cadastro desativada.
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro - StudyRank</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    height: 100vh;
    margin: 0;
    background: linear-gradient(135deg, #a020f0, #0d6efd);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: Arial;
}

.card-login {
    background: #eee;
    padding: 30px;
    border-radius: 20px;
    width: 350px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    text-align: center;
}

input {
    border-radius: 10px !important;
}

.btn-gradient {
    background: linear-gradient(90deg, #a020f0, #0d6efd);
    border: none;
    color: white;
    border-radius: 20px;
    padding: 10px;
    width: 100%;
    font-weight: bold;
}

.link {
    color: #a020f0;
    text-decoration: none;
    font-weight: bold;
}
</style>

</head>
<body>

<div class="card-login">

    <div style="font-size:40px;">🏆</div>

    <h3>StudyRank</h3>
    <p style="color:#666;">Crie sua conta</p>

    <form onsubmit="event.preventDefault();"> <div class="mb-3 text-start">
            <label>Nome</label>
            <input type="text" name="name" class="form-control" placeholder="Seu nome" required>
        </div>

        <div class="mb-3 text-start">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
        </div>

        <div class="mb-3 text-start">
            <label>Senha</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-gradient">Criar Conta</button>

    </form>

    <br>
    
    <a href="index.php" class="link">Já tem conta? Entra</a>

</div>

</body>
</html>