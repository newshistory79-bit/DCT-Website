document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('adminSidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }
});

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
