<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>欢迎来到我的网站</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 添加加载动画 */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #2c3e50, #3498db, #9b59b6, #1abc9c);
            background-size: 400% 400%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: gradientAnimation 15s ease infinite, fadeOut 0.5s forwards 1s;
        }

        .loader-content {
            text-align: center;
            color: white;
        }

        .loader-spinner {
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 5px solid white;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes fadeOut {
            to { opacity: 0; visibility: hidden; }
        }
    </style>
</head>
<body>
<!-- 添加加载动画 -->
<div class="loader">
    <div class="loader-content">
        <div class="loader-spinner"></div>
        <h2>加载中...</h2>
    </div>
</div>

<script>
    // 页面加载完成后隐藏加载动画
    window.addEventListener('load', function() {
        document.querySelector('.loader').style.display = 'none';
    });
</script>