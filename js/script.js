// Custom modal functions
function initModal() {
    const modal = document.getElementById('custom-modal');
    const closeBtn = document.querySelector('.close-btn');
    const confirmBtn = document.getElementById('modal-confirm');

    // Close modal
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    }

    // Confirm button
    confirmBtn.onclick = function() {
        modal.style.display = 'none';
    }

    // Close when clicking outside
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
}

// Show custom modal with Chinese messages
function showModal(icon, title, message, progress = null, queue = null) {
    const modal = document.getElementById('custom-modal');
    const modalIcon = document.getElementById('modal-icon');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const modalProgress = document.getElementById('modal-progress');
    const modalQueue = document.getElementById('modal-queue');

    // Set content
    modalIcon.innerHTML = icon;
    modalTitle.textContent = title;
    modalMessage.textContent = message;

    // Clear previous content
    modalProgress.innerHTML = '';
    modalQueue.innerHTML = '';

    // Add progress if provided
    if (progress) {
        const progressBar = document.createElement('div');
        progressBar.className = 'progress-bar';

        const progressFill = document.createElement('div');
        progressFill.className = 'progress-fill';
        progressFill.style.width = `${progress.percent}%`;

        const progressText = document.createElement('div');
        progressText.className = 'progress-text';
        progressText.textContent = progress.text;

        progressBar.appendChild(progressFill);
        progressBar.appendChild(progressText);
        modalProgress.appendChild(progressBar);
    }

    // Add queue info if provided
    if (queue) {
        const queueInfo = document.createElement('div');
        queueInfo.className = 'queue-info';
        queueInfo.innerHTML = `
            <div class="queue-icon"><i class="fas fa-users"></i></div>
            <div class="queue-text">${queue}</div>
        `;
        modalQueue.appendChild(queueInfo);
    }

    // Show modal
    modal.style.display = 'block';
}

