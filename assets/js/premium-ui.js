document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-premium-reveal]').forEach((el, index) => {
    el.style.animationDelay = `${index * 70}ms`;
    el.classList.add('animate');
  });

  const steps = Array.from(document.querySelectorAll('[data-booking-step]'));
  const panels = Array.from(document.querySelectorAll('[data-booking-panel]'));
  const showStep = (step) => {
    steps.forEach((item) => item.classList.toggle('is-active', item.dataset.bookingStep === step));
    panels.forEach((panel) => panel.classList.toggle('is-active', panel.dataset.bookingPanel === step));
  };

  steps.forEach((step) => {
    step.addEventListener('click', () => showStep(step.dataset.bookingStep));
  });

  document.querySelectorAll('[data-next-step]').forEach((button) => {
    button.addEventListener('click', () => showStep(button.dataset.nextStep));
  });

  document.querySelectorAll('[data-meeting-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      button.classList.toggle('round-btn--danger');
      const label = button.getAttribute('aria-label') || 'Control';
      if (typeof showToast === 'function') showToast(`${label} toggled`, 'info', 1600);
    });
  });
});
