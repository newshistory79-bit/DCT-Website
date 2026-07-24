document.addEventListener('DOMContentLoaded', function () {
    initNavigation();
});

// ============================================================
// Navigation (Nav Toggle / Hero Slider / Back Links)
// ============================================================
function initNavigation() {
    initNavToggle();
    initHeroSlider();
    initBackLinks();
    initDocumentFilter();
}

// ปุ่ม "กลับหน้าก่อนหน้า" (เช่นหน้า 404) - ใช้ History กลับไปหน้าก่อน ถ้าไม่มีประวัติให้ไปหน้าแรกแทน
function initBackLinks() {
    document.querySelectorAll('.js-back-link').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = link.getAttribute('data-fallback') || '/';
            }
        });
    });
}

// เปิด/ปิดเมนูหลักบนจอ Tablet/Mobile
function initNavToggle() {
    var toggleBtn = document.getElementById('navToggle');
    var nav = document.getElementById('mainNav');

    if (!toggleBtn || !nav) {
        return;
    }

    toggleBtn.addEventListener('click', function () {
        var isOpen = nav.classList.toggle('open');
        toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', function (event) {
        if (!nav.classList.contains('open')) {
            return;
        }

        if (!nav.contains(event.target) && !toggleBtn.contains(event.target)) {
            nav.classList.remove('open');
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
    });
}

// Hero Slider แบบ Vanilla JS ล้วน (ไม่ใช้ Library ภายนอก) - เปลี่ยนภาพอัตโนมัติทุก 5 วินาที
function initHeroSlider() {
    var slider = document.getElementById('heroSlider');

    if (!slider) {
        return;
    }

    var slides = slider.querySelectorAll('.hero-slide-bg');
    var dots = slider.querySelectorAll('.hero-dot');

    if (slides.length <= 1) {
        return;
    }

    var current = 0;
    var intervalId = null;

    function show(index) {
        slides[current].classList.remove('active');
        dots[current] && dots[current].classList.remove('active');

        current = (index + slides.length) % slides.length;

        slides[current].classList.add('active');
        dots[current] && dots[current].classList.add('active');
    }

    function start() {
        intervalId = window.setInterval(function () {
            show(current + 1);
        }, 5000);
    }

    function stop() {
        if (intervalId !== null) {
            window.clearInterval(intervalId);
            intervalId = null;
        }
    }

    dots.forEach(function (dot, index) {
        dot.addEventListener('click', function () {
            stop();
            show(index);
            start();
        });
    });

    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);

    start();
}

// ============================================================
// Public Documents — Filter ตามประเภทเอกสาร (Client-side ล้วน กรองเฉพาะการ์ดที่ Render อยู่ในหน้าปัจจุบัน)
// ============================================================
function initDocumentFilter() {
    var container    = document.querySelector('.filter-buttons');
    var grid          = document.getElementById('documentGrid');
    var emptyMessage  = document.getElementById('documentFilterEmpty');

    if (!container || !grid) {
        return;
    }

    var buttons = container.querySelectorAll('.filter-button');
    var cards   = grid.querySelectorAll('[data-category]');

    function countFor(filter) {
        if (filter === 'all') {
            return cards.length;
        }

        var total = 0;

        cards.forEach(function (card) {
            if (card.getAttribute('data-category') === filter) {
                total += 1;
            }
        });

        return total;
    }

    // นับจำนวนการ์ดต่อหมวดจากข้อมูลที่ Render อยู่จริงในหน้านี้ (ไม่ยิง Query ใหม่)
    buttons.forEach(function (button) {
        var countEl = button.querySelector('.filter-count');

        if (countEl) {
            countEl.textContent = String(countFor(button.getAttribute('data-filter')));
        }
    });

    cards.forEach(function (card) {
        card.addEventListener('transitionend', function (event) {
            // ใช้ style.display แทน .hidden — .card กำหนด display:flex ไว้อยู่แล้วใน CSS
            // ซึ่งมี Priority สูงกว่า [hidden] ของ User-Agent Stylesheet เสมอ ทำให้ .hidden ไม่มีผลจริง
            if (event.propertyName === 'opacity' && card.classList.contains('doc-card-fade-out')) {
                card.style.display = 'none';
            }
        });
    });

    function applyFilter(filter) {
        var visibleCount = 0;

        cards.forEach(function (card) {
            var matches = filter === 'all' || card.getAttribute('data-category') === filter;

            if (matches) {
                card.style.display = '';
                // ต้องรอ 1 Frame ก่อนเอา Class ออก ไม่งั้น Browser จะไม่เห็นการเปลี่ยนจาก opacity:0 -> 1 เป็น Transition (Reflow ก่อนเสมอ)
                window.requestAnimationFrame(function () {
                    card.classList.remove('doc-card-fade-out');
                });
                visibleCount += 1;
            } else {
                card.classList.add('doc-card-fade-out');
            }
        });

        if (emptyMessage) {
            emptyMessage.hidden = visibleCount > 0;
        }
    }

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            buttons.forEach(function (btn) {
                btn.classList.remove('active');
            });
            button.classList.add('active');
            applyFilter(button.getAttribute('data-filter'));
        });
    });
}
