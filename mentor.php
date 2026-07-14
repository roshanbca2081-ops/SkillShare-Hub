<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
ensure_database_schema();

$mentors = get_users('graduate');
if (!$mentors) {
    $mentors = [
        ['id'=>1,'full_name'=>'Anany Sharma','email'=>'anany@example.com'],
        ['id'=>2,'full_name'=>'Ram Thapa','email'=>'ram@example.com'],
        ['id'=>3,'full_name'=>'Shyam Rai','email'=>'shyam@example.com'],
    ];
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container">
    <div class="page-title">
      <div>
        <h1>Available Mentors</h1>
        <p>Choose field-related mentors, compare experience and book sessions.</p>
      </div>
      <a class="btn btn--outline" href="course.php">Explore Courses</a>
    </div>
    <div class="mentor-grid">
      <?php foreach (array_slice($mentors, 0, 6) as $index => $mentor): ?>
        <?php
          $names = ['Anany Sharma','Ram Thapa','Shyam Rai','Sujan Karki','Sita Magar','Nisha KC'];
          $fields = ['Information Technology','Information Technology','Agriculture','Engineering','Medical','Management'];
          $prices = ['Rs. 800','Rs. 800','Rs. 3000','Rs. 700','Rs. 900','Rs. 650'];
          $name = $mentor['full_name'] ?: $names[$index % count($names)];
        ?>
        <article class="mentor-card">
          <div class="mentor-head">
            <div class="avatar-photo"><?php echo e(strtoupper(substr($name, 0, 1))); ?></div>
            <div>
              <h3 class="mb-0"><?php echo e($name); ?></h3>
              <p class="small text-light-emphasis mb-0">Graduate Mentor</p>
            </div>
          </div>
          <div class="d-flex gap-2 mb-3">
            <span class="tag">IT</span>
            <span class="tag tag--green">Full Stack Developer</span>
          </div>
          <div class="meta-list">
            <span>Experience: <strong><?php echo 3 + ($index % 4); ?>+ Years</strong></span>
            <span>Field: <strong><?php echo e($fields[$index % count($fields)]); ?></strong></span>
            <span>Course: <strong>Full Stack Developer</strong></span>
            <span>Rating: <strong>4.<?php echo 8 - ($index % 4); ?> (<?php echo 20 + $index; ?>)</strong></span>
            <span>Availability: <strong>1 Hr / Day</strong></span>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <strong class="price"><?php echo e($prices[$index % count($prices)]); ?></strong>
            <a class="btn btn-primary btn-sm" href="fresher/mentorship/book.php?mentor_id=<?php echo (int)$mentor['id']; ?>">Book Session</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
</main>
<?php include 'includes/footer.php'; ?>
