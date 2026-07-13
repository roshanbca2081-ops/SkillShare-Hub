<?php include 'config/config.php'; include 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings | ShareSkill Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand fw-bold" href="dashboard.php"><i class="fa-solid fa-graduation-cap me-2"></i>ShareSkill Hub</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto gap-2">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link active" href="settings.php">Settings</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <section class="section">
    <div class="container">
      <div class="card p-4 animate">
        <h3 class="mb-3">Account Settings</h3>
        <p class="text-light-emphasis">Update your profile, notifications, and privacy preferences.</p>
      </div>
    </div>
  </section>
</body>
</html>
