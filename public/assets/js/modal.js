// modal.js

// مثال على فتح وإغلاق نافذة منبثقة
document.addEventListener('DOMContentLoaded', function() {
    const modalTrigger = document.querySelector('[data-toggle="modal"]');
    const modal = document.querySelector('.modal');
    const closeButton = document.querySelector('.modal .close');

    if (modalTrigger && modal && closeButton) {
        modalTrigger.addEventListener('click', function() {
            modal.classList.add('show');
        });

        closeButton.addEventListener('click', function() {
            modal.classList.remove('show');
        });
    }
});
