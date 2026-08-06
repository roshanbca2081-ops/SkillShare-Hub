/* =============================================
   SkillShare Hub - Booking / Session Scheduling JS
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    // Date picker min date = today
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(function (input) {
        const today = new Date().toISOString().split('T')[0];
        input.setAttribute('min', today);
    });

    // Time slot selection
    document.querySelectorAll('.time-slot').forEach(function (slot) {
        slot.addEventListener('click', function () {
            const wrap = this.closest('.time-slots');
            if (wrap) {
                wrap.querySelectorAll('.time-slot').forEach(function (s) {
                    s.classList.remove('selected');
                });
            }
            this.classList.add('selected');
            const hidden = document.getElementById('selectedTime');
            if (hidden) hidden.value = this.getAttribute('data-time');
        });
    });

    // Booking summary update
    const summary = document.getElementById('bookingSummary');
    if (summary) {
        const updateSummary = function () {
            const mentor = document.getElementById('mentorName') ? document.getElementById('mentorName').value : 'Selected Mentor';
            const date = document.getElementById('bookingDate') ? document.getElementById('bookingDate').value : '-';
            const time = document.getElementById('selectedTime') ? document.getElementById('selectedTime').value : '-';
            const type = document.getElementById('sessionType') ? document.getElementById('sessionType').value : '-';
            summary.innerHTML =
                '<strong>' + mentor + '</strong><br>' +
                'Date: ' + date + ' | Time: ' + time + '<br>' +
                'Session: ' + type;
        };
        ['mentorName', 'bookingDate', 'sessionType'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', updateSummary);
        });
    }

    // Stepper navigation
    const steps = document.querySelectorAll('.step');
    const nextBtns = document.querySelectorAll('.btn-next');
    const prevBtns = document.querySelectorAll('.btn-prev');
    let currentStep = 0;

    function showStep(index) {
        steps.forEach(function (step, i) {
            step.style.display = i === index ? 'block' : 'none';
        });
        currentStep = index;
    }

    if (steps.length) {
        showStep(0);
        nextBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (currentStep < steps.length - 1) showStep(currentStep + 1);
            });
        });
        prevBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (currentStep > 0) showStep(currentStep - 1);
            });
        });
    }
});
