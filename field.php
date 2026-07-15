<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/field-data.php';
ensure_database_schema();

$fieldSlug = $_GET['field'] ?? '';
$field = $academicFields[$fieldSlug] ?? null;
if (!$field) {
    header('Location: index.php');
    exit;
}

$assetBase = file_exists('assets/css/style.css') ? 'assets/' : (file_exists('../assets/css/style.css') ? '../assets/' : '../../assets/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo e($field['name']); ?> | ShareSkill Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?php echo $assetBase; ?>css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="<?php echo $assetBase; ?>css/app.css" />
  <style>
    body{background:linear-gradient(135deg,#f6f7ff 0%,#ebf2ff 45%,#f7fbff 100%)}
    .field-hero{position:relative;overflow:hidden;border-radius:28px;padding:26px;background:linear-gradient(135deg,rgba(14,58,128,.95),rgba(26,91,191,.95));color:#fff;min-height:340px;display:flex;align-items:flex-end;box-shadow:0 24px 60px rgba(13,31,71,.16)}
    .field-hero::before{content:'';position:absolute;inset:0;background:url('<?php echo $field['banner']; ?>') center/cover no-repeat;opacity:.24;mix-blend-mode:screen}
    .field-hero::after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,rgba(6,15,40,.92) 10%,rgba(6,15,40,.45) 65%,transparent 100%)}
    .field-hero > *{position:relative;z-index:1}
    .field-hero .btn{background:#fff;color:var(--primary)}
    .field-shell{display:grid;gap:22px;grid-template-columns:1.15fr .85fr;margin-top:24px}
    .glass-panel{background:rgba(255,255,255,.8);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.55);border-radius:24px;padding:24px;box-shadow:0 24px 50px rgba(15,23,42,.12)}
    .pill-list{display:flex;flex-wrap:wrap;gap:10px}
    .pill-list span{display:inline-flex;align-items:center;gap:8px;border-radius:999px;padding:8px 12px;background:rgba(26,91,191,.08);color:var(--primary);font-weight:700;font-size:.8rem}
    .course-list{display:grid;gap:16px}

    .field-course-card{display:grid;grid-template-rows:auto 1fr auto;gap:10px;padding:18px;border-radius:20px;background:var(--card);border:1px solid var(--border);box-shadow:var(--shadow);transition:transform var(--transition),box-shadow var(--transition),border-color var(--transition)}
    .field-course-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-lg);border-color:rgba(79,70,229,.18)}

    .field-course-card__media{border-radius:16px;overflow:hidden;border:1px solid rgba(255,255,255,.12);background:rgba(79,70,229,.06)}
    .field-course-card__img{display:block;width:100%;height:160px;object-fit:cover;filter:saturate(1.05)}

    .field-course-card__body{display:grid;gap:10px}
    .field-course-card__top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}

    .field-course-card__actions{display:flex;justify-content:flex-end}
    .field-course-card__details{position:relative;overflow:hidden}
    .field-course-card__details .btn__ripple{position:absolute;inset:0;pointer-events:none;background:rgba(255,255,255,.18);transform:translateX(-120%) translateY(120%);transition:transform .6s ease, opacity .6s ease;opacity:0}
    .field-course-card__details:hover .btn__ripple{transform:translateX(0) translateY(0);opacity:1}

    .mentor-list{display:grid;gap:12px}
    .mentor-list article{display:flex;gap:12px;align-items:center;padding:14px;border-radius:16px;background:var(--card);border:1px solid var(--border)}
    .mentor-avatar{width:48px;height:48px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;font-weight:800}
    .testimonial-list{display:grid;gap:14px;margin-top:12px}
    .testimonial-list blockquote{margin:0;padding:16px;border-radius:16px;background:rgba(26,91,191,.06);border:1px solid rgba(26,91,191,.12);color:var(--text)}
    @media(max-width:960px){.field-shell{grid-template-columns:1fr}}
  </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container">
    <div class="field-hero">
      <div class="max-width-720">
        <div class="pill-list" style="margin-bottom:14px;">
          <span><i class="fa-solid fa-shapes"></i> Academic Focus</span>
          <span><i class="fa-solid fa-chart-line"></i> High Industry Demand</span>
        </div>
        <h1 style="font-size:clamp(2rem,3.7vw,3rem);margin-bottom:10px;"><?php echo e($field['name']); ?></h1>
        <p style="max-width:720px;color:rgba(255,255,255,.9);font-size:1rem;line-height:1.7;"><?php echo e($field['description']); ?></p>
        <div class="hero-actions" style="margin-top:18px;">
          <a class="btn" href="course.php?field=<?php echo urlencode($field['name']); ?>">Explore Courses</a>
          <a class="btn btn--outline" href="mentor.php" style="color:#fff;border-color:rgba(255,255,255,.2);">Meet Mentors</a>
        </div>
      </div>
    </div>

    <div class="field-shell">
      <div class="glass-panel">
        <div class="section-heading" style="margin-top:0;">
          <h2>Field Overview</h2>
        </div>
        <div class="meta-list">
          <span><strong>Career opportunities:</strong> <?php echo e(implode(', ', $field['opportunities'])); ?></span>
          <span><strong>Industry demand:</strong> <?php echo e($field['demand']); ?></span>
          <span><strong>Practical skills:</strong> <?php echo e(implode(', ', $field['skills'])); ?></span>
          <span><strong>Available courses:</strong> <?php echo count($field['courses']); ?></span>
        </div>
        <div class="section-heading" style="margin-top:24px;">
          <h2>Top Mentors</h2>
        </div>
        <div class="mentor-list">
          <?php foreach ($field['mentors'] as $mentor): ?>
            <article>
              <div class="mentor-avatar"><i class="fa-solid fa-user-tie"></i></div>
              <div>
                <strong><?php echo e($mentor['name']); ?></strong>
                <div class="small text-light-emphasis"><?php echo e($mentor['role']); ?></div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="glass-panel">
        <div class="section-heading" style="margin-top:0;">
          <h2>Featured Courses</h2>
        </div>
        <div class="course-list">
          <?php foreach ($field['courses'] as $course): ?>
            <article class="field-course-card">
              <div class="field-course-card__media">
                <?php if (!empty($course['image'])): ?>
                  <img
                    class="field-course-card__img"
                    src="<?php echo e($course['image']); ?>"
                    alt="<?php echo e($course['title']); ?>"
                    loading="lazy"
                  />
                <?php endif; ?>
              </div>

              <div class="field-course-card__body">
                <div class="field-course-card__top">
                  <strong><?php echo e($course['title']); ?></strong>
                  <span class="tag"><?php echo e($course['level']); ?></span>
                </div>

                <p class="small text-light-emphasis" style="margin:8px 0 12px;"><?php echo e($course['description']); ?></p>

                <div class="pill-list">
                  <span><i class="fa-solid fa-clock"></i> <?php echo e($course['duration']); ?></span>
                  <span><i class="fa-solid fa-user-tie"></i> <?php echo e($course['mentors']); ?></span>
                  <span><i class="fa-solid fa-diagram-project"></i> <?php echo e($course['projects']); ?> projects</span>
                  <span><i class="fa-solid fa-clipboard-list"></i> <?php echo e($course['assignments']); ?> assignments</span>
                  <span><i class="fa-solid fa-briefcase"></i> <?php echo e($course['placement']); ?></span>
                </div>
              </div>

              <div class="field-course-card__actions">
                <a
                  class="btn btn--primary field-course-card__details"
                  href="course.php?course=<?php echo urlencode($course['title']); ?>&field=<?php echo urlencode($field['name']); ?>"
                >
                  <span class="btn__ripple" aria-hidden="true"></span>
                  View Details
                </a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
        <div class="section-heading" style="margin-top:24px;">
          <h2>Student Testimonials</h2>
        </div>
        <div class="testimonial-list">
          <?php foreach ($field['testimonials'] as $testimonial): ?>
            <blockquote>
              “<?php echo e($testimonial['quote']); ?>”
              <div class="small" style="margin-top:8px;font-weight:700;color:var(--primary);">— <?php echo e($testimonial['name']); ?></div>
            </blockquote>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include 'includes/footer.php'; ?>
</body>
</html>
