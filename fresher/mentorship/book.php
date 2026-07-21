<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('fresher');

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mentorId = (int) ($_POST['mentor_id'] ?? 0);
    $topic = trim($_POST['topic'] ?? '');
    if (create_mentorship_booking($mentorId, $_SESSION['user_id'], $topic)) {
        add_notification($mentorId, 'New mentorship request received from a fresher.');
        redirect('fresher/mentorship/history.php');
    }
    $message = 'Unable to create mentorship request.';
}

$mentors = get_users('graduate');
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<main class="page-shell premium-scene">
  <section class="container">
    <div class="premium-hero glass-card">
      <div>
        <span class="premium-kicker"><i class="fa-solid fa-calendar-check"></i> Mentor Booking</span>
        <h1>Book a mentor session with a clear guided flow.</h1>
        <p>Select your field, course, subject, mentor, date, duration and payment status before sending the request for mentor approval.</p>
      </div>
      <div class="premium-hero__art">
        <div class="site-logo premium-hero__logo" aria-hidden="true"></div>
        <div class="premium-icon-cloud" aria-hidden="true">
          <span><i class="fa-solid fa-layer-group"></i></span>
          <span><i class="fa-solid fa-video"></i></span>
          <span><i class="fa-solid fa-credit-card"></i></span>
          <span><i class="fa-solid fa-circle-check"></i></span>
        </div>
      </div>
    </div>

    <?php if ($message): ?><div class="alert alert-danger"><?php echo e($message); ?></div><?php endif; ?>

    <div class="glass-card card--padded">
      <div class="booking-steps" aria-label="Booking steps">
        <button class="booking-step is-active" type="button" data-booking-step="learn">1. Learning Goal</button>
        <button class="booking-step" type="button" data-booking-step="mentor">2. Mentor</button>
        <button class="booking-step" type="button" data-booking-step="time">3. Date & Time</button>
        <button class="booking-step" type="button" data-booking-step="payment">4. Payment</button>
        <button class="booking-step" type="button" data-booking-step="confirm">5. Confirm</button>
      </div>

      <form method="post">
        <div class="booking-panel is-active" data-booking-panel="learn">
          <div class="premium-grid-3">
            <div><label class="form-label">Academic Field</label><select class="form-control"><option>Information Technology</option><option>Engineering</option><option>Management</option><option>Health Sciences</option></select></div>
            <div><label class="form-label">Course</label><select class="form-control"><option>Full Stack Web Development</option><option>Database Management</option><option>Business Management</option></select></div>
            <div><label class="form-label">Subject</label><select class="form-control"><option>PHP & MySQL</option><option>JavaScript Basics</option><option>Interview Preparation</option></select></div>
          </div>
          <div class="mt-4"><label class="form-label">Session Topic</label><input type="text" name="topic" class="form-control" value="PHP & MySQL project guidance" required /></div>
          <button class="btn btn--primary mt-4" type="button" data-next-step="mentor">Find Mentor</button>
        </div>

        <div class="booking-panel" data-booking-panel="mentor">
          <div class="premium-grid-3">
            <?php foreach (array_slice($mentors, 0, 3) as $index => $mentor): ?>
              <label class="premium-card" style="cursor:pointer;">
                <input type="radio" name="mentor_id" value="<?php echo (int)$mentor['id']; ?>" <?php echo $index === 0 ? 'checked' : ''; ?> />
                <div class="mentor-head mt-3">
                  <div class="avatar-photo"><?php echo e(strtoupper(substr($mentor['full_name'] ?? 'M', 0, 1))); ?></div>
                  <div><h3><?php echo e($mentor['full_name'] ?? 'Mentor'); ?></h3><p>Full Stack Mentor</p></div>
                </div>
                <div class="d-flex gap-2" style="flex-wrap:wrap"><span class="tag">4.9 Rating</span><span class="tag tag--green">Available</span></div>
              </label>
            <?php endforeach; ?>
          </div>
          <button class="btn btn--primary mt-4" type="button" data-next-step="time">Select Date & Time</button>
        </div>

        <div class="booking-panel" data-booking-panel="time">
          <div class="premium-grid-3">
            <div><label class="form-label">Date</label><input class="form-control" type="date" /></div>
            <div><label class="form-label">Time</label><input class="form-control" type="time" /></div>
            <div><label class="form-label">Duration</label><select class="form-control"><option>30 Minutes</option><option selected>60 Minutes</option><option>90 Minutes</option></select></div>
          </div>
          <button class="btn btn--primary mt-4" type="button" data-next-step="payment">Continue to Payment</button>
        </div>

        <div class="booking-panel" data-booking-panel="payment">
          <div class="premium-grid-2">
            <article class="premium-card"><div class="module-icon module-icon--orange"><i class="fa-solid fa-wallet"></i></div><h3>Session Fee</h3><p>Simulated payment for mentorship session.</p><h2 class="mt-3">Rs. 800</h2></article>
            <article class="premium-card"><div class="module-icon module-icon--green"><i class="fa-solid fa-shield-check"></i></div><h3>Status</h3><p>Payment marked ready. Mentor approval is required before joining.</p><span class="tag tag--green mt-3">Waiting for Approval</span></article>
          </div>
          <button class="btn btn--primary mt-4" type="button" data-next-step="confirm">Review Booking</button>
        </div>

        <div class="booking-panel" data-booking-panel="confirm">
          <div class="premium-card">
            <h3>Booking Summary</h3>
            <div class="meta-list">
              <span>Field: <strong>Information Technology</strong></span>
              <span>Course: <strong>Full Stack Web Development</strong></span>
              <span>Subject: <strong>PHP & MySQL</strong></span>
              <span>Status after request: <strong>Waiting for Mentor Approval</strong></span>
            </div>
            <button class="btn btn--primary" type="submit">Submit Booking Request</button>
            <a class="btn btn--outline" href="history.php">View Booking History</a>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>

<?php include '../../includes/footer.php'; ?>
