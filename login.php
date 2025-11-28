<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin - Rental-diGue</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { 
      background: linear-gradient(135deg, #667eea, #764ba2); 
      min-height: 100vh; 
      display: flex; 
      align-items: center; 
      justify-content: center; 
      font-family: 'Segoe UI', sans-serif;
    }
    .login-box { 
      background: white; 
      padding: 40px; 
      border-radius: 20px; 
      box-shadow: 0 20px 50px rgba(0,0,0,0.3); 
      width: 90%; 
      max-width: 420px; 
      text-align: center; 
    }
    .login-box h2 { margin-bottom: 30px; color: #333; font-size: 28px; }
    input { 
      width: 100%; 
      padding: 15px; 
      margin: 12px 0; 
      border: 2px solid #ddd; 
      border-radius: 50px; 
      font-size: 16px; 
    }
    input:focus { outline: none; border-color: #25d366; }
    button { 
      width: 100%; 
      padding: 15px; 
      background: #25d366; 
      color: white; 
      border: none; 
      border-radius: 50px; 
      font-size: 18px; 
      font-weight: bold; 
      cursor: pointer; 
      margin-top: 10px;
    }
    button:hover { background: #1da851; }
    .back { margin-top: 25px; }
    .back a { color: #25d366; font-weight: bold; }
    .info { margin-top: 20px; font-size: 14px; color: #666; }
  </style>
</head>
<body>

<div class="login-box">
  <h2>LOGIN</h2>

  <?php if(isset($_GET['error'])): ?>
    <p style="color:red; margin:15px 0;">Username atau password salah!</p>
  <?php endif; ?>
  <?php if(isset($_GET['logout'])): ?>
    <p style="color:green; margin:15px 0;">Logout berhasil!</p>
  <?php endif; ?>

  <form action="proses/login_proses_aktif.php" method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">MASUK KE DASHBOARD</button>
  </form>

  <div class="back">
    <a href="index.html">← Kembali ke Website</a>
  </div>

</div>
</body>
</html>