document.addEventListener('DOMContentLoaded', () => {
  const passwordInput = document.querySelector('[data-password]');
  const toggleButton = document.querySelector('[data-password-toggle]');

  if (passwordInput && toggleButton) {
    toggleButton.addEventListener('click', () => {
      const isHidden = passwordInput.type === 'password';
      passwordInput.type = isHidden ? 'text' : 'password';
      toggleButton.innerHTML = isHidden ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
    });
  }
});
