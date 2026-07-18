document.addEventListener('DOMContentLoaded', function () {
    initSidebarToggle();
    initTopbarDropdowns();
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
document.addEventListener('submit', function (event) {
    var form = event.target;

    if (form.hasAttribute && form.hasAttribute('data-confirm')) {
        var message = form.getAttribute('data-confirm');

        if (!window.confirm(message)) {
            event.preventDefault();
        }
    }
});
