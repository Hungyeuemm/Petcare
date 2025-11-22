<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register — 3D Cat Cute</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap');

    :root {
      --accent: #ff96b6;
      --accent2: #ffc4d6;
      --shadow: rgba(0, 0, 0, 0.25);
    }

    * {
      box-sizing: border-box
    }

    html,
    body {
      height: 100%;
      margin: 0;
      font-family: Poppins, system-ui, sans-serif;
    }

    body {
      background-image: url('https://chobaove.com/wp-content/uploads/2018/12/golden-retriever-dogs-puppies-1.jpg');
      background-size: cover;
      background-position: center;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
      animation: fadeIn 1.2s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-box {
      width: 100%;
      max-width: 1100px;
      background: rgba(255, 255, 255, 0.8);
      border-radius: 22px;
      box-shadow: 0 20px 40px var(--shadow);
      display: flex;
      overflow: hidden;
      backdrop-filter: blur(6px);
      animation: floatIn 1.5s ease;
    }

    @keyframes floatIn {
      from {
        opacity: 0;
        transform: scale(0.96);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .left {
      flex: 1;
      position: relative;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(8px);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .cat {
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 420px;
      opacity: 0.55;
      transform: scale(1);
      transition: transform 3s ease-in-out;
      animation: catBreath 6s ease-in-out infinite;
      pointer-events: none;
    }

    @keyframes catBreath {

      0%,
      100% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.05);
      }
    }

    .left::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(120deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.15));
    }

    .right {
      flex: 1;
      background: linear-gradient(180deg, #fff9fb, #ffe9f2);
      padding: 60px 70px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      animation: fadeIn 1.6s ease;
    }

    h1 {
      font-size: 30px;
      font-weight: 700;
      margin: 0 0 10px;
      color: #333;
    }

    h1 span {
      color: var(--accent)
    }

    .subtitle {
      color: #666;
      margin-bottom: 24px;
    }

    .input {
      width: 100%;
      padding: 16px;
      border-radius: 12px;
      border: 2px solid rgba(0, 0, 0, 0.1);
      margin-bottom: 18px;
      font-size: 16px;
      background: rgba(255, 255, 255, 0.8);
    }

    .btn-login {
      align-self: flex-end;
      padding: 14px 32px;
      background: linear-gradient(90deg, var(--accent), var(--accent2));
      border: none;
      border-radius: 12px;
      color: #fff;
      font-weight: 700;
      box-shadow: 0 8px 0 rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: transform .2s, box-shadow .2s;
    }

    .btn-login:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 12px rgba(0, 0, 0, 0.2);
    }

    p {
      margin-top: 20px;
      text-align: right;
    }

    a {
      text-decoration: none;
      color: var(--accent);
      font-weight: 600;
    }

    a:hover {
      text-decoration: underline;
    }

    @media(max-width:960px) {
      .login-box {
        flex-direction: column;
        max-width: 90%;
      }

      .right {
        padding: 40px;
      }

      .left {
        min-height: 260px;
      }

      .cat {
        width: 300px;
      }
    }
  </style>
</head>

<body>
  <div class="login-box">
    <div class="left">
      <img class="cat" src="https://images.unsplash.com/photo-1592194996308-7b43878e84a6?q=80&w=800&auto=format&fit=crop" alt="3D Cat">
    </div>

    <div class="right">
      <h1>Create your <span>account!</span></h1>
      <div class="subtitle">Join us to start your learning journey</div>

      <!-- 🧾 FORM REGISTER -->
      <form action="../handle/register_process.php" method="POST">
        <input class="input" type="text" name="username" placeholder="Username" required />
        <input class="input" type="password" name="password" placeholder="Password" required />
        <input class="input" type="password" name="confirm_password" placeholder="Confirm Password" required />
        <input class="input" type="text" name="name_customer" placeholder="Full name" required />
        <input class="input" type="email" name="email_customer" placeholder="Email" required />
        <input class="input" type="text" name="phone_customer" placeholder="Phone number" required />
        <input class="input" type="text" name="address" placeholder="Address" required />
        <button class="btn-login" type="submit">Register</button>
      </form>

      <p>Đã có tài khoản? <a href="../index.php">Đăng nhập ngay</a></p>
    </div>
  </div>
</body>

</html>