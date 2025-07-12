<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人门户网站 | 开源项目</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="assets/logo.png" type="image/png">

    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<!-- Loading animation -->
<div class="loader">
    <div class="loader-content">
        <div class="loader-spinner"></div>
        <h2>加载中...</h2>
    </div>
</div>

<script>
    // Hide loader when page finishes loading
    window.addEventListener('load', function() {
        document.querySelector('.loader').style.display = 'none';
    });
</script>