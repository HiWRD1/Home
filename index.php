<?php
// Hello
// Ensure data directory exists
if (!is_dir('data')) {
    mkdir('data', 0755, true);
}

// Counts file path
$countsFile = 'data/counts.txt';

// Default counts array
$defaultCounts = [
    'last_visit' => '',
    'last_month' => '',
    'last_year' => '',
    'daily' => 0,
    'monthly' => 0,
    'yearly' => 0,
    'total' => 0,
    'history' => []
];

// Initialize counts array
$counts = $defaultCounts;

// Load saved counts if file exists
if (file_exists($countsFile)) {
    $savedCounts = json_decode(file_get_contents($countsFile), true);
    if (is_array($savedCounts)) {
        $counts = array_merge($defaultCounts, $savedCounts);
    }
}

// Get current date info
$now = new DateTime();
$today = $now->format('Y-m-d');
$month = $now->format('Y-m');
$year = $now->format('Y');

// Reset counters if new day
if ($counts['last_visit'] !== $today) {
    $counts['daily'] = 0;

    // Reset monthly if new month
    if ($counts['last_month'] !== $month) {
        $counts['monthly'] = 0;
        $counts['last_month'] = $month;

        // Reset yearly if new year
        if ($counts['last_year'] !== $year) {
            $counts['yearly'] = 0;
            $counts['last_year'] = $year;
        }
    }

    $counts['last_visit'] = $today;
}

// Increment counters
$counts['daily']++;
$counts['monthly']++;
$counts['yearly']++;
$counts['total']++;

// Add to history for charts
if (!isset($counts['history'][$year])) {
    $counts['history'][$year] = [];
}

if (!isset($counts['history'][$year][$month])) {
    $counts['history'][$year][$month] = [];
}

if (!isset($counts['history'][$year][$month][$today])) {
    $counts['history'][$year][$month][$today] = 0;
}

$counts['history'][$year][$month][$today]++;

// Save updated counts
file_put_contents($countsFile, json_encode($counts));

// Get visitor IP
$visitorIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

// Include header
include_once 'includes/header.php';

// Include access control
include_once 'access-control.php';
?>

    <div class="header">
        <h1 class="typewriter">欢迎来到我的门户网站</h1>

        <!-- Site name and visitor info -->
        <div class="site-info">
            <div class="site-name">WRD1145的个人中心</div>
            <div class="visitor-ip">您的IP: <?= $visitorIP ?></div>
        </div>
    </div>

    <div class="card-container">
        <div class="card" id="github">
            <h2><i class="fab fa-github"></i> GitHub</h2>
            <p>我的开源项目</p>
        </div>
        <div class="card" id="blog">
            <h2><i class="fas fa-blog"></i> 博客</h2>
            <p>我的技术文章</p>
        </div>
        <div class="card" id="portfolio">
            <h2><i class="fas fa-briefcase"></i> 作品集</h2>
            <p>我的专业作品</p>
        </div>
        <div class="card" id="contact">
            <h2><i class="fas fa-envelope"></i> 联系</h2>
            <p>与我取得联系</p>
        </div>
    </div>

    <canvas id="trailCanvas"></canvas>

    <div class="content-container">
        <div class="hitokoto-box">
            <h2>每日一句</h2>
            <a class="hitokoto-text" href="#" target="_blank">加载中...</a>
            <button class="hitokoto-btn" id="hitokoto-btn">换一句</button>
        </div>

        <div class="counter-box">
            <div class="counter-item">
                <div class="counter-number"><?= $counts['daily'] ?></div>
                <div class="counter-label">今日</div>
            </div>
            <div class="counter-item">
                <div class="counter-number"><?= $counts['monthly'] ?></div>
                <div class="counter-label">本月</div>
            </div>
            <div class="counter-item">
                <div class="counter-number"><?= $counts['yearly'] ?></div>
                <div class="counter-label">今年</div>
            </div>
            <div class="counter-item">
                <div class="counter-number"><?= $counts['total'] ?></div>
                <div class="counter-label">总计</div>
            </div>
        </div>
    </div>

    <!-- Statistics charts section -->
    <div class="charts-container">
        <h2 class="section-title">访问统计</h2>

        <div class="chart-row">
            <div class="chart-box">
                <h3>每日访问量（最近7天）</h3>
                <canvas id="dailyChart"></canvas>
            </div>

            <div class="chart-box">
                <h3>月度访问量（最近6个月）</h3>
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <div class="chart-box full-width">
            <h3>年度访问量（最近3年）</h3>
            <canvas id="yearlyChart"></canvas>
        </div>
    </div>

    <!-- Custom Modal -->
    <div id="custom-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <div class="modal-icon" id="modal-icon"></div>
            <h2 id="modal-title"></h2>
            <p id="modal-message"></p>
            <div id="modal-progress"></div>
            <div id="modal-queue"></div>
            <button id="modal-confirm" class="modal-btn">确定</button>
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const visitorStats = {
            daily: <?= $counts['daily'] ?>,
            monthly: <?= $counts['monthly'] ?>,
            yearly: <?= $counts['yearly'] ?>,
            total: <?= $counts['total'] ?>,
            history: <?= json_encode($counts['history']) ?>,
            ip: '<?= $visitorIP ?>'
        };

        // Access control settings
        const accessControl = {
            hitokotoRate: 3000,   // 3 seconds
            cardRate: 3000,       // 3 seconds
            hitokotoLimit: 10,    // 10 clicks per minute
            cardLimit: 5,         // 5 clicks per minute
            banTime: 600000,      // 10 minutes in ms
            queueRate: 2000,      // 2 seconds per user
            queueThreshold: 5     // 5 simultaneous IPs
        };

        // Hitokoto API integration
        function initHitokoto() {
            const box = document.querySelector('.hitokoto-box');
            const text = document.querySelector('.hitokoto-text');
            const btn = document.getElementById('hitokoto-btn');

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

                    // Record successful fetch
                    recordAccess('hitokoto', true);
                } catch {
                    text.innerText = "加载失败，点击重试";
                    text.onclick = fetchQuote;
                }
            }

            btn.addEventListener('click', function() {
                // Check access before proceeding
                checkAccess('hitokoto', () => {
                    const source = box.querySelector('div');
                    if(source) source.remove();
                    fetchQuote();
                });
            });

            fetchQuote();
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initHitokoto();
            initCharts();
            initModal();
            initCardClickHandlers();
            initMouseTrail(); // Initialize the enhanced mouse trail
        });
    </script>

    <script src="js/script.js"></script>

<?php include_once 'includes/footer.php'; ?>