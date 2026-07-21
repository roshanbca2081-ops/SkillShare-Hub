<?php include '../../config/config.php'; include '../../includes/functions.php'; ?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<main class="page-shell premium-scene">
  <section class="container">
    <div class="page-title">
      <div>
        <span class="premium-kicker"><i class="fa-solid fa-video"></i> Live Session</span>
        <h1>Video Session Room</h1>
        <p>Jitsi-style mentorship interface with controls, participants, chat, timer and session notes.</p>
      </div>
      <a class="btn btn--primary" href="schedule.php">Schedule Session</a>
    </div>

    <div class="meeting-shell">
      <section class="meeting-stage">
        <div style="position:relative;z-index:1;text-align:center;">
          <div class="avatar-photo" style="width:96px;height:96px;margin:0 auto 16px;">RT</div>
          <h2>PHP & MySQL Mentorship</h2>
          <p style="color:rgba(255,255,255,.72);">Session timer: <strong>00:42:18</strong></p>
        </div>
        <div class="meeting-controls">
          <button class="round-btn" type="button" data-meeting-toggle aria-label="Camera"><i class="fa-solid fa-video"></i></button>
          <button class="round-btn" type="button" data-meeting-toggle aria-label="Microphone"><i class="fa-solid fa-microphone"></i></button>
          <button class="round-btn" type="button" data-meeting-toggle aria-label="Screen Share"><i class="fa-solid fa-display"></i></button>
          <button class="round-btn round-btn--danger" type="button" aria-label="End Session"><i class="fa-solid fa-phone-slash"></i></button>
        </div>
      </section>

      <aside class="glass-card card--padded chat-panel">
        <div><h3>Participants & Chat</h3><p class="text-light-emphasis">4 active participants</p></div>
        <div class="chat-list">
          <div class="chat-bubble"><strong>Roshan:</strong> Can we review the database schema?</div>
          <div class="chat-bubble"><strong>Mentor:</strong> Yes, share your screen and open the ER diagram.</div>
          <div class="chat-bubble"><strong>Anjali:</strong> Notes uploaded in session files.</div>
        </div>
        <div class="input-icon"><i class="fa-regular fa-message"></i><input class="form-control" placeholder="Type a message..." /></div>
      </aside>
    </div>
  </section>
</main>

<?php include '../../includes/footer.php'; ?>
