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

      <div class="premium-bg" aria-hidden="true">
        <div class="premium-bg__watermark" data-parallax-y="-10">
          <div class="premium-bg__watermark-logo">ShareSkill Hub</div>
        </div>

        <div class="premium-bg__particles" data-parallax-y="8">
          <i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>
          <i></i><i></i><i></i><i></i><i></i><i></i><i></i>
        </div>

        <div class="premium-bg__glow-orbs" aria-hidden="true">
          <span class="orb orb--1" data-parallax-x="-22" data-parallax-y="14"></span>
          <span class="orb orb--2" data-parallax-x="18" data-parallax-y="-10"></span>
          <span class="orb orb--3" data-parallax-x="-10" data-parallax-y="-18"></span>
        </div>

        <div class="premium-bg__gradient" aria-hidden="true"></div>
      </div>

      <div class="field-icon-grid" role="list">
        <?php
          $fields = [
            ['slug' => 'engineering', 'name' => 'Engineering', 'icon' => 'fa-gears'],
            ['slug' => 'information-technology', 'name' => 'Information Technology', 'icon' => 'fa-laptop-code'],
            ['slug' => 'science', 'name' => 'Science', 'icon' => 'fa-flask'],
            ['slug' => 'management', 'name' => 'Management', 'icon' => 'fa-chart-line'],
            ['slug' => 'agriculture', 'name' => 'Agriculture', 'icon' => 'fa-seedling'],
            ['slug' => 'health-sciences', 'name' => 'Health Sciences', 'icon' => 'fa-heart-pulse'],
            ['slug' => 'education', 'name' => 'Education', 'icon' => 'fa-graduation-cap'],
            ['slug' => 'law', 'name' => 'Law', 'icon' => 'fa-gavel'],
            ['slug' => 'arts-humanities', 'name' => 'Arts & Humanities', 'icon' => 'fa-palette'],
            ['slug' => 'media-communication', 'name' => 'Media & Communication', 'icon' => 'fa-broadcast-tower'],
            ['slug' => 'hospitality-tourism', 'name' => 'Hospitality & Tourism', 'icon' => 'fa-hotel'],
            ['slug' => 'research-innovation', 'name' => 'Research & Innovation', 'icon' => 'fa-lightbulb'],
          ];
        ?>

        <?php foreach ($fields as $i => $field): ?>
          <a
            class="field-icon-item"
            href="field.php?field=<?php echo urlencode($field['slug']); ?>"
            role="listitem"
            data-float-delay="<?php echo (string)($i * 120); ?>ms"
          >
            <span class="field-icon-item__glass" data-parallax-x="<?php echo ($i % 2 === 0 ? -10 : 10); ?>" data-parallax-y="<?php echo ($i % 3) * 6; ?>">
              <i class="fa-solid <?php echo e($field['icon']); ?>" aria-hidden="true"></i>
              <span class="field-icon-item__label"><?php echo e($field['name']); ?></span>
            </span>
          </a>
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
