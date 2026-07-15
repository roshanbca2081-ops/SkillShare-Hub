<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
ensure_database_schema();
require_login('graduate');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Graduate Profile</title>
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
            <h1>Graduate Profile</h1>
            <p>Show your experience, skills, and availability for mentorship.</p>
          </div>
        </div>

        <form id="graduateProfileForm" class="mt-4" autocomplete="off">
          <div class="row g-4">
            <div class="col-md-12">
              <label class="form-label">Biography</label>
              <textarea name="biography" class="form-control" rows="4" placeholder="Tell students about your mentorship style..."></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">Experience</label>
              <textarea name="experience" class="form-control" rows="3" placeholder="Years of experience, notable work..."></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">Company</label>
              <input name="company" class="form-control" type="text" />
            </div>

            <div class="col-md-6">
              <label class="form-label">Skills</label>
              <input name="skills" class="form-control" type="text" placeholder="Comma-separated skills" />
            </div>

            <div class="col-md-6">
              <label class="form-label">Hourly Rate</label>
              <input name="hourly_rate" class="form-control" type="number" step="0.01" />
            </div>

            <div class="col-md-6">
              <label class="form-label">Available Time</label>
              <input name="available_time" class="form-control" type="text" placeholder="e.g., Mon-Wed evenings" />
            </div>

            <div class="col-md-6">
              <label class="form-label">Languages</label>
              <input name="languages" class="form-control" type="text" placeholder="Comma-separated" />
            </div>

            <div class="col-md-12">
              <label class="form-label">Certificates (optional)</label>
              <textarea name="certificates" class="form-control" rows="3" placeholder="List certificates or links..."></textarea>
            </div>

            <div class="col-md-12">
              <label class="form-label">Portfolio URL (optional)</label>
              <input name="portfolio_url" class="form-control" type="url" placeholder="https://..." />
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
      const el = document.querySelector(`#graduateProfileForm [name="${name}"]`);
      if (el) el.value = value ?? '';
    }

    async function loadProfile() {
      const res = await fetch(API_URL + 'get-graduate-profile', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json || json.status !== 'success') {
        document.getElementById('profileMessage').innerHTML = '<div class="alert alert-danger">Failed to load profile</div>';
        return;
      }

      const p = json.data || {};

      setField('biography', p.biography);
      setField('experience', p.experience);
      setField('company', p.company);
      setField('skills', p.skills);
      setField('hourly_rate', p.hourly_rate);
      setField('available_time', p.available_time);
      setField('languages', p.languages);
      setField('certificates', p.certificates);
      setField('portfolio_url', p.portfolio_url);
    }

    async function saveProfile(e) {
      e.preventDefault();

      const form = e.target;
      const data = Object.fromEntries(new FormData(form).entries());

      // Keep payload minimal; API requires experience/company/hourly_rate
      const payload = {
        experience: data.experience,
        company: data.company,
        hourly_rate: data.hourly_rate,
        skills: data.skills,
        biography: data.biography,
        available_time: data.available_time,
        languages: data.languages,
        certificates: data.certificates,
        portfolio_url: data.portfolio_url
      };

      const res = await fetch(API_URL + 'update-graduate-profile', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const json = await res.json();
      const box = document.getElementById('profileMessage');

      if (json && json.status === 'success') {
        box.innerHTML = '<div class="alert alert-success">Profile saved</div>';
      } else {
        box.innerHTML = `<div class="alert alert-danger">${(json && json.message) ? json.message : 'Save failed'}</div>`;
      }
    }

    document.getElementById('graduateProfileForm').addEventListener('submit', saveProfile);
    loadProfile();
  </script>
</body>
</html>

