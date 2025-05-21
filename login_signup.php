<?php
session_start();
require_once 'db.php';
$isLoginPage = true;

error_reporting(E_ALL);
ini_set('display_errors', 1);

// التحقق من وجود جلسة نشطة
if (isset($_SESSION['user'])) {
    echo "<script>window.location.href = 'profile.html';</script>";
    exit();
}

// معالجة تسجيل الدخول
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user'] = $row['username'];
            echo "<script>window.location.href = 'index.html';</script>";
            
            exit();
        } else {
            $login_error = "uncorrect password!";
        }
    } else {
        $login_error = "this username does not exist!";
    }
    $stmt->close();
}

// معالجة التسجيل
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $user_picture = 'default-avatar.jpg';

    // معالجة رفع الصورة
    if (isset($_FILES['user_picture']) && $_FILES['user_picture']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $file = $_FILES['user_picture'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // التحقق من نوع الملف
        if (!in_array($file['type'], $allowedTypes) || !in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
            $signup_error = "نوع الملف غير مسموح به (JPG, PNG, GIF فقط)";
        } 
        // التحقق من حجم الملف
        elseif ($file['size'] > 2 * 1024 * 1024) {
            $signup_error = "الحجم الأقصى للصورة 2MB";
        } 
        // نقل الملف
        else {
            $fileName = uniqid() . '_' . basename($file['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $user_picture = $targetPath;
            } else {
                $signup_error = "فشل في رفع الملف";
            }
        }
    }

    // إذا لم يكن هناك أخطاء
    if (empty($signup_error)) {
        // التحقق من وجود المستخدم
        $check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $signup_error = "Username or email already exists!";
            $check_stmt->close();
        } else {
            // إدراج البيانات
            $insert_sql = "INSERT INTO users (username, email, password, phone, address, user_picture) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssssss", $username, $email, $password, $phone, $address, $user_picture);

            if ($insert_stmt->execute()) {
                $signup_success = "The account has been created successfully!";
            } else {
                $signup_error = "Error" . $insert_stmt->error;
            }
            $insert_stmt->close();
        }
    }
}


?>


<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="assets/css/all.min.css">
    <title>login</title>
    <style>

