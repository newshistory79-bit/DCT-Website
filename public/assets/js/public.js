document.addEventListener('DOMContentLoaded', function () {
    initNavigation();
    initLightbox();
});

// ============================================================
// Navigation (Nav Toggle / Hero Slider / Back Links)
// ============================================================
function initNavigation() {
    initNavToggle();
    initHeroSlider();
    initBackLinks();
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

    var slides = slider.querySelectorAll('.hero-slide');
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
// Gallery Lightbox (Stage 2.4) - Fullscreen, Prev/Next, Keyboard,
// Touch Swipe, Focus Trap, Restore Focus - Vanilla JS ล้วน
// ============================================================
function initLightbox() {
    var triggers = document.querySelectorAll('[data-lightbox-image]');

    if (!triggers.length) {
        return;
    }

    var lightbox = document.getElementById('galleryLightbox');

    if (!lightbox) {
        return;
    }

    var items = Array.prototype.map.call(triggers, function (el) {
        return {
            image: el.getAttribute('data-lightbox-image'),
            title: el.getAttribute('data-lightbox-title') || '',
            description: el.getAttribute('data-lightbox-description') || ''
        };
    });

    var imageEl        = lightbox.querySelector('.lightbox-image');
    var titleEl        = lightbox.querySelector('.lightbox-title');
    var descriptionEl  = lightbox.querySelector('.lightbox-description');
    var counterEl      = lightbox.querySelector('.lightbox-counter');
    var closeBtn       = lightbox.querySelector('.lightbox-close');
    var prevBtn        = lightbox.querySelector('.lightbox-prev');
    var nextBtn        = lightbox.querySelector('.lightbox-next');

    var currentIndex       = 0;
    var lastFocusedElement = null;
    var touchStartX        = null;

    function render() {
        var item = items[currentIndex];

        imageEl.src = item.image;
        imageEl.alt = item.title;
        titleEl.textContent = item.title;
        descriptionEl.textContent = item.description;
        descriptionEl.hidden = item.description === '';
        counterEl.textContent = (currentIndex + 1) + ' / ' + items.length;
        prevBtn.disabled = items.length <= 1;
        nextBtn.disabled = items.length <= 1;
    }

    function openAt(index) {
        currentIndex = index;
        lastFocusedElement = document.activeElement;

        render();

        lightbox.hidden = false;
        document.body.classList.add('lightbox-open');
        document.addEventListener('keydown', onKeydown);

        // เปิด Class .active ในเฟรมถัดไปเพื่อให้ CSS Transition (Fade In) ทำงาน
        // ต้องโฟกัสปุ่มปิด "หลัง" จากนั้นเท่านั้น เพราะก่อนมี .active องค์ประกอบยังเป็น visibility:hidden
        // (Browser จะเพิกเฉยต่อ .focus() บน Element ที่มองไม่เห็นอยู่แบบเงียบๆ)
        window.requestAnimationFrame(function () {
            lightbox.classList.add('active');
            closeBtn.focus();
        });
    }

    function goTo(index) {
        currentIndex = (index + items.length) % items.length;
        render();
    }

    function close() {
        lightbox.classList.remove('active');
        document.body.classList.remove('lightbox-open');
        document.removeEventListener('keydown', onKeydown);

        window.setTimeout(function () {
            lightbox.hidden = true;
            imageEl.src = '';
        }, 250);

        if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
    }

    function onKeydown(event) {
        if (event.key === 'Escape') {
            close();
        } else if (event.key === 'ArrowLeft') {
            goTo(currentIndex - 1);
        } else if (event.key === 'ArrowRight') {
            goTo(currentIndex + 1);
        } else if (event.key === 'Tab') {
            trapFocus(event);
        }
    }

    // Focus Trap - วนโฟกัสอยู่ในปุ่มของ Lightbox เท่านั้นขณะเปิด (WCAG Keyboard Trap ที่ถูกต้อง)
    function trapFocus(event) {
        var focusable = lightbox.querySelectorAll('button:not(:disabled)');

        if (!focusable.length) {
            return;
        }

        var first = focusable[0];
        var last  = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }

    triggers.forEach(function (trigger, index) {
        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            openAt(index);
        });
    });

    closeBtn.addEventListener('click', close);
    prevBtn.addEventListener('click', function () { goTo(currentIndex - 1); });
    nextBtn.addEventListener('click', function () { goTo(currentIndex + 1); });

    // คลิกพื้นหลังนอกกรอบภาพเพื่อปิด (ไม่ปิดเมื่อคลิกที่ตัวภาพ/Caption)
    lightbox.addEventListener('click', function (event) {
        if (event.target === lightbox) {
            close();
        }
    });

    // Touch Swipe ซ้าย/ขวาเพื่อเปลี่ยนภาพ
    lightbox.addEventListener('touchstart', function (event) {
        touchStartX = event.touches[0].clientX;
    }, { passive: true });

    lightbox.addEventListener('touchend', function (event) {
        if (touchStartX === null) {
            return;
        }

        var deltaX = event.changedTouches[0].clientX - touchStartX;

        if (Math.abs(deltaX) > 40) {
            if (deltaX > 0) {
                goTo(currentIndex - 1);
            } else {
                goTo(currentIndex + 1);
            }
        }

        touchStartX = null;
    }, { passive: true });
}
