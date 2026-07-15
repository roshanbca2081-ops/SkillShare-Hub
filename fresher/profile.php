<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
ensure_database_schema();
require_login('fresher');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fresher Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <?php include '../includes/header.php'; ?>
  <?php include '../includes/navbar.php'; ?>

  <main class="page-shell">
    <section class="container page-section">
      <div class="card card--padded animate" style="max-width:900px;margin:auto;">
        <div class="page-title">
          <div>
            <h1>Fresher Profile</h1>
            <p>Update your academic background and learning goals.</p>
          </div>
        </div>

        <form id="fresherProfileForm" class="mt-4" autocomplete="off">
          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label">Academic Field</label>
              <input name="academic_field" class="form-control" type="text" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Course</label>
              <input name="course" class="form-control" type="text" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Semester</label>
              <input name="semester" class="form-control" type="text" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Skills</label>
              <input name="skills" class="form-control" type="text" placeholder="Comma-separated" />
            </div>

            <div class="col-md-6">
              <label class="form-label">Resume URL</label>
              <input name="resume_url" class="form-control" type="url" placeholder="https://..." />
            </div>
            <div class="col-md-6">
              <label class="form-label">Portfolio URL</label>
              <input name="portfolio_url" class="form-control" type="url" placeholder="https://..." />
            </div>

            <div class="col-12">
              <label class="form-label">Interests</label>
              <textarea name="interests" class="form-control" rows="3" placeholder="What topics do you want to learn?"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Personal Details</label>
              <textarea name="personal_details" class="form-control" rows="3" placeholder="Short bio, goals, and preferences"></textarea>
            </div>
          </div>

          <div class="mt-4 d-flex gap-3 flex-wrap">
            <button class="btn btn--primary" type="submit">Save Profile</button>
            <a class="btn btn--outline" href="../dashboard.php">Back to Dashboard</a>
          </div>

          <div id="profileMessage" class="mt-3"></div>
        </form>
      </div>
    </section>
  </main>

  <script>
    const API_URL = '../api/profile.php?action=';

    function setField(name, value) {
      const el = document.querySelector(`#fresherProfileForm [name="${name}"]`);
      if (el) el.value = value ?? '';
    }

    async function loadProfile() {
      const res = await fetch(API_URL + 'get-fresher-profile', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json || json.status !== 'success') {
        document.getElementById('profileMessage').innerHTML = '<div class="alert alert-danger">Failed to load profile</div>';
        return;
      }

      const p = json.data || {};

      setField('academic_field', p.academic_field);
      setField('course', p.course);
      setField('semester', p.semester);
      setField('skills', p.skills);
      setField('resume_url', p.resume_url);
      setField('portfolio_url', p.portfolio_url);
      setField('interests', p.interests);
      setField('personal_details', p.personal_details);
    }

    async function saveProfile(e) {
      e.preventDefault();

      const form = e.target;
      const data = Object.fromEntries(new FormData(form).entries());

      const res = await fetch(API_URL + 'update-fresher-profile', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      const json = await res.json();
      const box = document.getElementById('profileMessage');

      if (json && json.status === 'success') {
        box.innerHTML = '<div class="alert alert-success">Profile saved</div>';
      } else {
        box.innerHTML = `<div class="alert alert-danger">${(json && json.message) ? json.message : 'Save failed'}</div>`;
      }
    }

    document.getElementById('fresherProfileForm').addEventListener('submit', saveProfile);
    loadProfile();
  </script>
</body>
</html>

