/**
 * Hero Effects: Particles + Entrance Animations
 *
 * @package FLAW_Gaming
 */
(function () {
    'use strict';

    /* ═══════════════════════════════════════════════════════════
       1. ENTRANCE ANIMATIONS  (staggered fade-in / slide-up)
       ═══════════════════════════════════════════════════════════ */
    var animEls = document.querySelectorAll('.hero-anim');
    animEls.forEach(function (el) {
        var delay = parseInt(el.dataset.animDelay, 10) || 0;
        setTimeout(function () {
            el.classList.add('is-visible');
        }, delay + 100);          // +100 gives the page a beat to paint
    });

    /* ═══════════════════════════════════════════════════════════
       2. GLITCH EFFECT  (periodic glitch on .hero-glitch)
       ═══════════════════════════════════════════════════════════ */
    var glitchEl = document.querySelector('.hero-glitch');
    if (glitchEl) {
        // Fire a glitch burst every 4-8 s
        function triggerGlitch() {
            glitchEl.classList.add('glitching');
            setTimeout(function () {
                glitchEl.classList.remove('glitching');
            }, 200);
            setTimeout(triggerGlitch, 4000 + Math.random() * 4000);
        }
        setTimeout(triggerGlitch, 2500);   // first glitch after entrance anim
    }

    /* ═══════════════════════════════════════════════════════════
       3. CANVAS PARTICLE SYSTEM
       ═══════════════════════════════════════════════════════════ */
    var canvas = document.getElementById('hero-particles');
    if (!canvas) return;

    var ctx = canvas.getContext('2d');
    var particles = [];
    var mouse = { x: -9999, y: -9999 };
    var dpr = window.devicePixelRatio || 1;
    var w, h;

    // Reduce particle count on mobile
    var isMobile = window.innerWidth < 768;
    var PARTICLE_COUNT = isMobile ? 25 : 50;
    var CONNECT_DIST = isMobile ? 100 : 150;

    /* ── shapes drawn at (0,0) facing up, size ≈ 1 ── */
    var shapes = {
        // Crosshair
        crosshair: function (ctx, s) {
            ctx.beginPath();
            ctx.moveTo(0, -s);  ctx.lineTo(0, s);
            ctx.moveTo(-s, 0); ctx.lineTo(s, 0);
            ctx.stroke();
            ctx.beginPath();
            ctx.arc(0, 0, s * 0.6, 0, Math.PI * 2);
            ctx.stroke();
        },
        // Diamond
        diamond: function (ctx, s) {
            ctx.beginPath();
            ctx.moveTo(0, -s);
            ctx.lineTo(s * 0.6, 0);
            ctx.lineTo(0, s);
            ctx.lineTo(-s * 0.6, 0);
            ctx.closePath();
            ctx.stroke();
        },
        // Triangle
        triangle: function (ctx, s) {
            ctx.beginPath();
            ctx.moveTo(0, -s);
            ctx.lineTo(s * 0.87, s * 0.5);
            ctx.lineTo(-s * 0.87, s * 0.5);
            ctx.closePath();
            ctx.stroke();
        },
        // Small square
        square: function (ctx, s) {
            ctx.strokeRect(-s * 0.5, -s * 0.5, s, s);
        },
        // Circle / dot
        dot: function (ctx, s) {
            ctx.beginPath();
            ctx.arc(0, 0, s * 0.4, 0, Math.PI * 2);
            ctx.fill();
        },
        // Plus
        plus: function (ctx, s) {
            var t = s * 0.2;
            ctx.beginPath();
            ctx.moveTo(-t, -s); ctx.lineTo(t, -s);
            ctx.lineTo(t, -t);  ctx.lineTo(s, -t);
            ctx.lineTo(s, t);   ctx.lineTo(t, t);
            ctx.lineTo(t, s);   ctx.lineTo(-t, s);
            ctx.lineTo(-t, t);  ctx.lineTo(-s, t);
            ctx.lineTo(-s, -t); ctx.lineTo(-t, -t);
            ctx.closePath();
            ctx.stroke();
        }
    };

    var shapeKeys = Object.keys(shapes);

    /* ── colours that match the FLAW palette ── */
    var colours = [
        'rgba(212, 168, 67, 0.35)',  // primary gold
        'rgba(201, 40, 45, 0.3)',    // crimson accent
        'rgba(255, 255, 255, 0.15)', // white muted
        'rgba(212, 168, 67, 0.2)',
        'rgba(201, 40, 45, 0.15)',
    ];

    function resize() {
        var rect = canvas.parentElement.getBoundingClientRect();
        w = rect.width;
        h = rect.height;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        canvas.style.width = w + 'px';
        canvas.style.height = h + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    function createParticle() {
        var size = 4 + Math.random() * 8;
        return {
            x: Math.random() * w,
            y: Math.random() * h,
            vx: (Math.random() - 0.5) * 0.4,
            vy: (Math.random() - 0.5) * 0.4,
            size: size,
            shape: shapeKeys[Math.floor(Math.random() * shapeKeys.length)],
            color: colours[Math.floor(Math.random() * colours.length)],
            rotation: Math.random() * Math.PI * 2,
            rotSpeed: (Math.random() - 0.5) * 0.01,
            alpha: 0.3 + Math.random() * 0.5,
            pulseOffset: Math.random() * Math.PI * 2,
        };
    }

    function init() {
        resize();
        particles = [];
        for (var i = 0; i < PARTICLE_COUNT; i++) {
            particles.push(createParticle());
        }
    }

    function draw() {
        ctx.clearRect(0, 0, w, h);

        var time = Date.now() * 0.001;

        // Draw connecting lines between nearby particles
        for (var i = 0; i < particles.length; i++) {
            for (var j = i + 1; j < particles.length; j++) {
                var dx = particles[i].x - particles[j].x;
                var dy = particles[i].y - particles[j].y;
                var dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < CONNECT_DIST) {
                    var opacity = (1 - dist / CONNECT_DIST) * 0.08;
                    ctx.strokeStyle = 'rgba(212, 168, 67, ' + opacity + ')';
                    ctx.lineWidth = 0.5;
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                }
            }
        }

        // Draw & update particles
        for (var k = 0; k < particles.length; k++) {
            var p = particles[k];

            // Pulsing alpha
            var pulse = Math.sin(time * 1.5 + p.pulseOffset) * 0.15;
            var alpha = Math.max(0.1, p.alpha + pulse);

            // Mouse interaction – gentle push
            var mdx = p.x - mouse.x;
            var mdy = p.y - mouse.y;
            var mDist = Math.sqrt(mdx * mdx + mdy * mdy);
            if (mDist < 120) {
                var force = (1 - mDist / 120) * 0.6;
                p.vx += (mdx / mDist) * force;
                p.vy += (mdy / mDist) * force;
            }

            // Friction
            p.vx *= 0.99;
            p.vy *= 0.99;

            // Move
            p.x += p.vx;
            p.y += p.vy;
            p.rotation += p.rotSpeed;

            // Wrap around edges
            if (p.x < -20) p.x = w + 20;
            if (p.x > w + 20) p.x = -20;
            if (p.y < -20) p.y = h + 20;
            if (p.y > h + 20) p.y = -20;

            // Draw
            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate(p.rotation);
            ctx.globalAlpha = alpha;
            ctx.strokeStyle = p.color;
            ctx.fillStyle = p.color;
            ctx.lineWidth = 1;
            shapes[p.shape](ctx, p.size);
            ctx.restore();
        }

        requestAnimationFrame(draw);
    }

    // Mouse tracking (throttled)
    var heroSection = canvas.parentElement;
    heroSection.addEventListener('mousemove', function (e) {
        var rect = heroSection.getBoundingClientRect();
        mouse.x = e.clientX - rect.left;
        mouse.y = e.clientY - rect.top;
    });
    heroSection.addEventListener('mouseleave', function () {
        mouse.x = -9999;
        mouse.y = -9999;
    });

    // Debounced resize
    var resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            isMobile = window.innerWidth < 768;
            PARTICLE_COUNT = isMobile ? 25 : 50;
            CONNECT_DIST = isMobile ? 100 : 150;
            init();
        }, 200);
    });

    // Respect prefers-reduced-motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        canvas.style.display = 'none';
        return;
    }

    init();
    draw();
})();
