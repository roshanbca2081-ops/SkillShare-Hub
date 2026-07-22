<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
ensure_database_schema();

$fieldSlug = $_GET['field'] ?? '';
$fieldSlug = preg_replace('/[^a-z0-9\-]/i', '', (string)$fieldSlug);

// Try database first
$field = get_field_by_slug($fieldSlug);

// Fall back to static field data
if (!$field) {
    require_once 'includes/field-data.php';
    if (isset($academicFields[$fieldSlug])) {
        $staticField = $academicFields[$fieldSlug];
        // Create a compatible array structure
        $field = [
            'id' => null,
            'name' => $staticField['name'],
            'slug' => $fieldSlug,
            'icon' => $staticField['icon'] ?? 'fa-graduation-cap',
            'description' => $staticField['description'] ?? '',
            'logo_path' => null,
            'course_count' => count($staticField['courses'] ?? [])
        ];
        $staticCourses = $staticField['courses'] ?? [];
        $staticMentors = $staticField['mentors'] ?? [];
        $staticTestimonials = $staticField['testimonials'] ?? [];
        $staticDemand = $staticField['demand'] ?? '';
        $staticOpportunities = $staticField['opportunities'] ?? [];
        $staticSkills = $staticField['skills'] ?? [];
    }
}

if (!$field) {
    header('Location: index.php');
    exit;
}

// Get courses for this field
$courses = [];
if (!empty($field['id'])) {
    $courses = get_courses_by_field($field['id']);
}

