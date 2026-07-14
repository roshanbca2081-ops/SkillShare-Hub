<?php include 'config/config.php'; include 'includes/functions.php'; ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<main class="page-shell">
  <section class="home-hero">
    <div class="container home-grid">
      <div class="hero-copy animate">
        <h1>Share Knowledge.<br />Learn Together. Grow Forever.</h1>
        <p>Find the best mentors, courses and resources to achieve your goals across academic fields, guided sessions, assignments and certificates.</p>
        <div class="hero-actions">
          <a href="course.php" class="btn btn--primary">Explore Courses</a>
          <a href="mentor.php" class="btn btn--outline">Find a Mentor</a>
        </div>
      </div>
      <div class="hero-visual" aria-label="Learners and mentors">
        <div class="people-row">
          <div class="person"></div>
          <div class="person"></div>
          <div class="person"></div>
        </div>
      </div>
    </div>
  </section>

  <section class="field-overview">
    <div class="container">
      <div class="section-heading">
        <h2>Top Academic Fields</h2>
        <a href="course.php">View All Fields</a>
      </div>
      <div class="field-grid">
        <?php
          $fields = [
            ['Engineering','EN','2 Mentors','12+ Courses','50+ Learners'],
            ['Information Technology','IT','3 Mentors','6+ Courses','70+ Learners'],
            ['Agriculture','AG','1 Mentor','7+ Courses','11+ Learners'],
            ['Medical','MD','2 Mentors','8+ Courses','64 Learners'],
            ['Law','LW','1 Mentor','5+ Courses','44 Learners'],
            ['Management','MG','2 Mentors','6+ Courses','72 Learners'],
          ];
        ?>
        <?php foreach ($fields as $field): ?>
          <article class="field-card animate">
            <div class="field-icon"><?php echo e($field[1]); ?></div>
            <h3><?php echo e($field[0]); ?></h3>
            <div class="field-stats">
              <span><?php echo e($field[2]); ?></span>
              <span><?php echo e($field[3]); ?></span>
              <span><?php echo e($field[4]); ?></span>
            </div>
            <a href="course.php?field=<?php echo urlencode($field[0]); ?>">Explore Field -></a>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="container feature-strip">
    <article class="mini-module">
      <h3>Create / Join Group</h3>
      <p>Join course groups, research circles and project teams with mentor guidance.</p>
    </article>
    <article class="mini-module">
      <h3>Interview Preparation</h3>
      <p>Practice common interview questions, mock interviews and resume reviews.</p>
    </article>
    <article class="mini-module">
      <h3>Placement Preparation</h3>
      <p>Prepare resumes, cover letters, internship plans and placement guidance.</p>
    </article>
    <article class="mini-module">
      <h3>About SkillShare Hub</h3>
      <p>Connect learners and mentors around practical, outcome-focused learning.</p>
    </article>
  </section>
</main>

<?php include 'includes/footer.php'; ?>
