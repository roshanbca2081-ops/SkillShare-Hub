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
          <h1>Practical Skills</h1>
          <p>Pick a practical skill to view mentors who can teach it.</p>
        </div>
      </div>
      <div class="row g-3 mt-2">
        <div class="col-md-4"><label class="form-label">Skill ID</label><input id="skillId" type="number" class="form-control" placeholder="e.g., 1" /></div>
        <div class="col-md-8 d-flex align-items-end gap-2"><button class="btn btn--primary" onclick="loadMentorsBySkill()">Find Mentors</button></div>
      </div>
      <div id="skills" class="mt-4"></div>
      <div id="mentors" class="mt-4"></div>
    </div>
  </section>
</main>
<script>
const API_URL = '../api/phase3.php?action=';
function renderCards(items, containerId, mapFn){
  const el = document.getElementById(containerId);
  if (!items || items.length === 0){ el.innerHTML = '<div class="alert alert-info">No items found.</div>'; return; }
  el.innerHTML = items.map(mapFn).join('');
}
async function loadSkills(){
  const res = await fetch(API_URL + 'list-practical-skills', {credentials:'same-origin'});
  const json = await res.json();
  renderCards(json?.data?.practical_skills || [], 'skills', s => `
    <div class="soft-card mb-3">
      <div class="d-flex justify-content-between"><strong>${s.title}</strong><span class="tag">ID: ${s.id}</span></div>
      <div class="mt-2">${s.description ?? ''}</div>
    </div>
  `);
}
async function loadMentorsBySkill(){
  const skillId = document.getElementById('skillId').value;
  const res = await fetch(API_URL + 'list-mentors-by-practical-skill&skill_id=' + encodeURIComponent(skillId || 0), {credentials:'same-origin'});
  const json = await res.json();
  renderCards(json?.data?.mentors || [], 'mentors', m => `
    <div class="soft-card mb-3">
      <strong>${m.full_name}</strong>
      <div class="small text-light-emphasis">${m.email ?? ''}</div>
      <div class="mt-3"><a class="btn btn--outline btn-sm" href="../mentor.php">View Mentors</a></div>
    </div>
  `);
}
(async function(){ await loadSkills(); })();
</script>
<?php include '../includes/footer.php'; ?>