$icon = !empty($field['icon']) ? $field['icon'] : get_field_icon($field['slug'] ?? '');
$courseCount = (int)($field['course_count'] ?? count($courses));
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<link rel="stylesheet" href="assets/css/premium-fields.css" />
<main class="page-shell">
  <section class="container">
    <!-- Hero Section -->
    <div class="field-detail-hero" style="background:linear-gradient(135deg,rgba(14,58,128,.95),rgba(26,91,191,.95));color:#fff;">
      <div class="field-detail-hero-bg" style="background-image:url('https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1400&q=80');"></div>
      <div class="field-detail-hero-overlay"></div>
      <div class="field-detail-content">
        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
          <span class="pill" style="background:rgba(255,255,255,.15);color:#fff;backdrop-filter:blur(8px);"><i class="fa-solid fa-shapes"></i> Academic Focus</span>
          <span class="pill" style="background:rgba(255,255,255,.15);color:#fff;backdrop-filter:blur(8px);"><i class="fa-solid fa-book-open-reader"></i> <?php echo $courseCount; ?> Courses</span>
        </div>
        <h1 class="field-detail-title"><?php echo e($field['name']); ?></h1>
        <p class="field-detail-desc"><?php echo e($field['description'] ?? 'Explore courses and opportunities in this field.'); ?></p>
        <div style="display:flex;gap:12px;margin-top:20px;flex-wrap:wrap;">
          <a class="btn" href="#courses-section" style="background:#fff;color:var(--primary);font-weight:800;"><i class="fa-solid fa-book-open-reader"></i> Browse Courses</a>
          <a class="btn btn--outline" href="#" style="color:#fff;border-color:rgba(255,255,255,.2);" onclick="document.getElementById('book-modal')?.classList.add('is-visible')"><i class="fa-solid fa-calendar-check"></i> Book Mentor</a>
        </div>
      </div>
    </div>

    <div style="display:grid;gap:28px;grid-template-columns:1.2fr .8fr;">
      <!-- Left Column: Courses -->
      <div>
        <div class="section-heading" id="courses-section">
          <h2><i class="fa-solid fa-book-open-reader"></i> Available Courses (<?php echo count($courses); ?>)</h2>
        </div>

        <?php if (empty($courses) && empty($staticCourses)): ?>
          <div class="card p-4 text-center">
            <i class="fa-solid fa-graduation-cap" style="font-size:2.5rem;color:var(--muted);margin-bottom:12px;"></i>
            <h3>No Courses Yet</h3>
            <p class="text-light-emphasis">Courses for this field are being added. Check back soon.</p>
          </div>
        <?php else: ?>
          <div style="display:grid;gap:20px;">
            <?php
            // Use DB courses first, then fallback to static
            $displayCourses = !empty($courses) ? $courses : ($staticCourses ?? []);
            foreach ($displayCourses as $course):
              if (is_array($course) && isset($course['title'])):
            ?>
              <div class="course-card-premium">
                <div style="display:grid;grid-template-columns:280px 1fr;gap:0;">
                  <div style="overflow:hidden;">
                    <?php if (!empty($course['image_path']) && file_exists('assets/images/courses/' . $course['image_path'])): ?>
                      <img src="assets/images/courses/<?php echo e($course['image_path']); ?>" alt="<?php echo e($course['title']); ?>" class="course-card-image" style="height:100%;min-height:180px;" />
                    <?php elseif (!empty($course['image'])): ?>
                      <img src="<?php echo e($course['image']); ?>" alt="<?php echo e($course['title']); ?>" class="course-card-image" style="height:100%;min-height:180px;" />
                    <?php else: ?>
                      <div style="height:100%;min-height:180px;background:linear-gradient(135deg,rgba(26,91,191,.06),rgba(26,91,191,.02));display:grid;place-items:center;">
                        <i class="fa-solid fa-graduation-cap" style="font-size:2.5rem;color:var(--muted);"></i>
                      </div>
                    <?php endif; ?>
                  </div>
                  <div>
                    <div class="course-card-body">
                      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                        <h3 class="course-card-title"><?php echo e($course['title']); ?></h3>
                        <span class="tag"><?php echo e($course['difficulty_level'] ?? $course['level'] ?? 'All Levels'); ?></span>
                      </div>
                      <p class="course-card-desc"><?php echo e($course['description'] ?? ''); ?></p>
                      <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <span class="chip"><i class="fa-solid fa-clock"></i> <?php echo e($course['duration'] ?? 'Flexible'); ?></span>
                        <?php if (!empty($course['rating']) && $course['rating'] > 0): ?>
                          <span class="chip" style="color:#f59e0b;">
                            <i class="fa-solid fa-star"></i> <?php echo e($course['rating']); ?>
                          </span>
                        <?php endif; ?>
                        <?php if (!empty($course['enrolled_students'])): ?>
                          <span class="chip"><i class="fa-solid fa-users"></i> <?php echo e($course['enrolled_students']); ?> enrolled</span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="course-card-footer">
                      <div class="course-card-mentor">
                        <?php if (!empty($course['mentor_name'])): ?>
                          <div class="course-card-mentor-avatar">
                            <?php echo strtoupper(substr($course['mentor_name'], 0, 1)); ?>
                          </div>
                          <span class="course-card-mentor-name"><?php echo e($course['mentor_name']); ?></span>
                        <?php else: ?>
                          <span class="small text-light-emphasis">Multiple mentors available</span>
                        <?php endif; ?>
                      </div>
                      <div style="display:flex;gap:8px;">
                        <a class="btn btn--primary btn-sm" href="enrollment.php?course=<?php echo urlencode($course['title']); ?>"><i class="fa-solid fa-eye"></i> View Details</a>
                        <button class="btn btn--outline btn-sm" onclick="openBookModal('<?php echo e($course['title']); ?>', '<?php echo e($course['mentor_name'] ?? ''); ?>')"><i class="fa-solid fa-calendar-check"></i> Book Mentor</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php
              endif;
            endforeach;
            ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Right Column: Info Panel -->
      <div>
        <!-- Stats Card -->
        <div class="glass-panel" style="background:rgba(255,255,255,.8);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.55);border-radius:24px;padding:24px;box-shadow:0 24px 50px rgba(15,23,42,.12);margin-bottom:24px;">
          <div class="section-heading" style="margin-top:0;">
            <h2>Field Overview</h2>
          </div>
          <div class="meta-list">
            <span><strong>Field:</strong> <?php echo e($field['name']); ?></span>
            <span><strong>Available Courses:</strong> <?php echo $courseCount; ?></span>
            <span><strong>Expert Mentors:</strong> Available</span>
            <span><strong>Career Opportunities:</strong> High demand field</span>
          </div>
          <?php if (!empty($staticOpportunities)): ?>
            <div style="margin-top:16px;">
              <strong class="small">Career Paths:</strong>
              <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;">
                <?php foreach ($staticOpportunities as $opp): ?>
                  <span class="chip"><i class="fa-solid fa-briefcase"></i> <?php echo e($opp); ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
          <?php if (!empty($staticSkills)): ?>
            <div style="margin-top:16px;">
              <strong class="small">Key Skills:</strong>
              <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;">
                <?php foreach ($staticSkills as $skill): ?>
                  <span class="tag"><?php echo e($skill); ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- Mentors Card -->
        <div class="glass-panel" style="background:rgba(255,255,255,.8);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.55);border-radius:24px;padding:24px;box-shadow:0 24px 50px rgba(15,23,42,.12);margin-bottom:24px;">
          <div class="section-heading" style="margin-top:0;">
            <h2>Top Mentors</h2>
          </div>
          <?php if (!empty($staticMentors)): ?>
            <div style="display:grid;gap:12px;">
              <?php foreach ($staticMentors as $mentor): ?>
                <article style="display:flex;gap:12px;align-items:center;padding:14px;border-radius:16px;background:var(--card);border:1px solid var(--border);">
                  <div style="width:48px;height:48px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;font-weight:800;">
                    <?php echo strtoupper(substr($mentor['name'], 0, 1)); ?>
                  </div>
                  <div>
                    <strong><?php echo e($mentor['name']); ?></strong>
                    <div class="small text-light-emphasis"><?php echo e($mentor['role'] ?? 'Expert Mentor'); ?></div>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-light-emphasis small">Mentor profiles coming soon.</p>
          <?php endif; ?>
          <div style="margin-top:16px;">
            <button class="btn btn--outline w-100" onclick="openBookModal('<?php echo e($field['name']); ?>', '')">
              <i class="fa-solid fa-calendar-check"></i> Book a Mentor
            </button>
          </div>
        </div>

        <!-- Testimonials -->
        <?php if (!empty($staticTestimonials)): ?>
          <div class="glass-panel" style="background:rgba(255,255,255,.8);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.55);border-radius:24px;padding:24px;box-shadow:0 24px 50px rgba(15,23,42,.12);">
            <div class="section-heading" style="margin-top:0;">
              <h2>Student Testimonials</h2>
            </div>
            <div style="display:grid;gap:14px;">
              <?php foreach ($staticTestimonials as $testimonial): ?>
                <blockquote style="margin:0;padding:16px;border-radius:16px;background:rgba(26,91,191,.06);border:1px solid rgba(26,91,191,.12);color:var(--text);">
                  &ldquo;<?php echo e($testimonial['quote']); ?>&rdquo;
                  <div class="small" style="margin-top:8px;font-weight:700;color:var(--primary);">&mdash; <?php echo e($testimonial['name']); ?></div>
                </blockquote>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<!-- Book Mentor Modal -->
