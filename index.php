<?php include 'config/config.php'; include 'includes/functions.php'; ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<main class="page-shell">
  <section class="hero-section">
    <div class="container hero-grid">
      <div class="hero-copy animate">
        <span class="tag tag--orange">SkillShare Hub</span>
        <h1>Share Knowledge.<br />Learn Together. Grow Forever.</h1>
        <p>Find the best mentors, courses and resources to achieve your goals across academic fields, guided sessions, assignments and certificates.</p>
        <div class="hero-actions">
          <a href="course.php" class="btn btn--primary">Explore Courses</a>
          <a href="mentor.php" class="btn btn--outline">Find a Mentor</a>
        </div>
      </div>
      <div class="hero-visual" aria-label="Learners and mentors">
        <div class="floating-icons">
          <i class="fa-solid fa-book-open"></i>
          <i class="fa-solid fa-laptop-code"></i>
          <i class="fa-solid fa-user-graduate"></i>
          <i class="fa-solid fa-chalkboard-user"></i>
          <i class="fa-solid fa-rocket"></i>
        </div>
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
        <h2>Academic Fields</h2>
        <a href="course.php">Browse All Courses</a>
      </div>
      <div class="field-grid">
        <?php
          $fields = [
            ['name' => 'Information Technology', 'icon' => 'fa-laptop-code', 'mentors' => '3 Mentors', 'courses' => '12+ Courses', 'learners' => '70+ Learners', 'items' => ['Full Stack Web Development', 'Mobile App Development', 'Database Management']],
            ['name' => 'Computer Engineering', 'icon' => 'fa-microchip', 'mentors' => '2 Mentors', 'courses' => '9+ Courses', 'learners' => '48+ Learners', 'items' => ['Embedded Systems', 'IoT Design', 'Digital Logic']],
            ['name' => 'Software Engineering', 'icon' => 'fa-code', 'mentors' => '3 Mentors', 'courses' => '11+ Courses', 'learners' => '65+ Learners', 'items' => ['Agile Development', 'System Design', 'Quality Assurance']],
            ['name' => 'Civil Engineering', 'icon' => 'fa-person-digging', 'mentors' => '1 Mentor', 'courses' => '7+ Courses', 'learners' => '22+ Learners', 'items' => ['Structural Design', 'Construction Management', 'Land Surveying']],
            ['name' => 'Agriculture', 'icon' => 'fa-seedling', 'mentors' => '1 Mentor', 'courses' => '7+ Courses', 'learners' => '11+ Learners', 'items' => ['Agriculture Basics', 'Soil Management', 'Crop Planning']],
            ['name' => 'Business Administration', 'icon' => 'fa-chart-line', 'mentors' => '2 Mentors', 'courses' => '8+ Courses', 'learners' => '44+ Learners', 'items' => ['Business Management', 'Marketing Essentials', 'Finance Fundamentals']],
            ['name' => 'Nursing', 'icon' => 'fa-heart-pulse', 'mentors' => '1 Mentor', 'courses' => '5+ Courses', 'learners' => '19+ Learners', 'items' => ['Patient Care', 'Medical Ethics', 'Clinical Practice']],
            ['name' => 'Law', 'icon' => 'fa-gavel', 'mentors' => '1 Mentor', 'courses' => '5+ Courses', 'learners' => '44+ Learners', 'items' => ['Legal Foundation', 'Corporate Law Basics', 'Civil Procedure']],
            ['name' => 'Architecture', 'icon' => 'fa-building', 'mentors' => '2 Mentors', 'courses' => '6+ Courses', 'learners' => '38+ Learners', 'items' => ['Design Studio', 'Urban Planning', 'Sustainable Architecture']],
            ['name' => 'Hotel Management', 'icon' => 'fa-hotel', 'mentors' => '1 Mentor', 'courses' => '4+ Courses', 'learners' => '28+ Learners', 'items' => ['Hospitality Basics', 'Food & Beverage', 'Front Desk Operations']],
          ];
        ?>
        <?php foreach ($fields as $field): ?>
          <article class="field-card animate">
            <div class="field-card-body">
              <div class="field-icon"><i class="fa-solid <?php echo e($field['icon']); ?>"></i></div>
              <h3><?php echo e($field['name']); ?></h3>
              <div class="field-stats">
                <span><?php echo e($field['mentors']); ?></span>
                <span><?php echo e($field['courses']); ?></span>
                <span><?php echo e($field['learners']); ?></span>
              </div>
              <div class="meta-list" style="margin-top:16px;">
                <?php foreach ($field['items'] as $item): ?>
                  <span><i class="fa-solid fa-circle-dot" style="font-size:.75rem;color:var(--primary);"></i> <?php echo e($item); ?></span>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="field-card-action">
              <a href="course.php?field=<?php echo urlencode($field['name']); ?>"><i class="fa-solid fa-arrow-right"></i> Explore Course</a>
            </div>
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
