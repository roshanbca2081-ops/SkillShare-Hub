<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
ensure_database_schema();
require_login('fresher');
$fields = get_fields();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<link rel="stylesheet" href="../assets/css/premium-fields.css" />
<main class="page-shell">
  <section class="container">
    <div class="page-title">
      <div>
        <h1>Academic Fields</h1>
        <p>Choose a field of study to explore courses, mentors, and opportunities.</p>
      </div>
    </div>

    <?php if (!$fields): ?>
      <div class="card p-5 text-center animate">
        <i class="fa-solid fa-layer-group" style="font-size:3rem;color:var(--muted);margin-bottom:16px;"></i>
        <h3>No Fields Available</h3>
        <p class="text-light-emphasis">Fields are being set up. Please check back later.</p>
      </div>
    <?php else: ?>
      <!-- Search/Filter -->
      <div class="d-flex flex-wrap gap-3 mb-4 animate">
        <div class="input-icon" style="flex:1;max-width:360px;">
          <i class="fa-solid fa-search"></i>
          <input type="text" id="fieldSearch" class="form-control" placeholder="Search fields..." style="padding-left:42px;" oninput="filterFields(this.value)" />
        </div>
        <div class="d-flex gap-2 align-items-center">
          <span class="small text-light-emphasis"><?php echo count($fields); ?> fields</span>
        </div>
      </div>

      <!-- Fields Grid -->
      <div class="field-grid-modern" id="fieldsGrid">
        <?php foreach ($fields as $field):
          $slug = $field['slug'] ?? strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9 ]/', '', $field['name'])));
          $icon = !empty($field['icon']) ? $field['icon'] : get_field_icon($slug);
          $courseCount = (int)($field['course_count'] ?? 0);
          $desc = $field['description'] ?? 'Explore courses and opportunities in ' . $field['name'] . '.';
          $logoPath = $field['logo_path'] ?? null;
        ?>
          <a href="../field.php?field=<?php echo urlencode($slug); ?>" class="field-card-premium animate" style="text-decoration:none;animation-delay:<?php echo rand(0, 5) * 0.1; ?>s;">
            <div class="d-flex align-items-start gap-4">
              <div class="field-card-icon">
                <?php if ($logoPath && file_exists('../assets/images/fields/' . $logoPath)): ?>
                  <img src="../assets/images/fields/<?php echo e($logoPath); ?>" alt="<?php echo e($field['name']); ?>" />
                <?php else: ?>
                  <i class="fa-solid <?php echo e($icon); ?>"></i>
                <?php endif; ?>
              </div>
              <div style="flex:1;min-width:0;">
                <h3 class="field-card-title"><?php echo e($field['name']); ?></h3>
                <p class="field-card-desc"><?php echo e($desc); ?></p>
              </div>
            </div>
            <div class="field-card-meta">
              <span class="field-card-stat">
                <i class="fa-solid fa-book-open-reader"></i>
                <?php echo $courseCount; ?> Course<?php echo $courseCount !== 1 ? 's' : ''; ?>
              </span>
              <span class="field-card-stat">
                <i class="fa-solid fa-user-tie"></i>
                Expert Mentors
              </span>
            </div>
            <span class="field-card-action-btn">
              Explore Field <i class="fa-solid fa-arrow-right" style="font-size:0.75rem;"></i>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>

<script>
function filterFields(query) {
  const cards = document.querySelectorAll('.field-card-premium');
  const q = query.toLowerCase().trim();
  cards.forEach(card => {
    const title = card.querySelector('.field-card-title')?.textContent?.toLowerCase() || '';
    const desc = card.querySelector('.field-card-desc')?.textContent?.toLowerCase() || '';
    const match = !q || title.includes(q) || desc.includes(q);
    card.style.display = match ? '' : 'none';
  });
}
</script>

<?php include '../includes/footer.php'; ?>