.login-parent{
          display: flex;
          flex-direction: row;
          align-items: center;
          justify-content: space-around;
          width: 100%;
          min-height: 688px;
          background-color: #f6f7e9;
          z-index: 5;
        }

        .login-parent .login-video{
          position: relative;
          overflow: hidden;
          width: 60%;
          min-height: 682px;
          border-radius: 10px;
        }

        #bg-video {
          position: absolute;
          top: 50%;
          left: 50%;
          width: 100%;
          height: 100%;
          object-fit: cover;
          transform: translate(-50%, -50%);
          z-index: -1;
          opacity: 0.9;
        }
        
        .login-parent .container{
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          gap: 2rem;
          width: 50%;
          min-height: 400px;
        }

        .container .back-from-login{
          display: flex;
          justify-content: flex-start;
          width: 100%;
          padding-left: 2rem;
        }

        .container .back-from-login button{
          padding: 0.3rem;
          background-color: #FF7043;
          border-radius: 5px;
          transition: 0.3s;
        }

        .container button:hover{
          scale: 0.95;
          background-color: tomato;
        }
        
        .container .form-container.active{
          display: flex;
          flex-direction: column;
          gap: 1.2rem;
          padding: 1rem;
          border: solid 0.2rem #FF7043;
          box-shadow: 0 0 20px 10px rgba(255, 112, 67, 0.7);
          border-radius: 10px;
          font-size: 1.5rem;
          width: 70%;
        }

        .container .form-container.active form{
          display: flex;
          flex-direction: column;
          align-items: center;
        }

        .container .form-container.active form input{
          border-radius: 5px;
          width: 70%;
          height: 2rem;
          text-indent: 1rem;
          text-transform: none;
        }
        
        .container .form-container.active form input:hover{
          scale: 1.05;
          border: 0.155rem solid tomato;
        }
        
        .container .form-container.active form input:focus{
          border: 0.155rem solid orangered;
          outline: none;
        }
        
        .container .form-container.active p{
          font-size: 1rem;
        }

        .container .form-container.active p a{
          font-size: 0.8rem;
          color: green;
          cursor: pointer;
        }
        .container .form-container.active p a:hover{
          color: tomato;
        }

        .container .form-container.active p a{
          padding-left: 0.5rem;
        }

        .container .form-container.active div{
          display: flex;
          flex-direction: column;
          flex-wrap: wrap;
          justify-content: center;
          align-items: center;
          gap: 1rem;
        }
        
        .container .form-container.active div label{
          border-top: solid #FF7043 0.2rem; 
          font-size: 1rem;
          text-align: center;
          padding-top: 0.7rem;
        }

        .container .form-container.active div input:hover{
          border: none;
        }

        .form-container.active button{
          width: 20%;
          padding: 0.2rem;
          font-size: 1rem;
          border-radius: 5px;
          background-color: #FF7043;
        }
        
        .form-container {
            display: none;
        }
        .form-container:last-of-type form{
          gap: 1rem;
        }
        .form-container.active {
            display: flex;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }


        @media (min-width:100px) and (max-width:600px){

          .login-parent{
            align-items: flex-start;
          }
          
          .login-parent .login-video{
            position: fixed;
            width: 100%;
            min-height: 682px;
            border-radius: 10px;
        }
        
        
          #bg-video {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            opacity: 0.8;
          }
        
          .login-parent .container{
            padding-top: 4rem;
            z-index: 5;
            background-color: unset;
            width: 100%;
          }

          .container .form-container.active{
            background-color: rgba(255, 255, 255, 0.5);
            width: 90%;
          }

          .form-container.active button{
            width: 40%;
          }

        }

        @media (min-width:600px) and (max-width:1000px){

          .form-container.active button{
            width: 40%;
          }
        }

        

    </style>
    <script>
        function showSignupForm() {
            document.getElementById('login-form').classList.remove('active');
            document.getElementById('signup-form').classList.add('active');
        }
        function showLoginForm() {
            document.getElementById('signup-form').classList.remove('active');
            document.getElementById('login-form').classList.add('active');
        }
    </script>
</head>
<body>
  <div class="login-parent">
    <div class="login-video">
      <video autoplay muted loop id="bg-video">
        <source src="imgs/music-video.mp4" type="video/mp4">
      </video>
    </div>
    <div class="container">
        <!-- رسائل الخطأ أو النجاح -->
        <?php if (isset($login_error)): ?>
            <div class="error"><?php echo $login_error; ?></div>
        <?php endif; ?>
        <?php if (isset($signup_error)): ?>
            <div class="error"><?php echo $signup_error; ?></div>
        <?php endif; ?>
        <?php if (isset($signup_success)): ?>
            <div class="success"><?php echo $signup_success; ?></div>
        <?php endif; ?>

        <div class="back-from-login">
            <button onclick="window.location.href='index.html'">BACK</button>
        </div>

        <!-- نموذج تسجيل الدخول -->
        <div id="login-form" class="form-container active">
            <h3>Login</h3>
            <form action="login_signup.php" method="post">
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit" name="login">Enter</button>
            </form>
            <p>Don't Have an Account? <a href="#" onclick="showSignupForm()">Create an Account</a></p>
        </div>

        <!-- نموذج التسجيل -->
        <div id="signup-form" class="form-container">
            <h3>Create an Account</h3>
            <form action="login_signup.php" method="post" enctype="multipart/form-data">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="tel" name="phone" placeholder="Phone" required>
                <input type="text" name="address" placeholder="Address" required>

                <div>
                <label for="picture"> choose a profile picture: </label>
                <input id="picture" type="file" name="user_picture" accept="image/*">
                </div>

                <button type="submit" name="signup">Create</button>
            </form>
            <p>You Have Already Account<a href="#" onclick="showLoginForm()">Login</a></p>
        </div>
    </div>
  </div>
</body>
</html>
