<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login/signup</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <div class="container">
        <!-- login form -->
        <div class="form-box login">
            <form action="#">
                <h1>Login</h1>
                <div class="input-box">
                    <input type="text" placeholder="username" required>
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="password" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div class="forgot-link">
                    <a href="#">Lupa Kata Sandi?</a>
                    
                </div>
                <button type="submit" class="btn">Masuk</button>
            </form>
        </div>
        <!-- register form -->
        <div class="form-box register">
            <form action="#">
                <h1>Daftar</h1>
                <div class="input-box">
                    <input type="text" placeholder="username" required>
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="email" placeholder="email" required>
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="password" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
                <button type="submit" class="btn">login</button>
            </form>
        </div>
        <!-- toggle box -->
        <div class="toggle-box">
            <!-- toggle box kiri -->
            <div class="toggle-panel toggle-left">
                <h1>Hallo Selamat Datamg!</h1>
                <p>Tidak Punya Akun?</p>
                <button class="btn register-btn">Daftar</button>
            </div>
            <!-- toggle box kanan -->
            <div class="toggle-panel toggle-right">
                <h1>Selamat Datang!</h1>
                <p>Sudah Punya Akun?</p>
                <button class="btn login-btn">Masuk</button>
            </div>
        </div>
    </div>
    <script src="js/login.js"></script>
</body>

</html>