// Main JavaScript for Melody Masters

document.addEventListener('DOMContentLoaded', function () {
    // View Order Details Toggle (Admin)
    const viewOrderButtons = document.querySelectorAll('.view-order');

    viewOrderButtons.forEach(button => {
        button.addEventListener('click', function () {
            const orderId = this.getAttribute('data-order-id');
            const detailsRow = document.getElementById('order-' + orderId);

            if (detailsRow) {
                if (detailsRow.style.display === 'none' || detailsRow.style.display === '') {
                    detailsRow.style.display = 'table-row';
                    this.textContent = 'Hide';
                } else {
                    detailsRow.style.display = 'none';
                    this.textContent = 'View';
                }
            }
        });
    });

    // Form Validation
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'red';
                } else {
                    field.style.borderColor = '';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');

    anchorLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });

    // Image placeholder fallback
    const images = document.querySelectorAll('img');

    images.forEach(img => {
        img.addEventListener('error', function () {
            this.src = SITE_URL + '/assets/images/placeholder.jpg';
        });
    });

    // Quantity input validation
    const quantityInputs = document.querySelectorAll('.quantity-input, input[type="number"][name="quantity"]');

    quantityInputs.forEach(input => {
        input.addEventListener('change', function () {
            const min = parseInt(this.getAttribute('min')) || 1;
            const max = parseInt(this.getAttribute('max')) || 999;
            let value = parseInt(this.value);

            if (value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
            }
        });
    });

    // Confirm before delete actions
    const deleteButtons = document.querySelectorAll('[name="delete_product"], [name="delete_user"]');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // Mobile menu toggle logic
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');

    // Create backdrop overlay if it doesn't exist
    let menuBackdrop = document.querySelector('.menu-backdrop');
    if (!menuBackdrop) {
        menuBackdrop = document.createElement('div');
        menuBackdrop.className = 'menu-backdrop';
        document.body.appendChild(menuBackdrop);
    }

    function toggleMenu(show) {
        if (!mainNav || !menuToggle) return;

        const isActive = show !== undefined ? show : !mainNav.classList.contains('active');
        const icon = menuToggle.querySelector('i');

        if (isActive) {
            menuToggle.classList.add('active');
            mainNav.classList.add('active');
            menuBackdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
            if (icon) {
                icon.setAttribute('class', 'fas fa-times');
            }
        } else {
            menuToggle.classList.remove('active');
            mainNav.classList.remove('active');
            menuBackdrop.classList.remove('active');
            document.body.style.overflow = '';
            if (icon) {
                icon.setAttribute('class', 'fas fa-bars');
            }
        }
    }

    if (menuToggle && mainNav) {
        const newToggle = menuToggle.cloneNode(true);
        menuToggle.parentNode.replaceChild(newToggle, menuToggle);

        newToggle.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            toggleMenu();
        };

        const menuToggleRef = newToggle;

        document.onclick = (e) => {
            if (mainNav.classList.contains('active') && !mainNav.contains(e.target) && !menuToggleRef.contains(e.target)) {
                toggleMenu(false);
            }
        };

        menuBackdrop.onclick = () => toggleMenu(false);

        mainNav.querySelectorAll('a').forEach(link => {
            link.onclick = () => toggleMenu(false);
        });
    }

    // Price formatting
    const priceElements = document.querySelectorAll('.price, .product-price');
    priceElements.forEach(element => {
        const text = element.textContent;
        if (!text.includes('£') && !isNaN(parseFloat(text))) {
            element.textContent = '£' + parseFloat(text).toFixed(2);
        }
    });

    // Search functionality enhancement
    const searchForms = document.querySelectorAll('.search-form');
    searchForms.forEach(form => {
        const input = form.querySelector('input[type="text"]');
        if (input) {
            input.addEventListener('input', function () {
                if (this.value.length > 0) {
                    form.querySelector('button').style.background = '#2ecc71';
                } else {
                    form.querySelector('button').style.background = '';
                }
            });
        }
    });

    // Add loading state to forms on submit
    const allForms = document.querySelectorAll('form');
    allForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const button = this.querySelector('button[type="submit"]');

            // Only show loading if the button doesn't have a name 
            // OR use a timeout to disable it AFTER the form starts submitting
            if (button && !button.disabled) {
                const buttonName = button.getAttribute('name');
                const buttonValue = button.getAttribute('value') || '1';

                if (buttonName) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = buttonName;
                    hiddenInput.value = buttonValue;
                    form.appendChild(hiddenInput);
                }

                // Preserve original width to prevent layout shift ("beating")
                const originalWidth = button.offsetWidth;
                button.style.width = originalWidth + 'px';
                button.disabled = true;

                // Use a non-disruptive spinner that keeps the button's footprint
                button.innerHTML = '<i class="fas fa-spinner fa-spin" style="color: var(--bg-dark);"></i> Wait...';
            }
        });
    });

    // Hero Slider Logic
    const slides = document.querySelectorAll('.hero-slide');
    if (slides.length > 0) {
        let currentSlide = 0;
        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 5000);
    }
});

// Admin Image Preview
function updateImagePreview(url) {
    const preview = document.getElementById('image-preview');
    if (preview) {
        preview.src = SITE_URL + '/' + (url.trim() || 'assets/images/placeholder.jpg');
    }
}

function setImageUrl(url) {
    const input = document.getElementById('image');
    if (input) {
        input.value = url;
        updateImagePreview(url);
    }
}
