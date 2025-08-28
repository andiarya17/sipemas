// Toggle sidebar on mobile
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('show');
}

// Close sidebar when clicking outside
document.addEventListener('click', function(e) {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    
    if (sidebar && !sidebar.contains(e.target) && !toggleBtn?.contains(e.target)) {
        sidebar.classList.remove('show');
    }
});

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Show loading button state
function showLoading(buttonId) {
    const button = document.getElementById(buttonId);
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="loading"></span> Memproses...';
    button.disabled = true;
    
    // Restore button after 3 seconds (adjust as needed)
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    }, 3000);
}

// Auto hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
});

// Image preview for file uploads
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Confirm delete actions
function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
    return confirm(message);
}

// Format phone number input
function formatPhone(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.startsWith('0')) {
        value = '62' + value.substring(1);
    }
    input.value = value;
}

// Character counter for textarea
function updateCharCount(textarea, counterId, maxLength) {
    const counter = document.getElementById(counterId);
    const remaining = maxLength - textarea.value.length;
    counter.textContent = `${remaining} karakter tersisa`;
    
    if (remaining < 50) {
        counter.style.color = '#dc3545';
    } else {
        counter.style.color = '#6c757d';
    }
}

// Smooth scroll to top
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Initialize tooltips and popovers (if using Bootstrap)
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});