// Access control functions
function recordAccess(type, success) {
    // Only record successful accesses
    if (!success) return;

    const now = Date.now();
    const data = {
        ip: visitorStats.ip,
        type: type,
        timestamp: now
    };

    // Send to server to record
    fetch('record-access.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });
}

function checkAccess(type, callback) {
    const now = Date.now();
    const accessType = accessData[type];

    // Check if banned
    if (accessType.banned_until > now) {
        const remaining = Math.ceil((accessType.banned_until - now) / 1000);
        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;

        showModal(
            '<i class="fas fa-ban"></i>',
            '访问受限',
            `系统检测到异常操作，该功能已临时禁用10分钟。`,
            {
                percent: (600 - remaining) / 600 * 100,
                text: `剩余时间: ${minutes}分 ${seconds}秒`
            }
        );
        return;
    }

    // Check rate limit
    if (now - accessType.last_click < accessControl[`${type}Rate`]) {
        showModal(
            '<i class="fas fa-hourglass-half"></i>',
            '操作过快',
            `请等待 ${accessControl[`${type}Rate`]/1000} 秒后再试`,
            {
                percent: 100,
                text: '请放慢操作速度'
            }
        );
        return;
    }

    // Check if queue is active
    if (accessData.queueActive) {
        // Calculate position in queue
        const position = accessData.queueCount + 1;
        const waitTime = position * accessControl.queueRate;

        showModal(
            '<i class="fas fa-door-open"></i>',
            '排队等待中',
            `当前访问人数较多，您的请求正在排队处理...`,
            null,
            `队列位置: ${position} | 预计等待: ${Math.ceil(waitTime/1000)}秒`
        );

        // Add to queue and process after delay
        setTimeout(() => {
            processQueuedAction(type, callback);
        }, waitTime);

        return;
    }

    // If all checks pass, execute callback
    callback();
}

function processQueuedAction(type, callback) {
    // Check ban status again after waiting
    const now = Date.now();
    const accessType = accessData[type];

    if (accessType.banned_until > now) {
        const remaining = Math.ceil((accessType.banned_until - now) / 1000);
        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;

        showModal(
            '<i class="fas fa-ban"></i>',
            '访问受限',
            `系统检测到异常操作，该功能已临时禁用10分钟。`,
            {
                percent: (600 - remaining) / 600 * 100,
                text: `剩余时间: ${minutes}分 ${seconds}秒`
            }
        );
        return;
    }

    // Execute the action
    callback();
}

// Initialize charts
function initCharts() {
    // Prepare data
    const stats = visitorStats.history;

    // Daily chart (last 7 days)
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    const dailyData = getLast7DaysData(stats);

    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyData.labels,
            datasets: [{
                label: '访问量',
                data: dailyData.values,
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                borderWidth: 3,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Monthly chart (last 6 months)
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyData = getLast6MonthsData(stats);

    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthlyData.labels,
            datasets: [{
                label: '访问量',
                data: monthlyData.values,
                backgroundColor: 'rgba(155, 89, 182, 0.7)',
                borderColor: 'rgba(155, 89, 182, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Yearly chart (last 3 years)
    const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
    const yearlyData = getLast3YearsData(stats);

    new Chart(yearlyCtx, {
        type: 'bar',
        data: {
            labels: yearlyData.labels,
            datasets: [{
                label: '访问量',
                data: yearlyData.values,
                backgroundColor: [
                    'rgba(52, 152, 219, 0.7)',
                    'rgba(46, 204, 113, 0.7)',
                    'rgba(155, 89, 182, 0.7)'
                ],
                borderColor: [
                    'rgba(52, 152, 219, 1)',
                    'rgba(46, 204, 113, 1)',
                    'rgba(155, 89, 182, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Helper functions for chart data
function getLast7DaysData(stats) {
    const today = new Date();
    const result = { labels: [], values: [] };

    for (let i = 6; i >= 0; i--) {
        const date = new Date();
        date.setDate(today.getDate() - i);
        const dateStr = formatDate(date);

        // Get year/month/day from dateStr
        const [year, month, day] = dateStr.split('-');

        // Get value from stats
        let value = 0;
        if (stats[year] && stats[year][month] && stats[year][month][dateStr]) {
            value = stats[year][month][dateStr];
        }

        // Format label (e.g., "周一 15")
        const label = date.toLocaleDateString('zh-CN', { weekday: 'short', day: 'numeric' });

        result.labels.push(label);
        result.values.push(value);
    }

    return result;
}

function getLast6MonthsData(stats) {
    const today = new Date();
    const result = { labels: [], values: [] };

    for (let i = 5; i >= 0; i--) {
        const date = new Date();
        date.setMonth(today.getMonth() - i);
        const monthStr = formatMonth(date);

        // Get year/month from monthStr
        const [year, month] = monthStr.split('-');

        // Get value from stats
        let value = 0;
        if (stats[year] && stats[year][monthStr]) {
            // Sum all days in the month
            Object.values(stats[year][monthStr]).forEach(dayValue => {
                value += dayValue;
            });
        }

        // Format label (e.g., "1月 2025")
        const label = date.toLocaleDateString('zh-CN', { month: 'short', year: 'numeric' });

        result.labels.push(label);
        result.values.push(value);
    }

    return result;
}

function getLast3YearsData(stats) {
    const today = new Date();
    const result = { labels: [], values: [] };

    for (let i = 2; i >= 0; i--) {
        const year = today.getFullYear() - i;
        const yearStr = String(year);

        // Get value from stats
        let value = 0;
        if (stats[yearStr]) {
            // Sum all months in the year
            Object.values(stats[yearStr]).forEach(month => {
                Object.values(month).forEach(dayValue => {
                    value += dayValue;
                });
            });
        }

        result.labels.push(yearStr + '年');
        result.values.push(value);
    }

    return result;
}

// Helper function to format date as YYYY-MM-DD
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Helper function to format month as YYYY-MM
function formatMonth(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    return `${year}-${month}`;
}

// Initialize card click handlers
function initCardClickHandlers() {
    const cardLinks = {
        'github': 'https://github.com',
        'blog': '#',
        'portfolio': '#',
        'contact': '#'
    };

    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', function(e) {
            e.stopPropagation();

            // Check access before proceeding
            checkAccess('card', () => {
                // Create ripple effect
                const ripple = document.createElement('div');
                ripple.className = 'card__ripple';
                const rect = card.getBoundingClientRect();
                ripple.style.left = `${rect.width}px`;
                ripple.style.top = `${rect.height}px`;
                this.appendChild(ripple);

                // Get link from configuration
                const link = cardLinks[this.id];
                if (link) {
                    setTimeout(() => {
                        window.location.href = link;
                    }, 400);
                }

                // Record successful access
                recordAccess('card', true);

                // Clean up ripple
                setTimeout(() => {
                    if (ripple.parentNode === this) {
                        this.removeChild(ripple);
                    }
                }, 400);
            });
        });
    });

    // Global click effect
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.card')) {
            const globalEffect = document.createElement('div');
            globalEffect.className = 'global-click-effect';
            globalEffect.style.left = `${e.clientX - 10}px`;
            globalEffect.style.top = `${e.clientY - 10}px`;
            document.body.appendChild(globalEffect);

            setTimeout(() => {
                if (globalEffect.parentNode === document.body) {
                    document.body.removeChild(globalEffect);
                }
            }, 600);
        }
    });
}

// Enhanced mouse trail effect
function initMouseTrail() {
    const canvas = document.getElementById('trailCanvas');
    if (!canvas) return;

    // Check if mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    if (isMobile) {
        canvas.style.display = 'none';
        return;
    }

    const ctx = canvas.getContext('2d');
    let mouseX = 0, mouseY = 0;

    // Increased spacing between points
    const points = Array(25).fill({x: 0, y: 0});

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    document.addEventListener('mousemove', e => {
        mouseX = e.clientX;
        mouseY = e.clientY;
    });

    function lerp(start, end, amt) {
        return (1 - amt) * start + amt * end;
    }

    function drawTrail() {
        points[0] = { x: mouseX, y: mouseY };

        // Increased spacing with adjusted lerp factor
        for (let i = 1; i < points.length; i++) {
            points[i] = {
                x: lerp(points[i].x, points[i-1].x, 0.15), // Reduced from 0.3
                y: lerp(points[i].y, points[i-1].y, 0.15)  // Reduced from 0.3
            };
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        points.forEach((point, index) => {
            const alpha = 1 - (index / points.length);
            const size = 10 * (1 - index/points.length); // Slightly larger

            // Rainbow gradient colors
            const hue = (index * 10) % 360;
            const color = `hsla(${hue}, 100%, 60%, ${alpha})`;

            ctx.beginPath();
            ctx.arc(point.x, point.y, size, 0, Math.PI * 2);
            ctx.fillStyle = color;
            ctx.fill();

            // Draw connecting lines for smoother trail
            if (index > 0) {
                ctx.beginPath();
                ctx.moveTo(points[index-1].x, points[index-1].y);
                ctx.lineTo(point.x, point.y);
                ctx.strokeStyle = color;
                ctx.lineWidth = size * 1.5;
                ctx.stroke();
            }
        });

        requestAnimationFrame(drawTrail);
    }

    drawTrail();
}