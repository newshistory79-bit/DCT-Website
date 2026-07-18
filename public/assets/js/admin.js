document.addEventListener('DOMContentLoaded', function () {
    initSidebarToggle();
    initTopbarDropdowns();
    initModal();
    initConfirmDialog();
    initToast();
    initFormLoadingState();
});

// ปุ่มเดียวกันมีสองพฤติกรรม: จอเล็ก (<=1024px) เลื่อนเมนูเข้า/ออกแบบ Overlay (เดิม)
// จอใหญ่ (>1024px) ย่อ Sidebar เหลือแค่ไอคอน (ใหม่) - แยกด้วย Viewport Width ตอนคลิก ไม่กระทบพฤติกรรมเดิมบนจอเล็ก
function initSidebarToggle() {
    var toggleBtn = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('adminSidebar');
    var content = document.querySelector('.admin-content');

    if (!toggleBtn || !sidebar) {
        return;
    }

    toggleBtn.addEventListener('click', function () {
        if (window.innerWidth > 1024) {
            sidebar.classList.toggle('collapsed');
            if (content) {
                content.classList.toggle('sidebar-collapsed');
            }
        } else {
            sidebar.classList.toggle('open');
        }
    });
}

// เมนูแบบ Dropdown บน Topbar (การแจ้งเตือน / ผู้ใช้) - เปิดทีละอันได้ 1 อัน ปิดเมื่อคลิกนอกกรอบ หรือกด Esc
function initTopbarDropdowns() {
    var dropdowns = [
        { toggle: document.getElementById('notifToggle'), menu: document.getElementById('notifMenu') },
        { toggle: document.getElementById('userToggle'), menu: document.getElementById('userMenuDropdown') }
    ];

    function closeAll() {
        dropdowns.forEach(function (item) {
            if (item.toggle && item.menu) {
                item.menu.hidden = true;
                item.toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    dropdowns.forEach(function (item) {
        if (!item.toggle || !item.menu) {
            return;
        }

        item.toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            var isOpen = !item.menu.hidden;
            closeAll();
            item.menu.hidden = isOpen;
            item.toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        });
    });

    document.addEventListener('click', closeAll);
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAll();
        }
    });
}

// Confirm ก่อน Submit ฟอร์มใดๆ ที่มี data-confirm (ใช้ร่วมกันได้ทุกโมดูล CRUD เช่นปุ่มลบ)
// *** Handler เดิมนี้ไม่ถูกแก้ไขแม้แต่บรรทัดเดียว (Stage DS1) — ฟอร์มที่ยังไม่ Retrofit ใช้ window.confirm() เหมือนเดิมทุกประการ ***
document.addEventListener('submit', function (event) {
    var form = event.target;

    if (form.hasAttribute && form.hasAttribute('data-confirm')) {
        var message = form.getAttribute('data-confirm');

        if (!window.confirm(message)) {
            event.preventDefault();
        }
    }
});

// ============================================================
// Design System v2 — Stage DS1 (Module ใหม่ ต่อท้ายไฟล์เดิม)
// เรียกทั้งหมดจาก DOMContentLoaded จุดเดียวด้านบน (ไม่มี Listener ที่สอง)
// ============================================================

// Modal ทั่วไป — เปิดจาก [data-modal-target="#id"], ปิดจาก [data-modal-close]/Esc/คลิกนอกกรอบ
// Focus Trap + Restore Focus (Pattern เดียวกับ Gallery Lightbox ฝั่ง Public Stage 2.4)
// ไม่ครอบคลุม .admin-confirm-dialog (มี initConfirmDialog() ของตัวเองแยกต่างหาก กันการทำงานซ้อนกัน)
function initModal() {
    var triggers = document.querySelectorAll('[data-modal-target]');
    var modals = document.querySelectorAll('.admin-modal:not(.admin-confirm-dialog)');

    if (!triggers.length && !modals.length) {
        return;
    }

    var lastFocusedElement = null;
    var activeModal = null;

    function openModal(modal, trigger) {
        activeModal = modal;
        lastFocusedElement = trigger || document.activeElement;
        modal.hidden = false;
        window.requestAnimationFrame(function () {
            modal.classList.add('active');
            var closeBtn = modal.querySelector('[data-modal-close]');
            if (closeBtn) {
                closeBtn.focus();
            }
        });
        document.addEventListener('keydown', onModalKeydown);
    }

    function closeModal() {
        if (!activeModal) {
            return;
        }
        var modal = activeModal;
        modal.classList.remove('active');
        document.removeEventListener('keydown', onModalKeydown);
        window.setTimeout(function () {
            modal.hidden = true;
        }, 200);
        if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
        activeModal = null;
    }

    function onModalKeydown(event) {
        if (event.key === 'Escape') {
            closeModal();
        } else if (event.key === 'Tab' && activeModal) {
            trapFocus(event, activeModal);
        }
    }

    function trapFocus(event, modal) {
        var focusable = modal.querySelectorAll('button:not(:disabled), a[href], input, select, textarea');

        if (!focusable.length) {
            return;
        }

        var first = focusable[0];
        var last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }

    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            var targetSelector = trigger.getAttribute('data-modal-target');
            var modal = targetSelector ? document.querySelector(targetSelector) : null;

            if (!modal || modal.classList.contains('admin-confirm-dialog')) {
                return;
            }

            event.preventDefault();
            openModal(modal, trigger);
        });
    });

    modals.forEach(function (modal) {
        var closeBtn = modal.querySelector('[data-modal-close]');

        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });
    });
}

