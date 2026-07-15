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
  <title>Assignments</title>
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
            <h1>Assignments</h1>
            <p>Browse assignments linked to courses and fields (Phase 3).</p>
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-4">
            <label class="form-label">Filter by Course ID</label>
            <input id="courseId" type="number" class="form-control" placeholder="e.g., 1" />
          </div>
          <div class="col-md-4">
            <label class="form-label">Filter by Field ID</label>
            <input id="fieldId" type="number" class="form-control" placeholder="e.g., 1" />
          </div>
          <div class="col-md-4 d-flex align-items-end gap-2">
            <button class="btn btn--primary" onclick="loadAssignmentsByCourse()">Load by Course</button>
            <button class="btn btn--outline" onclick="loadAssignmentsByField()">Load by Field</button>
          </div>
        </div>

        <div id="list" class="mt-4"></div>

      </div>
    </section>
  </main>

  <script>
    const API_URL = '../api/phase3.php?action=';

    function renderAssignments(items){
      const el = document.getElementById('list');
      if (!items || items.length === 0){
        el.innerHTML = '<div class="alert alert-info">No assignments found.</div>';
        return;
      }
      el.innerHTML = items.map(a => `
        <div class="soft-card mb-3">
          <div class="d-flex justify-content-between align-items-start gap-3">
            <div>
              <strong>${a.title}</strong>
              <div class="small text-light-emphasis mt-1">Deadline: ${a.deadline ?? 'N/A'}</div>
            </div>
            <div class="small text-light-emphasis">Created by: ${a.created_by_name ?? ''}</div>
          </div>
          <div class="mt-2">${a.description ?? ''}</div>
        </div>
      `).join('');
    }

    async function loadAssignmentsByCourse(){
      const courseId = document.getElementById('courseId').value;
      const res = await fetch(API_URL + 'list-assignments-by-course&course_id=' + encodeURIComponent(courseId || 0), {credentials:'same-origin'});
      const json = await res.json();
      renderAssignments(json?.data?.assignments || []);
    }

    async function loadAssignmentsByField(){
      const fieldId = document.getElementById('fieldId').value;
      const res = await fetch(API_URL + 'list-assignments-by-field&field_id=' + encodeURIComponent(fieldId || 0), {credentials:'same-origin'});
      const json = await res.json();
      renderAssignments(json?.data?.assignments || []);
    }

    // Default load all assignments
    (async function(){
      const res = await fetch(API_URL + 'list-assignments', {credentials:'same-origin'});
      const json = await res.json();
      renderAssignments(json?.data?.assignments || []);
    })();
  </script>
</body>
</html>