<div class="modal-overlay" id="book-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(8px);z-index:2000;display:grid;place-items:center;padding:20px;opacity:0;visibility:hidden;transition:all .3s ease;">
  <div class="modal-content" style="background:#fff;border-radius:24px;padding:32px;max-width:500px;width:100%;box-shadow:0 30px 60px rgba(0,0,0,.3);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
      <h3 style="margin:0;">Book a Mentor</h3>
      <button class="btn btn--ghost btn-sm" onclick="closeBookModal()" style="border:0;background:transparent;font-size:1.5rem;cursor:pointer;">&times;</button>
    </div>
    <form method="post" action="fresher/mentorship/book.php">
      <input type="hidden" name="course" id="book-course" value="" />
      <div class="mb-3">
        <label class="form-label">Course / Field</label>
        <input type="text" class="form-control" id="book-course-display" readonly style="background:#f5f7fa;" />
      </div>
      <div class="mb-3">
        <label class="form-label">Mentor</label>
        <input type="text" class="form-control" id="book-mentor-display" readonly style="background:#f5f7fa;" />
      </div>
      <div class="mb-3">
        <label class="form-label">Your Message</label>
        <textarea name="topic" class="form-control" rows="3" placeholder="What would you like to learn?" required></textarea>
      </div>
      <button type="submit" class="btn btn--primary w-100"><i class="fa-solid fa-paper-plane"></i> Send Request</button>
    </form>
  </div>
</div>

<style>
.modal-overlay.is-visible {
  opacity: 1 !important;
  visibility: visible !important;
}
.section-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
  margin-top: 24px;
}
.section-heading h2 {
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--ink);
  margin: 0;
}
.glass-panel {
  background: rgba(255,255,255,.8);
  backdrop-filter: blur(18px);
  border: 1px solid rgba(255,255,255,.55);
  border-radius: 24px;
  padding: 24px;
  box-shadow: 0 24px 50px rgba(15,23,42,.12);
}
.meta-list {
  display: grid;
  gap: 10px;
  color: var(--text);
  font-size: .92rem;
}
.chip {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: .78rem;
  font-weight: 700;
  color: var(--text-secondary);
  background: rgba(26,91,191,.06);
}
</style>

<script>
function openBookModal(course, mentor) {
  const modal = document.getElementById('book-modal');
  document.getElementById('book-course').value = course;
  document.getElementById('book-course-display').value = course;
  document.getElementById('book-mentor-display').value = mentor || 'Any available mentor';
  modal.classList.add('is-visible');
}

function closeBookModal() {
  document.getElementById('book-modal').classList.remove('is-visible');
}

// Close modal on overlay click
document.getElementById('book-modal')?.addEventListener('click', function(e) {
  if (e.target === this) closeBookModal();
});
</script>

<?php include 'includes/footer.php'; ?>

