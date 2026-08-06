/* =============================================
   SkillShare Hub - Profile Page JS
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    // Avatar preview on file select
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Profile completion progress
    const progressBar = document.querySelector('.profile-progress .progress-fill');
    const progressText = document.querySelector('.profile-progress .progress-text');
    if (progressBar) {
        const filled = document.querySelectorAll('.profile-complete-list li.completed').length;
        const total = document.querySelectorAll('.profile-complete-list li').length;
        const percent = total ? Math.round((filled / total) * 100) : 0;
        progressBar.style.width = percent + '%';
        if (progressText) progressText.textContent = percent + '%';
    }

    // Editable detail rows
    document.querySelectorAll('.editable-row').forEach(function (row) {
        const editBtn = row.querySelector('.edit-field');
        const display = row.querySelector('.field-display');
        const input = row.querySelector('.field-input');
        if (editBtn && display && input) {
            editBtn.addEventListener('click', function () {
                display.style.display = 'none';
                input.style.display = 'inline-block';
                input.focus();
                editBtn.innerHTML = '<i class="fa-solid fa-check"></i>';
            });
            const saveEdit = function () {
                display.textContent = input.value || display.textContent;
                display.style.display = 'inline-block';
                input.style.display = 'none';
                editBtn.innerHTML = '<i class="fa-solid fa-pen"></i>';
            };
            editBtn.addEventListener('click', function () {
                if (editBtn.innerHTML.includes('fa-check')) {
                    saveEdit();
                } else {
                    display.style.display = 'none';
                    input.style.display = 'inline-block';
                    input.focus();
                    editBtn.innerHTML = '<i class="fa-solid fa-check"></i>';
                }
            });
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') saveEdit();
                if (e.key === 'Escape') saveEdit();
            });
        }
    });

    // Skills tag input
    const skillInput = document.getElementById('skillInput');
    const skillsContainer = document.getElementById('skillsList');
    if (skillInput && skillsContainer) {
        skillInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const val = this.value.trim();
                if (val) {
                    const tag = document.createElement('span');
                    tag.className = 'skill-tag';
                    tag.innerHTML = val + ' <i class="fa-solid fa-xmark"></i>';
                    tag.querySelector('i').addEventListener('click', function () {
                        tag.remove();
                        updateSkillCount();
                    });
                    skillsContainer.appendChild(tag);
                    this.value = '';
                    updateSkillCount();
                }
            }
        });
        function updateSkillCount() {
            const count = document.querySelectorAll('.skill-tag').length;
            const el = document.querySelector('.skill-count');
            if (el) el.textContent = count;
        }
    }
});
