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

    // Mobile menu toggle (if needed)
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');

    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function () {
            mainNav.classList.toggle('active');
        });
    }

    // Price formatting
    const priceElements = document.querySelectorAll('.price, .product-price');

    priceElements.forEach(element => {
        const text = element.textContent;
        if (!text.includes('₱') && !isNaN(parseFloat(text))) {
            element.textContent = '₱' + parseFloat(text).toFixed(2);
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
    forms.forEach(form => {
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
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wait...';
            }
        });
    });
});

// Helper functions
function showAlert(message, type = 'success') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;

    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alert, container.firstChild);

        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    }
}

function formatPrice(price) {
    return '₱' + parseFloat(price).toFixed(2);
}

// Console welcome message
console.log('%cWelcome to Melody Masters! 🎵', 'color: #3498db; font-size: 20px; font-weight: bold;');
console.log('%cYour premier music instrument shop', 'color: #2ecc71; font-size: 14px;');
