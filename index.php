<?php include 'config/config.php'; include 'includes/functions.php'; ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<main class="page-shell">
  <section class="hero-section" style="position:relative;">
    <div class="premium-bg__watermark" aria-hidden="true" style="position:absolute;inset:0;display:grid;place-items:center;opacity:.14;z-index:0;">
      <div class="premium-bg__watermark-logo" style="filter:blur(1.2px);">ShareSkill Hub</div>
    </div>
    <div style="position:relative;z-index:1;">

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
  </div>

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
    <article class="mini-module reveal" data-reveal>
      <h3>Create / Join Group</h3>
      <p>Join course groups, research circles and project teams with mentor guidance.</p>
    </article>
    <article class="mini-module reveal" data-reveal>
      <h3>Interview Preparation</h3>
      <p>Practice common interview questions, mock interviews and resume reviews.</p>
    </article>
    <article class="mini-module reveal" data-reveal>
      <h3>Placement Preparation</h3>
      <p>Prepare resumes, cover letters, internship plans and placement guidance.</p>
    </article>
    <article class="mini-module reveal" data-reveal>
      <h3>About SkillShare Hub</h3>
      <p>Connect learners and mentors around practical, outcome-focused learning.</p>
    </article>
  </section>

  <section class="container" style="padding:18px 0 6px;">
    <div class="section-heading">
      <h2>Popular Courses</h2>
      <a href="course.php">View all</a>
    </div>
    <div class="course-grid">
      <?php
        $courses = get_courses();
        $courses = array_slice($courses, 0, 6);
        if (empty($courses)) {
          $courses = [
            ['id'=>1,'title'=>'Software Engineering Foundations','description'=>'Build practical engineering habits.'],
            ['id'=>2,'title'=>'Data Science Essentials','description'=>'From data cleaning to modeling.'],
            ['id'=>3,'title'=>'Cyber Security Basics','description'=>'Threats, defenses and secure thinking.'],
            ['id'=>4,'title'=>'Cloud Computing Fundamentals','description'=>'Deploy, scale and secure in the cloud.'],
            ['id'=>5,'title'=>'Networking for Developers','description'=>'Understand routing, protocols, and performance.'],
            ['id'=>6,'title'=>'AI Literacy & Applications','description'=>'Hands-on overview of modern AI workflows.'],
          ];
        }
      ?>
      <?php foreach ($courses as $course): ?>
        <article class="course-card reveal" data-reveal style="padding:22px;">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <div class="avatar-photo" style="width:52px;height:52px;border-radius:16px;font-size:.95rem;">CS</div>
            <span class="pill" style="background:#eef1ff;">Premium</span>
          </div>
          <h3 style="margin-top:14px;"><?php echo e($course['title']); ?></h3>
          <p style="margin:8px 0 12px;color:var(--muted);font-weight:650;line-height:1.6;font-size:.92rem;"><?php echo e($course['description'] ?? 'Learn with mentor guidance.'); ?></p>
          <a class="btn btn--primary" href="course.php" style="width:100%;text-decoration:none;">Explore course</a>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="container" style="padding:26px 0 8px;">
    <div class="section-heading">
      <h2>Popular Mentors</h2>
      <a href="mentor.php">View all</a>
    </div>
    <div class="mentor-grid">
      <input id="mentorSearch" class="form-control" type="search" placeholder="Search mentors" style="margin-bottom:14px;max-width:520px;">
      <div class="small" style="color:var(--muted);font-weight:900;margin-bottom:10px;">UI filter (static)</div>

      <?php
        // mentors currently not globally listed in this repo section; show premium UI placeholders.
        $mentors = [
          ['name'=>'Aarav Mehta','spec'=>'Software Engineering','rate'=>'$49/hr','tag'=>'Top Mentor'],
          ['name'=>'Sana Khan','spec'=>'Data Science','rate'=>'$59/hr','tag'=>'ML Expert'],
          ['name'=>'Neeraj Verma','spec'=>'Cyber Security','rate'=>'$45/hr','tag'=>'Red Team'],
          ['name'=>'Priya Nair','spec'=>'Cloud Computing','rate'=>'$55/hr','tag'=>'DevOps'],
          ['name'=>'Fatima Ali','spec'=>'Networking','rate'=>'$40/hr','tag'=>'CCNA'],
          ['name'=>'Omar Hassan','spec'=>'AI Applications','rate'=>'$65/hr','tag'=>'Applied AI'],
        ];
      ?>
      <?php foreach ($mentors as $m): ?>
        <article class="mentor-card reveal" data-reveal style="padding:22px;">
          <div class="mentor-head">
            <div class="avatar-photo" aria-hidden="true"><?php echo strtoupper(substr($m['name'],0,1)); ?></div>
            <div>
              <div style="font-weight:950;color:var(--ink);"><?php echo e($m['name']); ?></div>
              <div style="color:var(--muted);font-weight:800;font-size:.9rem;"><?php echo e($m['spec']); ?></div>
            </div>
          </div>
          <div class="meta-list">
            <div><span class="badge" style="background:rgba(255,255,255,.7);">☆</span> <?php echo e($m['tag']); ?></div>
            <div><span class="badge" style="background:#e7f8ef;color:#087b45;">💎</span> <?php echo e($m['rate']); ?></div>
          </div>
          <a class="btn btn--outline" href="mentor.php" style="width:100%;text-decoration:none;">Book a session</a>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="container" style="padding:28px 0 8px;">
    <div class="section-heading">
      <h2>Success Stories</h2>
      <a href="about.php">Read more</a>
    </div>
    <div class="course-grid">
      <?php
        $stories = [
          ['title'=>'From Fresher to Mentor-ready in 6 months','desc'=>'Guided assignments, feedback loops and portfolio milestones.'],
          ['title'=>'Placed after structured mentorship plan','desc'=>'Interview questions + mock sessions + resume review.'],
          ['title'=>'Research collaboration that led to publication','desc'=>'Join groups, upload documents, and discuss research topics.'],
          ['title'=>'Certified completion through mentorship tracks','desc'=>'Certificates after mentorship + course + assignments.'],
          ['title'=>'Video mentorship that boosted real outcomes','desc'=>'Session notes and attendance kept progress on track.'],
          ['title'=>'Payment + earnings clarity for mentors','desc'=>'Track wallet, payments and mentor earnings.'],
        ];
      ?>
      <?php foreach ($stories as $s): ?>
        <article class="mini-module reveal" data-reveal>
          <h3 style="margin-bottom:8px;"><?php echo e($s['title']); ?></h3>
          <p><?php echo e($s['desc']); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="container" style="padding:28px 0 10px;">
    <div class="section-heading">
      <h2>Statistics</h2>
      <span class="small" style="color:var(--muted);font-weight:900;">Live counters</span>
    </div>
    <?php $counts = get_dashboard_counts(); ?>
    <div class="stat-grid">
      <div class="stat-card reveal" data-reveal>
        <div class="stat-icon">👥</div>
        <div>
          <strong data-count="<?php echo (int)$counts['users']; ?>">0</strong>
          <span>Users</span>
        </div>
      </div>
      <div class="stat-card reveal" data-reveal>
        <div class="stat-icon">📚</div>
        <div>
          <strong data-count="<?php echo (int)$counts['courses']; ?>">0</strong>
          <span>Courses</span>
        </div>
      </div>
      <div class="stat-card reveal" data-reveal>
        <div class="stat-icon">🧩</div>
        <div>
          <strong data-count="<?php echo (int)$counts['assignments']; ?>">0</strong>
          <span>Assignments</span>
        </div>
      </div>
      <div class="stat-card reveal" data-reveal>
        <div class="stat-icon">🎥</div>
        <div>
          <strong data-count="<?php echo (int)$counts['sessions']; ?>">0</strong>
          <span>Mentorship Sessions</span>
        </div>
      </div>
    </div>
  </section>

  <section class="container" style="padding:28px 0 10px;">
    <div class="section-heading">
      <h2>Research • Placement • Assignments • Video Mentorship</h2>
      <a href="contact.php">Contact</a>
    </div>
    <div class="feature-strip" style="grid-template-columns:repeat(4,minmax(0,1fr));">
      <article class="mini-module reveal" data-reveal>
        <h3>Research</h3>
        <p>Join groups, discuss topics, and share research documents.</p>
        <a class="btn btn--outline" href="fresher/research/index.php" style="margin-top:12px;text-decoration:none;width:100%;">Explore Research</a>
      </article>
      <article class="mini-module reveal" data-reveal>
        <h3>Placement</h3>
        <p>Resume builder, review sessions, career roadmaps and interview prep.</p>
        <a class="btn btn--outline" href="fresher/placement/index.php" style="margin-top:12px;text-decoration:none;width:100%;">Placement Hub</a>
      </article>
      <article class="mini-module reveal" data-reveal>
        <h3>Assignments</h3>
        <p>Submit assignments, receive marks, feedback and deadlines.</p>
        <a class="btn btn--outline" href="fresher/assignments/index.php" style="margin-top:12px;text-decoration:none;width:100%;">Assignments</a>
      </article>
      <article class="mini-module reveal" data-reveal>
        <h3>Video Mentorship</h3>
        <p>Create and join mentorship sessions with notes and attendance tracking.</p>
        <a class="btn btn--outline" href="fresher/mentorship/index.php" style="margin-top:12px;text-decoration:none;width:100%;">Video Sessions</a>
      </article>
    </div>
  </section>

  <section class="container" style="padding:24px 0 10px;">
    <div class="section-heading">
      <h2>Certificates</h2>
      <a href="fresher/certificates/index.php">View</a>
    </div>
    <div class="course-grid">
      <article class="mini-module reveal" data-reveal>
        <h3>Mentorship Certificates</h3>
        <p>Generate after completing mentorship sessions and approvals.</p>
      </article>
      <article class="mini-module reveal" data-reveal>
        <h3>Assignment Certificates</h3>
        <p>Issued after submission deadlines and graduate feedback.</p>
      </article>
      <article class="mini-module reveal" data-reveal>
        <h3>Course Certificates</h3>
        <p>Complete courses and unlock certificate tracks.</p>
      </article>
    </div>
  </section>

  <section class="container" style="padding:26px 0 10px;">
    <div class="section-heading">
      <h2>Testimonials</h2>
      <a href="about.php">Community</a>
    </div>
    <div class="course-grid">
      <?php
        $testimonials = [
          ['name'=>'Riya','text'=>'Premium mentorship workflow with clear steps. Loved the assignments and feedback.'],
          ['name'=>'Vikram','text'=>'Placement preparation felt structured and practical. Sessions made a difference.'],
          ['name'=>'Neha','text'=>'Research groups are easy to join. Documents and discussions are organized.'],
          ['name'=>'Karthik','text'=>'The UI feels modern and fast. Dark mode is smooth too.'],
          ['name'=>'Mariam','text'=>'Video mentorship + session notes helped me track my progress confidently.'],
          ['name'=>'Sahil','text'=>'Certificates and history make it easy to show achievements.'],
        ];
      ?>
      <?php foreach ($testimonials as $t): ?>
        <article class="mini-module reveal" data-reveal>
          <h3 style="font-size:1rem;margin-bottom:8px;">“<?php echo e($t['text']); ?>”</h3>
          <p style="margin:0;color:var(--muted);font-weight:900;">— <?php echo e($t['name']); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="container" style="padding:26px 0 10px;">
    <div class="section-heading">
      <h2>FAQ</h2>
      <a href="contact.php">Ask a question</a>
    </div>
    <div class="course-grid">
      <?php
        $faqs = [
          ['q'=>'How do I book a mentorship session?','a'=>'Select a mentor, choose field/course/subject, pick date/time, and submit your request.'],
          ['q'=>'Can mentors upload assignments?','a'=>'Yes. Graduates upload assignments and fresher students submit work for marks.'],
          ['q'=>'Do you provide certificates?','a'=>'Certificates are generated after mentorship completion, course progress, and assignment milestones.'],
          ['q'=>'Is the platform secure?','a'=>'We use CSRF protection, prepared statements, and session-based authentication.'],
          ['q'=>'How are notifications handled?','a'=>'Events like bookings, messages, assignments and session approvals appear in your notification history.'],
          ['q'=>'Does dark mode work?','a'=>'Yes—your preference is saved and applied automatically.'],
        ];
      ?>
      <?php foreach ($faqs as $f): ?>
        <article class="mini-module reveal" data-reveal>
          <h3 style="margin-bottom:8px;"><?php echo e($f['q']); ?></h3>
          <p><?php echo e($f['a']); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="container" style="padding:26px 0 26px;">
    <div class="section-heading">
      <h2>Contact</h2>
      <a href="contact.php">Go</a>
    </div>
    <div class="mini-module reveal" data-reveal>
      <h3>Need help getting started?</h3>
      <p>Send us your message. We’ll help with mentorship bookings, assignments, research groups and placement prep.</p>
      <a class="btn btn--primary" href="contact.php" style="margin-top:14px;text-decoration:none;">Contact Support</a>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>
