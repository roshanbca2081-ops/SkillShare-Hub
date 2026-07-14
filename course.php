<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
ensure_database_schema();

$courses = [
  ['Full Stack Web Development','Information Technology','Intermediate Level','3 Months','12','24','120+','Rs. 800','Node.js, MySQL, JavaScript'],
  ['Mobile App Development','Information Technology','Beginner Level','2 Months','8','16','85+','Rs. 700','Flutter, API, Firebase'],
  ['Database Management','Information Technology','Intermediate Level','2 Months','10','18','64+','Rs. 650','SQL, ERD, Backup'],
  ['Agriculture Basics','Agriculture','Beginner Level','1 Month','6','10','40+','Rs. 500','Soil, Crop, Planning'],
  ['Business Management','Management','Beginner Level','2 Months','7','12','72+','Rs. 600','Planning, Finance, Team'],
  ['Legal Foundation','Law','Beginner Level','1 Month','5','8','44+','Rs. 550','Case, Contract, Policy'],
];
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container">
    <div class="page-title">
      <div>
        <p class="small">Home > Information Technology > Full Stack Web Development</p>
        <h1>Full Stack Web Development</h1>
        <p>Learn frontend and backend development with real-world projects and become job ready.</p>
      </div>
      <a class="btn btn--primary" href="enrollment.php?course=Full%20Stack%20Web%20Development">Start Course</a>
    </div>
    <div class="course-hero mb-4">
      <div class="course-hero-grid">
        <div>
          <div class="d-flex gap-2 mb-3">
            <span class="tag">Intermediate Level</span>
            <span class="tag">3 Months</span>
            <span class="tag">Certificate</span>
          </div>
          <div class="meta-list">
            <span>Duration: <strong>3 Months</strong></span>
            <span>Mentors Available: <strong>3</strong></span>
            <span>Assignments: <strong>12</strong></span>
            <span>Video Sessions: <strong>24</strong></span>
            <span>Students Enrolled: <strong>120+</strong></span>
          </div>
        </div>
        <div class="course-summary soft-card text-center">
          <div class="field-icon" style="margin:0 auto 16px;width:80px;height:80px">JS</div>
          <div class="d-flex gap-2 justify-content-center" style="flex-wrap:wrap">
            <span class="chip">Node.js</span>
            <span class="chip">MySQL</span>
            <span class="chip">React</span>
            <span class="chip">API</span>
          </div>
          <h2 class="price mt-3">Rs. 800</h2>
          <p class="small text-light-emphasis">Full Course</p>
          <div class="d-flex justify-content-center gap-2 mt-3">
            <span class="tag">12 Assignments</span>
            <span class="tag">24 Sessions</span>
          </div>
        </div>
      </div>
    </div>

    <div class="section-heading">
      <h2>Popular Courses</h2>
      <a href="mentor.php">View Mentors</a>
    </div>
    <div class="course-grid">
      <?php foreach ($courses as $course): ?>
        <article class="course-card">
          <span class="tag"><?php echo e($course[1]); ?></span>
          <h3><?php echo e($course[0]); ?></h3>
          <p class="text-light-emphasis"><?php echo e($course[8]); ?></p>
          <div class="meta-list">
            <span><?php echo e($course[2]); ?></span>
            <span><?php echo e($course[3]); ?> - <?php echo e($course[4]); ?> assignments</span>
            <span><?php echo e($course[6]); ?> students</span>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <strong class="price"><?php echo e($course[7]); ?></strong>
            <a class="btn btn-primary btn-sm" href="enrollment.php?course=<?php echo urlencode($course[0]); ?>">Start</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
</main>
<?php include 'includes/footer.php'; ?>
