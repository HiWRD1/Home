<?php
// 确保 data 目录存在
if (!is_dir('data')) {
    mkdir('data', 0755, true);
}

// 访问量统计文件路径
$countsFile = 'data/counts.txt';

// 默认计数数组
$defaultCounts = [
    'last_visit' => '',
    'last_month' => '',
    'last_year' => '',
    'daily' => 0,
    'monthly' => 0,
    'yearly' => 0,
    'total' => 0
];

// 尝试读取计数数据
$counts = $defaultCounts;
if (file_exists($countsFile)) {
    $fileContent = file_get_contents($countsFile);
    if ($fileContent !== false) {
        $savedCounts = json_decode($fileContent, true);
        if (is_array($savedCounts)) {
            $counts = array_merge($defaultCounts, $savedCounts);
        }
    }
}

// 更新访问量
$now = new DateTime();
$today = $now->format('Y-m-d');
$month = $now->format('Y-m');
$year = $now->format('Y');

if ($counts['last_visit'] !== $today) {
    // 新的一天
    $counts['daily'] = 0;

    if ($counts['last_month'] !== $month) {
        // 新的月份
        $counts['monthly'] = 0;
        $counts['last_month'] = $month;

        if ($counts['last_year'] !== $year) {
            // 新的一年
            $counts['yearly'] = 0;
            $counts['last_year'] = $year;
        }
    }

    $counts['last_visit'] = $today;
}

$counts['daily']++;
$counts['monthly']++;
$counts['yearly']++;
$counts['total']++;

// 保存更新后的数据
file_put_contents($countsFile, json_encode($counts));

// 包含公共头部
include_once 'includes/header.php';
?>

    <div class="header">
        <h1 class="typewriter">Hi！欢迎来到我的网站！</h1>
    </div>

    <div class="card-container">
        <div class="card" id="a">
            <h2><i class="fas fa-globe"></i> 网站1</h2>
            <p>详细说明</p>
        </div>
        <div class="card" id="b">
            <h2><i class="fas fa-envelope"></i> 网站2</h2>
            <p>详细说明</p>
        </div>
        <div class="card" id="c">
            <h2><i class="fas fa-image"></i> 网站3</h2>
            <p>详细说明</p>
        </div>
        <div class="card" id="d">
            <h2><i class="fas fa-music"></i> 网站4</h2>
            <p>详细说明</p>
        </div>
    </div>

    <canvas id="trailCanvas"></canvas>

    <div class="content-container">
        <div class="hitokoto-box">
            <h2>每日一言</h2>
            <a class="hitokoto-text" href="#" target="_blank">加载中...</a>
            <button class="hitokoto-btn">换一句</button>
        </div>

        <div class="counter-box">
            <div class="counter-item">
                <div class="counter-number"><?php echo $counts['daily']; ?></div>
                <div class="counter-label">今日访问</div>
            </div>
            <div class="counter-item">
                <div class="counter-number"><?php echo $counts['monthly']; ?></div>
                <div class="counter-label">本月访问</div>
            </div>
            <div class="counter-item">
                <div class="counter-number"><?php echo $counts['yearly']; ?></div>
                <div class="counter-label">今年访问</div>
            </div>
            <div class="counter-item">
                <div class="counter-number"><?php echo $counts['total']; ?></div>
                <div class="counter-label">总访问量</div>
            </div>
        </div>
    </div>

    <script>
        function initHitokoto() {
            const box = document.querySelector('.hitokoto-box');
            const text = document.querySelector('.hitokoto-text');
            const btn = document.querySelector('.hitokoto-btn');

            async function fetchQuote() {
                try {
                    text.innerText = "加载中...";
                    const response = await fetch('https://v1.hitokoto.cn');
                    const data = await response.json();

                    text.href = `https://hitokoto.cn/?uuid=${data.uuid}`;
                    text.innerText = data.hitokoto;

                    if(data.from_who || data.from) {
                        const source = document.createElement('div');
                        source.style = "color:#777; margin-top:10px; font-style:italic";
                        source.innerText = `${data.from_who ? data.from_who : ''}${data.from ? (data.from_who ? ' · ' : '') + data.from : ''}`;
                        box.appendChild(source);
                    }
                } catch {
                    text.innerText = "加载失败，点击重试";
                    text.onclick = fetchQuote;
                }
            }

            btn.addEventListener('click', function() {
                const source = box.querySelector('div');
                if(source) source.remove();
                fetchQuote();
            });

            fetchQuote();
        }

        document.addEventListener('DOMContentLoaded', initHitokoto);
    </script>

    <script src="js/script.js"></script>

<?php include_once 'includes/footer.php'; ?>