// Confirm Dialog สวยงาม (Progressive Enhancement ของ data-confirm เดิมด้านบน) — ใช้กับฟอร์มที่มี data-confirm-modal เท่านั้น
// data-confirm เดิม (window.confirm()) ไม่ถูกแตะต้อง ยังทำงานเหมือนเดิมทุกประการสำหรับฟอร์มที่ยังไม่ Retrofit
function initConfirmDialog() {
    var dialog = document.getElementById('adminConfirmDialog');
    var forms = document.querySelectorAll('form[data-confirm-modal]');

    if (!dialog || !forms.length) {
        return;
    }

    var messageEl = dialog.querySelector('#adminConfirmMessage');
    var acceptBtn = dialog.querySelector('[data-confirm-accept]');
    var cancelBtn = dialog.querySelector('[data-confirm-cancel]');
    var pendingForm = null;
    var lastFocusedElement = null;

    function open(form, message) {
        pendingForm = form;
        lastFocusedElement = document.activeElement;
        messageEl.textContent = message;
        dialog.hidden = false;
        window.requestAnimationFrame(function () {
            dialog.classList.add('active');
            acceptBtn.focus();
        });
        document.addEventListener('keydown', onKeydown);
    }

    function close() {
        dialog.classList.remove('active');
        document.removeEventListener('keydown', onKeydown);
        window.setTimeout(function () {
            dialog.hidden = true;
        }, 200);
        if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
        pendingForm = null;
    }

    function onKeydown(event) {
        if (event.key === 'Escape') {
            close();
        }
    }

    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();
            open(form, form.getAttribute('data-confirm-modal') || 'ยืนยันการทำรายการ');
        });
    });

    acceptBtn.addEventListener('click', function () {
        var form = pendingForm;
        close();

        if (form) {
            form.dataset.confirmed = 'true';

            if (form.requestSubmit) {
                form.requestSubmit();
            } else {
                form.submit();
            }
        }
    });

    cancelBtn.addEventListener('click', close);

    dialog.addEventListener('click', function (event) {
        if (event.target === dialog) {
            close();
        }
    });
}

// เสริม .alert เดิม (Flash Message จาก $successMessage/$errorMessage) ให้มีปุ่มปิด + หายอัตโนมัติเฉพาะข้อความสำเร็จ
// Progressive Enhancement ล้วน — ถ้า JS ไม่ทำงาน .alert ยังแสดงข้อความแบบ Static ได้ปกติเหมือนเดิมทุกประการ
function initToast() {
    var alerts = document.querySelectorAll('.alert');

    alerts.forEach(function (alertEl) {
        var dismissBtn = document.createElement('button');
        dismissBtn.type = 'button';
        dismissBtn.className = 'alert-dismiss';
        dismissBtn.setAttribute('aria-label', 'ปิดข้อความแจ้งเตือน');
        dismissBtn.textContent = '×';
        alertEl.appendChild(dismissBtn);

        function dismiss() {
            alertEl.classList.add('is-dismissing');
            window.setTimeout(function () {
                alertEl.remove();
            }, 250);
        }

        dismissBtn.addEventListener('click', dismiss);

        if (alertEl.classList.contains('alert-success')) {
            window.setTimeout(dismiss, 6000);
        }
    });
}

// Loading State บนปุ่ม Submit กันการกดซ้ำระหว่างรอโหลดหน้าใหม่ (Progressive Enhancement)
// ตรวจ event.defaultPrevented เพื่อไม่ Disable ปุ่มเมื่อ Submit ถูกยกเลิกจาก data-confirm/data-confirm-modal ด้านบน
function initFormLoadingState() {
    document.addEventListener('submit', function (event) {
        if (event.defaultPrevented) {
            return;
        }

        var form = event.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        var submitBtn = form.querySelector('button[type="submit"]');

        if (submitBtn && !submitBtn.disabled) {
            window.setTimeout(function () {
                submitBtn.disabled = true;
                submitBtn.classList.add('is-loading');
            }, 0);
        }
    });
}
