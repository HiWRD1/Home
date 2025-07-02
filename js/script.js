document.addEventListener('DOMContentLoaded', function() {
    const cardLinks = {
        'a': 'https://Baidu.com',
        // ......
    };
    function initTypewriter() {
        const element = document.querySelector('.typewriter');
        const text = 'Hi！欢迎来到我的网站！';//替换成你想要的文本
        let index = 0;

        element.innerHTML = '';

        function type() {
            if (index < text.length) {
                element.innerHTML += text.charAt(index);
                index++;
                setTimeout(type, 100);
            }
        }
        type();
    }
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', function(e) {
            e.stopPropagation();
            const ripple = document.createElement('div');
            ripple.className = 'card__ripple';
            const rect = card.getBoundingClientRect();
            ripple.style.left = `${rect.width}px`;
            ripple.style.top = `${rect.height}px`;
            this.appendChild(ripple);
            const link = cardLinks[this.id];
            if (link) {
                setTimeout(() => {
                    window.location.href = link;
                }, 400);
            } else {
                console.error('未找到卡片链接:', this.id);
            }

            // 清理波纹
            setTimeout(() => {
                if (ripple.parentNode === this) {
                    this.removeChild(ripple);
                }
            }, 400);
        });
    });
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
    const canvas = document.getElementById('trailCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let mouseX = 0, mouseY = 0;
        let points = Array(20).fill({x: 0, y: 0});

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

            for (let i = 1; i < points.length; i++) {
                points[i] = {
                    x: lerp(points[i].x, points[i-1].x, 0.3),
                    y: lerp(points[i].y, points[i-1].y, 0.3)
                };
            }

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            points.forEach((point, index) => {
                const alpha = 1 - (index / points.length);
                const size = 8 * (1 - index/points.length);

                ctx.beginPath();
                ctx.arc(point.x, point.y, size, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(52, 152, 219, ${alpha})`;
                ctx.fill();
            });

            requestAnimationFrame(drawTrail);
        }
        drawTrail();
    }
    initTypewriter();
});