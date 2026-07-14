<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
ensure_database_schema();

$course = trim($_GET['course'] ?? $_POST['course'] ?? 'Full Stack Web Development');
$message = '';
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (create_enrollment([
        'student_id' => $_SESSION['user_id'] ?? null,
        'full_name' => trim($_POST['full_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'course' => trim($_POST['course'] ?? ''),
        'mentor' => trim($_POST['mentor'] ?? ''),
        'preferred_duration' => trim($_POST['preferred_duration'] ?? ''),
        'preferred_time' => trim($_POST['preferred_time'] ?? ''),
        'session_type' => trim($_POST['session_type'] ?? ''),
        'payment_method' => trim($_POST['payment_method'] ?? ''),
        'remarks' => trim($_POST['remarks'] ?? ''),
        'amount' => 800,
    ])) {
        $ok = true;
        $message = 'Enrollment saved successfully. Your course request is ready to start.';
        if (!empty($_SESSION['user_id'])) {
            add_notification($_SESSION['user_id'], 'Course enrollment created for ' . $course . '.');
        }
    } else {
        $message = 'Unable to save enrollment. Please check required fields.';
    }
}

$mentors = get_users('graduate');
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container">
    <div class="page-title">
      <div>
        <h1>Start Course - Enrollment Form</h1>
        <p>Complete basic details, select mentor, choose schedule and payment method.</p>
      </div>
    </div>
    <?php if ($message): ?>
      <div class="alert <?php echo $ok ? '' : 'alert-danger'; ?>"><?php echo e($message); ?></div>
    <?php endif; ?>
    <div class="app-layout" style="grid-template-columns:180px minmax(0,1fr) 270px">
      <aside class="card">
        <div class="meta-list">
          <span class="tag">1 Basic Details</span>
          <span>2 Select Mentor</span>
          <span>3 Choose Schedule</span>
          <span>4 Payment</span>
          <span>5 Confirm & Start</span>
        </div>
      </aside>
      <form class="card" method="post" id="enroll-form">
        <h3>Basic Details</h3>
        <div class="row g-4">
          <div class="col-md-6 mb-3"><label>Full Name</label><input name="full_name" value="<?php echo e($_SESSION['user_name'] ?? ''); ?>" required /></div>
          <div class="col-md-6 mb-3"><label>Email</label><input type="email" name="email" value="<?php echo e($_SESSION['user_email'] ?? ''); ?>" required /></div>
          <div class="col-md-6 mb-3"><label>Phone</label><input name="phone" placeholder="9800000000" /></div>
          <div class="col-md-6 mb-3"><label>Address</label><input name="address" placeholder="Kathmandu, Nepal" /></div>
          <div class="col-md-6 mb-3"><label>Freshers or Graduate</label><select name="role"><option>Fresher</option><option>Graduate</option></select></div>
          <div class="col-md-6 mb-3"><label>Field</label><select name="field"><option>Information Technology</option><option>Agriculture</option><option>Engineering</option></select></div>
          <div class="col-md-12 mb-3"><label>Course</label><input name="course" value="<?php echo e($course); ?>" required /></div>
          <div class="col-md-6 mb-3"><label>Preferred Duration</label><select name="preferred_duration"><option>3 Months</option><option>2 Months</option><option>1 Month</option></select></div>
          <div class="col-md-6 mb-3"><label>Preferred Time</label><select name="preferred_time"><option>07:00 PM - 08:00 PM</option><option>06:00 PM - 07:00 PM</option><option>08:00 AM - 09:00 AM</option></select></div>
          <div class="col-md-12 mb-3"><label>Session Type</label><select name="session_type"><option>Theory</option><option>Practical</option></select></div>
          <div class="col-md-12 mb-3"><label>Remarks / Goals</label><textarea name="remarks" rows="3" placeholder="I want to become a full stack developer."></textarea></div>
        </div>
        <div class="mt-4">
          <h3>Schedule & Payment</h3>
          <div class="row g-4">
            <div class="col-md-6 mb-3"><label>Choose Schedule</label><select name="schedule" class="form-select"><option>Monday, 7:00 PM</option><option>Wednesday, 6:00 PM</option><option>Friday, 8:00 AM</option></select></div>
            <div class="col-md-6 mb-3"><label>Payment Method</label><select name="payment_method" class="form-select"><option>eSewa</option><option>Khalti</option><option>FonePay</option></select></div>
          </div>
        </div>
        <button class="btn btn-primary" type="submit">Start Course</button>
      </form>
      <aside class="card">
        <h3>Choose Mentor</h3>
        <div class="meta-list">
          <?php foreach (array_slice($mentors, 0, 3) as $mentor): ?>
            <label class="soft-card" style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
              <span><?php echo e($mentor['full_name']); ?></span>
              <input type="radio" form="enroll-form" name="mentor" value="<?php echo e($mentor['full_name']); ?>" style="width:auto;min-height:auto" />
            </label>
          <?php endforeach; ?>
        </div>
        <h3>Choose Schedule</h3>
        <div class="meta-list">
          <label class="soft-card"><input type="radio" form="enroll-form" name="schedule" value="Monday, 7:00 PM" style="width:auto;min-height:auto" checked /> Monday, 7:00 PM</label>
          <label class="soft-card"><input type="radio" form="enroll-form" name="schedule" value="Wednesday, 6:00 PM" style="width:auto;min-height:auto" /> Wednesday, 6:00 PM</label>
          <label class="soft-card"><input type="radio" form="enroll-form" name="schedule" value="Friday, 8:00 AM" style="width:auto;min-height:auto" /> Friday, 8:00 AM</label>
        </div>
        <h3>Payment Method</h3>
        <div class="meta-list">
          <label class="soft-card"><input type="radio" name="payment_method" value="eSewa" form="enroll-form" style="width:auto;min-height:auto" checked /> eSewa</label>
          <label class="soft-card"><input type="radio" name="payment_method" value="Khalti" form="enroll-form" style="width:auto;min-height:auto" /> Khalti</label>
          <label class="soft-card"><input type="radio" name="payment_method" value="FonePay" form="enroll-form" style="width:auto;min-height:auto" /> FonePay</label>
        </div>
        <hr />
        <div class="d-flex justify-content-between"><strong>Total Amount</strong><strong style="color:var(--success)">Rs. 800</strong></div>
      </aside>
    </div>
  </section>
</main>
<?php include 'includes/footer.php'; ?>
