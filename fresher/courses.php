<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
ensure_database_schema();
require_login('fresher');
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container page-section">
    <div class="card card--padded animate" style="max-width:900px;margin:auto;">
      <div class="page-title">
        <div>
          <h1>Courses</h1>
          <p>Browse available courses.</p>
        </div>
      </div>
      <div id="courses" class="mt-4"></div>
    </div>
  </section>
</main>
<script>
const API_URL = '../api/phase3.php?action=';
function renderCourses(courses){
  const el = document.getElementById('courses');
  if (!courses || courses.length === 0){ el.innerHTML = '<div class="alert alert-info">No courses available yet.</div>'; return; }
  el.innerHTML = courses.map(c => `
    <div class="soft-card mb-3">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div><strong>${c.title}</strong><div class="small text-light-emphasis mt-1">${c.description ? c.description : ''}</div></div>
        <div class="tag">ID: ${c.id}</div>
      </div>
      <div class="mt-3">
        <a class="btn btn--outline btn-sm" href="assignments.php?course_id=${encodeURIComponent(c.id)}">View Assignments</a>
        <a class="btn btn--primary btn-sm" href="../enrollment.php?course=${encodeURIComponent(c.title)}">Enroll</a>
      </div>
    </div>
  `).join('');
}
(async function(){
  const res = await fetch(API_URL + 'list-courses', {credentials:'same-origin'});
  const json = await res.json();
  renderCourses(json?.data?.courses || []);
})();
</script>
<?php include '../includes/footer.php'; ?>
