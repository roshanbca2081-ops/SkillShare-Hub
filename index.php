<?php include 'config/config.php'; include 'includes/functions.php'; require_once 'includes/field-data.php'; ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<main class="page-shell">
  <section class="hero-section">
    <div class="container hero-grid">
      <div class="hero-copy animate">
        <span class="tag">Welcome to Skill Share Hub</span>
        <h1>Learn. Share.<br />Grow <span>Together.</span></h1>
        <p>A platform where graduates share practical knowledge and skills with future learners across all fields.</p>
        <div class="hero-actions">
          <a href="mentor.php" class="btn btn--primary">Explore Mentors</a>
          <a href="course.php" class="btn btn--outline">Explore Courses</a>
        </div>
      </div>
      <div class="hero-visual" aria-label="Learners and mentors">
        <div class="hero-watermark site-logo" aria-hidden="true"></div>
        <div class="hero-orbit">
          <div class="hero-orbit__ring hero-orbit__ring--outer"></div>
          <div class="hero-orbit__ring hero-orbit__ring--inner"></div>
          <div class="hero-orbit__center"><span class="site-logo site-logo--auth" aria-hidden="true"></span></div>
          <span class="orbit-icon orbit-icon--1"><i class="fa-solid fa-laptop-code"></i></span>
          <span class="orbit-icon orbit-icon--2"><i class="fa-solid fa-book-open"></i></span>
          <span class="orbit-icon orbit-icon--3"><i class="fa-solid fa-flask"></i></span>
          <span class="orbit-icon orbit-icon--4"><i class="fa-solid fa-scale-balanced"></i></span>
          <span class="orbit-icon orbit-icon--5"><i class="fa-solid fa-seedling"></i></span>
          <span class="orbit-person orbit-person--1"></span>
          <span class="orbit-person orbit-person--2"></span>
          <span class="orbit-person orbit-person--3"></span>
        </div>
      </div>
    </div>
  </section>

  <section class="container home-stats">
    <article class="stat-card"><div class="stat-icon"><i class="fa-regular fa-user"></i></div><div><strong>500+</strong><span>Mentors</span></div></article>
    <article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-computer"></i></div><div><strong>1000+</strong><span>Courses</span></div></article>
    <article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-users"></i></div><div><strong>10,000+</strong><span>Freshers</span></div></article>
    <article class="stat-card"><div class="stat-icon"><i class="fa-solid fa-layer-group"></i></div><div><strong>50+</strong><span>Academic Fields</span></div></article>
  </section>

  <section class="field-overview" id="academic-fields">
    <div class="container">
      <div class="section-heading">
        <h2>Academic Fields</h2>
        <a href="course.php">Browse All Courses</a>
      </div>

      <div class="premium-bg" aria-hidden="true">
        <div class="premium-bg__watermark" data-parallax-y="-10">
          <div class="site-logo site-logo--auth premium-bg__watermark-logo" aria-hidden="true"></div>
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
          $fieldList = array_values($academicFields ?? []);
          $mainFieldCount = 12;
          $displayFields = array_slice($fieldList, 0, $mainFieldCount);
        ?>
        <?php foreach ($displayFields as $i => $f): ?>
          <a
            class="field-icon-item"
            href="field.php?field=<?php echo urlencode($f['slug']); ?>"
            role="listitem"
            style="--float-delay: <?php echo (string)($i * 120); ?>ms;"
          >
            <span class="field-icon-item__glass" data-parallax-x="<?php echo ($i % 2 === 0 ? -10 : 10); ?>" data-parallax-y="<?php echo ($i % 3) * 6; ?>">
              <i class="fa-solid <?php echo e($f['icon'] ?? 'fa-graduation-cap'); ?>" aria-hidden="true"></i>
              <span class="field-icon-item__label"><?php echo e($f['name']); ?></span>
            </span>
            <?php if (!empty($f['courses'])): ?>
              <span class="field-course-count"><?php echo count($f['courses']); ?> Courses</span>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
        <!-- View All Fields CTA -->
        <a class="field-icon-item" href="course.php" role="listitem" style="--float-delay: 1500ms;">
          <span class="field-icon-item__glass view-all-fields">
            <i class="fa-solid fa-arrow-right" aria-hidden="true" style="font-size:24px;"></i>
            <span class="field-icon-item__label" style="background:linear-gradient(135deg,var(--primary),var(--primary-2));">View All Fields →</span>
          </span>
        </a>
      </div>
      <?php if (count($fieldList) > $mainFieldCount): ?>
      <div style="text-align:center;margin-top:18px;">
        <a href="course.php" class="btn btn--outline" style="border-radius:999px;padding:12px 28px;">
          <i class="fa-regular fa-compass"></i> Explore All <?php echo count($fieldList); ?> Academic Fields
        </a>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ===== HOW IT WORKS ===== -->
  <section class="container how-it-works-section" id="how-it-works">
    <div class="section-heading">
      <h2>How It Works</h2>
      <a href="register.php">Get Started</a>
    </div>
    <div class="how-it-works-grid">
      <div class="how-step reveal" data-reveal>
        <div class="how-step__number">1</div>
        <h3>Create Your Account</h3>
        <p>Sign up as a Fresher or Graduate Mentor. Complete your profile with your field, skills, and interests.</p>
      </div>
      <div class="how-step reveal" data-reveal>
        <div class="how-step__number">2</div>
        <h3>Choose Your Path</h3>
        <p>Explore academic fields, enroll in courses, find mentors, or join research groups that match your goals.</p>
      </div>
      <div class="how-step reveal" data-reveal>
        <div class="how-step__number">3</div>
        <h3>Learn & Grow</h3>
        <p>Book mentorship sessions, complete assignments, earn certificates, and prepare for your career.</p>
      </div>
    </div>
  </section>

  <!-- ===== FEATURES ===== -->
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
    <?php
      // Aggregate courses from all fields in field-data.php for a rich display
      $allCourses = [];
      foreach (($academicFields ?? []) as $fieldSlug => $fieldData) {
        if (!empty($fieldData['courses'])) {
          foreach ($fieldData['courses'] as $c) {
            $c['field_name'] = $fieldData['name'];
            $c['field_slug'] = $fieldSlug;
            $c['field_icon'] = $fieldData['icon'] ?? 'fa-graduation-cap';
            $allCourses[] = $c;
          }
        }
      }
      $allCourses = array_slice($allCourses, 0, 9);
    ?>
    <?php if ($allCourses): ?>
      <div class="course-grid">
        <?php foreach ($allCourses as $c): ?>
          <article class="course-card reveal" data-reveal style="padding:22px;">
            <div class="course-media" style="height:140px;border-radius:14px;overflow:hidden;margin:-22px -22px 14px;background:linear-gradient(135deg,rgba(79,70,229,.12),rgba(6,182,212,.08));">
              <?php if (!empty($c['image'])): ?>
                <img src="<?php echo e($c['image']); ?>" alt="<?php echo e($c['title']); ?>" style="width:100%;height:100%;object-fit:cover;" loading="lazy" />
              <?php else: ?>
                <div style="display:grid;place-items:center;height:100%;">
                  <i class="fa-solid <?php echo e($c['field_icon']); ?>" style="font-size:2.8rem;opacity:.25;color:var(--primary);"></i>
                </div>
              <?php endif; ?>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
              <span class="pill" style="background:#eef1ff;font-size:.7rem;"><?php echo e($c['field_name'] ?? 'General'); ?></span>
              <span class="tag <?php echo e(strtolower($c['level'] ?? 'beginner') === 'beginner' ? 'tag--green' : 'tag--orange'); ?>"><?php echo e($c['level'] ?? 'All Levels'); ?></span>
            </div>
            <h3 style="margin-top:12px;font-size:1.05rem;"><?php echo e($c['title']); ?></h3>
            <p style="margin:6px 0 10px;color:var(--muted);font-weight:650;line-height:1.5;font-size:.85rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?php echo e($c['description'] ?? 'Learn with mentor guidance.'); ?></p>
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px;">
              <span class="chip" style="font-size:.72rem;"><i class="fa-regular fa-clock"></i> <?php echo e($c['duration'] ?? 'Flexible'); ?></span>
              <span class="chip" style="font-size:.72rem;"><i class="fa-solid fa-user-tie"></i> <?php echo e($c['mentors'] ?? '2+ mentors'); ?></span>
              <span class="chip" style="font-size:.72rem;"><i class="fa-solid fa-diagram-project"></i> <?php echo e($c['projects'] ?? '2+'); ?> projects</span>
            </div>
            <div style="display:flex;gap:8px;">
              <a class="btn btn--primary" href="field.php?field=<?php echo urlencode($c['field_slug'] ?? ''); ?>" style="flex:1;text-decoration:none;font-size:.82rem;">View Course</a>
              <a class="btn btn--outline" href="enrollment.php?course=<?php echo urlencode($c['title']); ?>" style="flex:1;text-decoration:none;font-size:.82rem;">Enroll</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="course-grid">
        <?php
          $fallbackCourses = [
            ['title'=>'Software Engineering Foundations','desc'=>'Build practical engineering habits.','level'=>'Intermediate','duration'=>'8 weeks','field'=>'Engineering'],
            ['title'=>'Data Science Essentials','desc'=>'From data cleaning to modeling.','level'=>'Beginner','duration'=>'6 weeks','field'=>'IT'],
            ['title'=>'Cyber Security Basics','desc'=>'Threats, defenses and secure thinking.','level'=>'Beginner','duration'=>'6 weeks','field'=>'IT'],
            ['title'=>'Cloud Computing Fundamentals','desc'=>'Deploy, scale and secure in the cloud.','level'=>'Intermediate','duration'=>'8 weeks','field'=>'IT'],
            ['title'=>'Networking for Developers','desc'=>'Understand routing, protocols, and performance.','level'=>'Intermediate','duration'=>'8 weeks','field'=>'IT'],
            ['title'=>'AI Literacy & Applications','desc'=>'Hands-on overview of modern AI workflows.','level'=>'Advanced','duration'=>'10 weeks','field'=>'Engineering'],
          ];
        ?>
        <?php foreach ($fallbackCourses as $c): ?>
          <article class="course-card reveal" data-reveal style="padding:22px;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
              <div class="avatar-photo" style="width:52px;height:52px;border-radius:16px;font-size:.95rem;">CS</div>
              <span class="pill" style="background:#eef1ff;"><?php echo e($c['field']); ?></span>
            </div>
            <h3 style="margin-top:14px;font-size:1.05rem;"><?php echo e($c['title']); ?></h3>
            <p style="margin:8px 0 12px;color:var(--muted);font-weight:650;line-height:1.6;font-size:.85rem;"><?php echo e($c['desc']); ?></p>
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px;">
              <span class="chip" style="font-size:.72rem;"><i class="fa-regular fa-clock"></i> <?php echo e($c['duration']); ?></span>
              <span class="tag <?php echo e(strtolower($c['level']) === 'beginner' ? 'tag--green' : (strtolower($c['level']) === 'advanced' ? '' : 'tag--orange')); ?>"><?php echo e($c['level']); ?></span>
            </div>
            <a class="btn btn--primary" href="course.php" style="width:100%;text-decoration:none;">Explore course</a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
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
