<?php include '../../config/config.php'; include '../../includes/functions.php'; ?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<main class="page-shell premium-scene">
  <section class="container">
    <div class="page-title">
      <div>
        <span class="premium-kicker"><i class="fa-solid fa-video"></i> Join Session</span>
        <h1>My Mentorship Sessions</h1>
        <p>Join confirmed sessions, review waiting approvals, and track attendance history.</p>
      </div>
      <a class="btn btn--primary" href="book.php">Book New Session</a>
    </div>

    <div class="premium-grid-3">
      <article class="premium-card"><div class="module-icon"><i class="fa-solid fa-circle-play"></i></div><h3>Next Session</h3><p>PHP & MySQL with Roshan Timalsina</p><a class="btn btn--primary mt-3" href="../../graduate/video-sessions/index.php">Join Video Session</a></article>
      <article class="premium-card"><div class="module-icon module-icon--orange"><i class="fa-solid fa-hourglass-half"></i></div><h3>Waiting Approval</h3><p>JavaScript Basics booking request is pending mentor confirmation.</p><span class="tag tag--orange mt-3">Pending</span></article>
      <article class="premium-card"><div class="module-icon module-icon--green"><i class="fa-solid fa-clipboard-check"></i></div><h3>Attendance</h3><p>12 sessions attended this month.</p><div class="progress-bar mt-3"><span style="width:82%"></span></div></article>
    </div>

    <div class="glass-card card--padded mt-4">
      <h3>Session History</h3>
      <div class="timeline mt-3">
        <div class="timeline-item"><span class="timeline-dot"><i class="fa-solid fa-check"></i></span><div><strong>React for Beginners</strong><p class="text-light-emphasis">Completed with feedback and notes.</p></div></div>
        <div class="timeline-item"><span class="timeline-dot"><i class="fa-solid fa-check"></i></span><div><strong>Database Relationships</strong><p class="text-light-emphasis">Attendance marked and assignment assigned.</p></div></div>
      </div>
    </div>
  </section>
</main>

<?php include '../../includes/footer.php'; ?>
