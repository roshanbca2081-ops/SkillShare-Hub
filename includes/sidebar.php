<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
<div class="sidebar bg-dark text-white min-vh-100 p-3">
    <h6 class="text-uppercase text-muted small px-3">Navigation</h6>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="../dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'academic' ? 'active' : ''; ?>" href="../academic/fiels.php">
                <i class="fas fa-book me-2"></i> Academic Fields
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'mentor' ? 'active' : ''; ?>" href="../mentor.php">
                <i class="fas fa-chalkboard-teacher me-2"></i> Mentors
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'sessions' ? 'active' : ''; ?>" href="../sessions/index.php">
                <i class="fas fa-calendar-alt me-2"></i> Sessions
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'learning' ? 'active' : ''; ?>" href="../learning/my-course.php">
                <i class="fas fa-book-open me-2"></i> My Courses
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'booking' ? 'active' : ''; ?>" href="../booking/index.php">
                <i class="fas fa-calendar-check me-2"></i> My Bookings
                <?php 
                // Count pending bookings
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE fresher_id = ? AND status = 'pending'");
                $stmt->execute([getUserId()]);
                $pending_bookings = $stmt->fetchColumn();
                if ($pending_bookings > 0): ?>
                    <span class="badge bg-warning"><?php echo $pending_bookings; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'assignment' ? 'active' : ''; ?>" href="../assignment/index.php">
                <i class="fas fa-tasks me-2"></i> Assignments
                <?php 
                // Count pending assignments
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM assignments a 
                                       JOIN enrollments e ON a.course_id = e.course_id 
                                       WHERE e.fresher_id = ? AND a.due_date > NOW() 
                                       AND NOT EXISTS (SELECT 1 FROM assignment_submissions s WHERE s.assignment_id = a.id AND s.fresher_id = ?)");
                $stmt->execute([getUserId(), getUserId()]);
                $pending_count = $stmt->fetchColumn();
                if ($pending_count > 0): ?>
                    <span class="badge bg-danger"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'resources' ? 'active' : ''; ?>" href="../resources/index.php">
                <i class="fas fa-folder-open me-2"></i> Resources
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'research' ? 'active' : ''; ?>" href="../research/index.php">
                <i class="fas fa-flask me-2"></i> Research
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'interview' ? 'active' : ''; ?>" href="../interview/index.php">
                <i class="fas fa-question-circle me-2"></i> Interview Prep
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'certificate' ? 'active' : ''; ?>" href="../certificate/index.php">
                <i class="fas fa-certificate me-2"></i> Certificates
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'bookmark' ? 'active' : ''; ?>" href="../bookmark.php/index.php">
                <i class="fas fa-bookmark me-2"></i> Bookmarks
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'message' ? 'active' : ''; ?>" href="../message/index.php">
                <i class="fas fa-envelope me-2"></i> Messages
                <?php 
                $unread = getUnreadMessages($pdo, getUserId());
                if ($unread > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'notification' ? 'active' : ''; ?>" href="../notification/index.php">
                <i class="fas fa-bell me-2"></i> Notifications
                <?php 
                $unread = getUnreadNotifications($pdo, getUserId());
                if ($unread > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread; ?></span>
                <?php endif; ?>
            </a>
        </li>
        
        <li><hr class="bg-secondary"></li>
        
        <!-- Account Section -->
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'account' && $current_page == 'profile.php' ? 'active' : ''; ?>" href="../account/profile.php">
                <i class="fas fa-user me-2"></i> My Profile
            </a>
        </li>
        <li class="nav-item">
             <a class="nav-link text-white <?php echo $current_dir == 'setting' ? 'active' : ''; ?>" href="../account/setting.php">
                <i class="fas fa-cog me-2"></i> Settings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'account' && $current_page == 'security.php' ? 'active' : ''; ?>" href="../account/security.php">
                <i class="fas fa-shield-alt me-2"></i> Security
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="../../logout.php">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>
