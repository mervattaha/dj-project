// main.js
console.log("Valid code snippet");

document.addEventListener('DOMContentLoaded', function() {
    const myButton = document.getElementById('myButton');
    if (myButton) {
        myButton.addEventListener('click', function() {
            alert('Button clicked!');
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const bookNowButton = document.querySelector('.btn-book-now');
    if (bookNowButton) {
        bookNowButton.addEventListener('click', function() {
            alert('Thank you for booking!');
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
});


// مثال على التحقق من صحة النموذج
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookingForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const date = document.getElementById('date').value;
            
            if (!name || !email || !date) {
                alert('Please fill out all required fields.');
                event.preventDefault(); // منع إرسال النموذج
            }
        });
    }
});

// مثال على التفاعل مع القائمة المنسدلة للغات
document.addEventListener('DOMContentLoaded', function() {
    const langDropdown = document.getElementById('languageDropdown');
    if (langDropdown) {
        langDropdown.addEventListener('change', function(event) {
            const selectedLang = event.target.value;
            window.location.href = `?lang=${selectedLang}`;
        });
    }
});

