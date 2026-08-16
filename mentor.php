<?php
session_start();
$page_title = 'Mentor Profile | SkillShare Hub';
$page_active = 'Mentors';
include 'config.php';
include 'frontend/components/platform-header.php';

$mentorId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pdo = getDB();

$mentor = null;
if ($mentorId) {
    $stmt = $pdo->prepare("SELECT u.id, u.full_name, u.email, u.profile_picture, u.bio, u.hourly_rate, u.address, u.city, u.state, u.country, u.date_of_birth, u.gender, u.created_at as member_since, m.specialization, m.experience_years, m.current_company, m.current_position, m.qualification, m.certifications, m.languages, m.is_verified, m.verified_at, m.rating, m.reviews_count, m.total_sessions, m.total_students, m.total_hours, m.portfolio_url, m.linkedin_url, m.github_url, m.website_url, m.youtube_url, f.name as field_name, f.icon as field_icon, f.color as field_color FROM users u JOIN mentors m ON u.id = m.user_id LEFT JOIN academic_fields f ON u.academic_field_id = f.id WHERE u.id = ? AND u.role = 'mentor' AND u.status = 'active'");
    $stmt->execute([$mentorId]);
    $mentor = $stmt->fetch();
}

if (!$mentor) {
    echo '<div class="container"><div class="research-no-results" style="padding:60px 20px;text-align:center;"><i class="fa-solid fa-user-slash" style="font-size:3rem;display:block;margin-bottom:16px;color:var(--text-muted);"></i><h2 style="color:var(--text-primary);">Mentor Not Found</h2><p style="color:var(--text-muted);">The requested mentor does not exist or is not available.</p><a href="mentors.php" style="color:var(--primary-400);text-decoration:none;">Browse all mentors</a></div></div>';
    include 'frontend/components/platform-footer.php';
    exit;
}

$mentor['reviews_list'] = $pdo->prepare("SELECT r.rating, r.comment, r.created_at, u.full_name, u.profile_picture FROM reviews r JOIN users u ON r.reviewer_id = u.id WHERE r.reviewee_id = ? AND r.is_public = 1 ORDER BY r.created_at DESC LIMIT 10")->execute([$mentorId]) ? $pdo->prepare("SELECT r.rating, r.comment, r.created_at, u.full_name, u.profile_picture FROM reviews r JOIN users u ON r.reviewer_id = u.id WHERE r.reviewee_id = ? AND r.is_public = 1 ORDER BY r.created_at DESC LIMIT 10")->fetchAll() : [];

$mentor['availability'] = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM availability WHERE user_id = ? AND is_available = 1 ORDER BY FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")->execute([$mentorId]) ? $pdo->prepare("SELECT day_of_week, start_time, end_time FROM availability WHERE user_id = ? AND is_available = 1 ORDER BY FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")->fetchAll() : [];

$mentor['courses_list'] = $pdo->prepare("SELECT c.id, c.name, c.slug, c.description, c.duration, c.level, c.rating FROM courses c WHERE c.academic_field_id = ? AND c.status = 'active' ORDER BY c.name LIMIT 10")->execute([$mentor['academic_field_id']]) ? $pdo->prepare("SELECT c.id, c.name, c.slug, c.description, c.duration, c.level, c.rating FROM courses c WHERE c.academic_field_id = ? AND c.status = 'active' ORDER BY c.name LIMIT 10")->fetchAll() : [];

$mentor['skills_list'] = $pdo->prepare("SELECT s.id, s.name, s.slug, s.description, s.category, s.difficulty FROM skills s WHERE s.course_id = ? AND s.status = 'active' ORDER BY s.name LIMIT 20")->execute([$mentor['course_id']]) ? $pdo->prepare("SELECT s.id, s.name, s.slug, s.description, s.category, s.difficulty FROM skills s WHERE s.course_id = ? AND s.status = 'active' ORDER BY s.name LIMIT 20")->fetchAll() : [];

