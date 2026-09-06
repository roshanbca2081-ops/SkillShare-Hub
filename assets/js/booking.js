// ============================================
// Booking JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Booking form validation
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            if (!validateBookingForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Date/time picker validation
    const dateInput = document.getElementById('bookingDate');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            validateDateTime();
        });
    }
    
    // Cancel booking
    document.querySelectorAll('.cancel-booking').forEach(el => {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to cancel this booking?')) {
                const bookingId = this.dataset.bookingId;
                cancelBooking(bookingId);
            }
        });
    });
});

// ============================================
// Validate Booking Form
// ============================================

function validateBookingForm() {
    const date = document.getElementById('bookingDate');
    const time = document.getElementById('bookingTime');
    const notes = document.getElementById('notes');
    let valid = true;
    
    // Reset validation
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    
    // Validate date
    if (date && !date.value) {
        date.classList.add('is-invalid');
        valid = false;
    }
    
    // Validate time
    if (time && !time.value) {
        time.classList.add('is-invalid');
        valid = false;
    }
    
    // Validate notes (optional but min length if provided)
    if (notes && notes.value && notes.value.length < 10) {
        notes.classList.add('is-invalid');
        valid = false;
        showNotification('Please provide more details (min 10 characters)', 'warning');
    }
    
    return valid;
}

// ============================================
// Validate Date/Time
// ============================================

function validateDateTime() {
    const dateInput = document.getElementById('bookingDate');
    const timeInput = document.getElementById('bookingTime');
    const messageEl = document.getElementById('datetimeMessage');
    
    if (!dateInput || !timeInput || !messageEl) return;
    
    const date = new Date(dateInput.value + 'T' + timeInput.value);
    const now = new Date();
    
    if (date < now) {
        messageEl.textContent = 'Please select a future date and time';
        messageEl.className = 'text-danger small';
        return false;
    }
    
    messageEl.textContent = '';
    return true;
}

// ============================================
// Cancel Booking
// ============================================

function cancelBooking(bookingId) {
    ajaxRequest('api/cancel-booking.php', 'POST', { booking_id: bookingId })
        .then(data => {
            if (data.success) {
                showNotification('Booking cancelled successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message || 'Failed to cancel booking', 'error');
            }
        })
        .catch(() => {
            showNotification('An error occurred. Please try again.', 'error');
        });
}

// ============================================
// Reschedule Booking
// ============================================

function rescheduleBooking(bookingId) {
    const modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
    modal.show();
    
    document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const date = document.getElementById('newDate').value;
        const time = document.getElementById('newTime').value;
        
        ajaxRequest('api/reschedule-booking.php', 'POST', {
            booking_id: bookingId,
            date: date,
            time: time
        })
        .then(data => {
            if (data.success) {
                showNotification('Booking rescheduled successfully', 'success');
                modal.hide();
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message || 'Failed to reschedule', 'error');
            }
        })
        .catch(() => {
            showNotification('An error occurred. Please try again.', 'error');
        });
    });
}

// ============================================
// Rate Session
// ============================================

function rateSession(bookingId) {
    const modal = new bootstrap.Modal(document.getElementById('ratingModal'));
    modal.show();
    
    let selectedRating = 0;
    
    document.querySelectorAll('.rating-star').forEach(star => {
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.dataset.rating);
            updateRatingStars(selectedRating);
        });
        
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            updateRatingStars(rating, true);
        });
        
        star.addEventListener('mouseleave', function() {
            updateRatingStars(selectedRating);
        });
    });
    
    document.getElementById('submitRating').addEventListener('click', function() {
        if (selectedRating === 0) {
            showNotification('Please select a rating', 'warning');
            return;
        }
        
        const review = document.getElementById('reviewText').value;
        
        ajaxRequest('api/rate-session.php', 'POST', {
            booking_id: bookingId,
            rating: selectedRating,
            review: review
        })
        .then(data => {
            if (data.success) {
                showNotification('Thank you for your feedback!', 'success');
                modal.hide();
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message || 'Failed to submit rating', 'error');
            }
        })
        .catch(() => {
            showNotification('An error occurred. Please try again.', 'error');
        });
    });
}

function updateRatingStars(rating, hover = false) {
    document.querySelectorAll('.rating-star').forEach(star => {
        const starRating = parseInt(star.dataset.rating);
        if (starRating <= rating) {
            star.innerHTML = '<i class="fas fa-star"></i>';
            star.style.color = hover ? '#ed8936' : '#f6ad55';
        } else {
            star.innerHTML = '<i class="far fa-star"></i>';
            star.style.color = '#cbd5e0';
        }
    });
}