$fullStars = floor($mentor['rating'] ?? 0);
$halfStar = ($mentor['rating'] - $fullStars) >= 0.5;
$emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
$stars = str_repeat('★', $fullStars) . ($halfStar ? '★' : '') . str_repeat('☆', $emptyStars);
$initials = '';
foreach (explode(' ', $mentor['full_name']) as $w) { $initials .= strtoupper(substr($w, 0, 1)); }
$initials = substr($initials, 0, 2);
?>

<style>
    .mentor-profile { max-width: 900px; margin: 0 auto; padding: 20px 0 60px; }
    .mentor-profile-header { display: flex; gap: 24px; align-items: flex-start; margin-bottom: 30px; }
    .mentor-profile-avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid var(--glass-border); flex-shrink: 0; }
    .mentor-profile-avatar-placeholder { width: 120px; height: 120px; border-radius: 50%; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; color: #fff; flex-shrink: 0; }
    .mentor-profile-info { flex: 1; }
    .mentor-profile-info h1 { font-family: var(--font-heading); font-weight: 700; font-size: 1.8rem; color: var(--text-primary); margin: 0 0 4px; }
    .mentor-profile-info .mp-title { color: var(--text-secondary); font-size: 1rem; margin-bottom: 8px; }
    .mentor-profile-info .mp-rating { color: #fbbf24; font-size: 1rem; margin-bottom: 8px; }
    .mentor-profile-info .mp-rating span { color: var(--text-muted); font-size: 0.85rem; }
    .mentor-profile-info .mp-meta { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 12px; }
    .mentor-profile-info .mp-meta span { color: var(--text-muted); font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; }
    .mentor-profile-info .mp-meta i { color: var(--primary-400); }
    .mentor-profile-actions { display: flex; gap: 10px; margin-top: 12px; flex-wrap: wrap; }
    .btn { padding: 10px 20px; border-radius: var(--radius-full); font-size: 0.85rem; cursor: pointer; text-decoration: none; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary { background: var(--gradient-primary); color: #fff; border: none; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-outline { background: transparent; border: 1px solid var(--glass-border); color: var(--text-secondary); }
    .btn-outline:hover { background: rgba(255,255,255,0.06); color: var(--text-primary); border-color: rgba(255,255,255,0.2); }

    .mentor-profile-section { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 20px; }
    .mentor-profile-section h3 { font-family: var(--font-heading); font-weight: 600; font-size: 1.1rem; color: var(--text-primary); margin: 0 0 16px; }
    .mentor-profile-section p { color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; }

    .availability-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; }
    .availability-item { background: rgba(255,255,255,0.04); border: 1px solid var(--glass-border); border-radius: 8px; padding: 12px; text-align: center; }
    .availability-item .day { font-weight: 600; color: var(--text-primary); font-size: 0.85rem; text-transform: capitalize; }
    .availability-item .time { color: var(--text-muted); font-size: 0.75rem; margin-top: 4px; }

    .reviews-list { display: flex; flex-direction: column; gap: 12px; }
    .review-item { background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 8px; padding: 14px; }
    .review-item .review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
    .review-item .review-author { font-weight: 600; color: var(--text-primary); font-size: 0.85rem; }
    .review-item .review-rating { color: #fbbf24; font-size: 0.8rem; }
    .review-item .review-text { color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5; }
    .review-item .review-date { color: var(--text-muted); font-size: 0.75rem; margin-top: 6px; }

    .skills-list { display: flex; flex-wrap: wrap; gap: 8px; }
    .skill-tag { padding: 6px 14px; border-radius: var(--radius-full); background: rgba(59,130,246,0.12); color: var(--primary-400); font-size: 0.8rem; }
</style>

<div class="container">
    <div class="mentor-profile reveal">
        <div class="mentor-profile-header">
            <?php if ($mentor['profile_picture']): ?>
                <img src="<?php echo BASE_URL; ?>frontend/assets/images/profile/<?php echo htmlspecialchars($mentor['profile_picture']); ?>" alt="<?php echo htmlspecialchars($mentor['full_name']); ?>" class="mentor-profile-avatar">
            <?php else: ?>
                <div class="mentor-profile-avatar-placeholder"><?php echo htmlspecialchars($initials); ?></div>
            <?php endif; ?>
            <div class="mentor-profile-info">
                <h1><?php echo htmlspecialchars($mentor['full_name']); ?> <?php if ($mentor['is_verified']): ?><i class="fa-solid fa-circle-check" style="color:var(--primary-400);font-size:1rem;" title="Verified"></i><?php endif; ?></h1>
                <div class="mp-title"><?php echo htmlspecialchars($mentor['specialization'] ?: 'Mentor'); ?> <?php echo htmlspecialchars($mentor['current_position'] ?: ''); ?></div>
                <div class="mp-rating"><?php echo $stars; ?> <span><?php echo number_format($mentor['rating'] ?? 0, 1); ?> (<?php echo (int)($mentor['reviews_count'] ?? 0); ?> reviews)</span></div>
                <div class="mp-meta">
                    <span><i class="fa-solid fa-briefcase"></i> <?php echo htmlspecialchars($mentor['current_company'] ?: 'N/A'); ?></span>
                    <span><i class="fa-solid fa-clock"></i> <?php echo htmlspecialchars($mentor['experience_years'] ? $mentor['experience_years'] . ' years' : 'N/A'); ?></span>
                    <span><i class="fa-solid fa-dollar-sign"></i> $<?php echo number_format($mentor['hourly_rate'] ?? 0, 2); ?>/hr</span>
                    <span><i class="fa-solid fa-users"></i> <?php echo (int)($mentor['total_students'] ?? 0); ?> students</span>
                </div>
                <div class="mentor-profile-actions">
                    <?php if (isLoggedIn() && getUserRole() === 'fresher'): ?>
                        <a href="<?php echo BASE_URL; ?>dashboard/fresher/bookings.php?mentor_id=<?php echo (int)$mentor['id']; ?>" class="btn btn-primary"><i class="fa-solid fa-calendar-check"></i> Book Session</a>
                    <?php elseif (!isLoggedIn()): ?>
                        <a href="<?php echo BASE_URL; ?>login.php" class="btn btn-primary"><i class="fa-solid fa-calendar-check"></i> Book Session</a>
                    <?php endif; ?>
                    <a href="mailto:<?php echo htmlspecialchars($mentor['email']); ?>" class="btn btn-outline"><i class="fa-solid fa-envelope"></i> Contact</a>
                    <?php if ($mentor['portfolio_url']): ?><a href="<?php echo htmlspecialchars($mentor['portfolio_url']); ?>" target="_blank" class="btn btn-outline"><i class="fa-solid fa-globe"></i> Portfolio</a><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mentor-profile-section">
            <h3><i class="fa-solid fa-user" style="color:var(--primary-400);margin-right:8px;"></i> About</h3>
            <p><?php echo nl2br(htmlspecialchars($mentor['bio'] ?: 'No bio available.')); ?></p>
            <?php if ($mentor['qualification']): ?><p style="margin-top:8px;"><strong>Qualification:</strong> <?php echo htmlspecialchars($mentor['qualification']); ?></p><?php endif; ?>
            <?php if ($mentor['languages']): ?><p><strong>Languages:</strong> <?php echo htmlspecialchars($mentor['languages']); ?></p><?php endif; ?>
        </div>

        <?php if (count($mentor['availability']) > 0): ?>
        <div class="mentor-profile-section">
            <h3><i class="fa-solid fa-calendar" style="color:var(--primary-400);margin-right:8px;"></i> Availability</h3>
            <div class="availability-grid">
                <?php foreach ($mentor['availability'] as $av): ?>
                <div class="availability-item">
                    <div class="day"><?php echo htmlspecialchars($av['day_of_week']); ?></div>
                    <div class="time"><?php echo htmlspecialchars($av['start_time']); ?> - <?php echo htmlspecialchars($av['end_time']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (count($mentor['skills_list']) > 0): ?>
        <div class="mentor-profile-section">
            <h3><i class="fa-solid fa-star" style="color:var(--primary-400);margin-right:8px;"></i> Skills</h3>
            <div class="skills-list">
                <?php foreach ($mentor['skills_list'] as $skill): ?>
                    <span class="skill-tag"><?php echo htmlspecialchars($skill['name']); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (count($mentor['reviews_list']) > 0): ?>
        <div class="mentor-profile-section">
            <h3><i class="fa-solid fa-comments" style="color:var(--primary-400);margin-right:8px;"></i> Reviews</h3>
            <div class="reviews-list">
                <?php foreach ($mentor['reviews_list'] as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <span class="review-author"><?php echo htmlspecialchars($review['full_name']); ?></span>
                        <span class="review-rating"><?php echo str_repeat('★', (int)$review['rating']) . str_repeat('☆', 5 - (int)$review['rating']); ?></span>
                    </div>
                    <div class="review-text"><?php echo htmlspecialchars($review['comment'] ?: 'No comment'); ?></div>
                    <div class="review-date"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isLoggedIn() && getUserRole() === 'fresher'):
            $completedSession = $pdo->prepare("SELECT s.id, s.booking_id FROM sessions s JOIN bookings b ON s.booking_id = b.id WHERE s.mentor_id = ? AND b.user_id = ? AND s.status = 'completed' ORDER BY s.created_at DESC LIMIT 1");
            $completedSession->execute([$mentorId, $userId]);
            $canReview = $completedSession->fetch();
        ?>
        <div class="mentor-profile-section">
            <h3><i class="fa-solid fa-star" style="color:var(--primary-400);margin-right:8px;"></i> Write a Review</h3>
            <?php if ($canReview): ?>
                <form id="reviewForm" onsubmit="submitReview(event, <?php echo (int)$mentor['id']; ?>, <?php echo (int)$canReview['booking_id']; ?>)">
                    <div class="form-group" style="margin-bottom:12px;">
                        <label style="color:var(--text-secondary);font-size:0.85rem;">Rating</label>
                        <select id="reviewRating" style="background:rgba(255,255,255,0.05);border:1px solid var(--glass-border);border-radius:8px;padding:8px 12px;color:var(--text-primary);">
                            <option value="5">★★★★★</option>
                            <option value="4">★★★★</option>
                            <option value="3">★★★</option>
                            <option value="2">★★</option>
                            <option value="1">★</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:12px;">
                        <label style="color:var(--text-secondary);font-size:0.85rem;">Review</label>
                        <textarea id="reviewComment" rows="3" required placeholder="Share your experience..." style="width:100%;padding:10px 14px;background:rgba(255,255,255,0.05);border:1px solid var(--glass-border);border-radius:8px;color:var(--text-primary);outline:none;resize:vertical;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Submit Review</button>
                </form>
            <?php else: ?>
                <p style="color:var(--text-muted);font-size:0.85rem;">Complete a session with this mentor to leave a review.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function submitReview(e, mentorId, bookingId) {
    e.preventDefault();
    var rating = document.getElementById('reviewRating').value;
    var comment = document.getElementById('reviewComment').value;
    SkillShare.apiFetch(BASE + 'api/reviews.php?action=create', {
        method: 'POST',
        body: JSON.stringify({ mentor_id: mentorId, booking_id: bookingId, rating: rating, comment: comment })
    }).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Review submitted', 'success'); document.getElementById('reviewForm').reset(); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
}
</script>

<?php include 'frontend/components/platform-footer.php'; ?>
