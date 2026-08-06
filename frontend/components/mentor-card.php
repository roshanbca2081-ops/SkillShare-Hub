<?php
// Mentor Cards
// Accepts optional $mentors array (each: name, title, image, rating, reviews, skills, students, courses, verified)
$mentors = isset($mentors) && is_array($mentors) ? $mentors : [
    ['name' => 'Dr. Sarah Wilson', 'title' => 'Senior Web Architect', 'image' => 'frontend/assets/images/mentor/mentor-1.svg', 'rating' => 4.9, 'reviews' => 320, 'skills' => ['PHP', 'Laravel', 'React'], 'students' => 3200, 'courses' => 12, 'verified' => true],
    ['name' => 'Prof. James Carter', 'title' => 'Data Science Expert', 'image' => 'frontend/assets/images/mentor/mentor-2.svg', 'rating' => 4.8, 'reviews' => 280, 'skills' => ['Python', 'ML', 'SQL'], 'students' => 2100, 'courses' => 9, 'verified' => true],
    ['name' => 'Emily Chen', 'title' => 'UX/UI Design Lead', 'image' => 'frontend/assets/images/mentor/mentor-3.svg', 'rating' => 4.7, 'reviews' => 210, 'skills' => ['Figma', 'Sketch', 'Adobe XD'], 'students' => 1500, 'courses' => 7, 'verified' => true],
    ['name' => 'Michael Brown', 'title' => 'Digital Marketing Strategist', 'image' => 'frontend/assets/images/mentor/mentor-4.svg', 'rating' => 4.6, 'reviews' => 180, 'skills' => ['SEO', 'Ads', 'Analytics'], 'students' => 980, 'courses' => 5, 'verified' => true],
    ['name' => 'Dr. Aisha Khan', 'title' => 'Medical Researcher', 'image' => 'frontend/assets/images/mentor/mentor-5.svg', 'rating' => 4.9, 'reviews' => 150, 'skills' => ['Research', 'Biotech', 'Statistics'], 'students' => 760, 'courses' => 6, 'verified' => true],
    ['name' => 'Robert Garcia', 'title' => 'Business Analyst', 'image' => 'frontend/assets/images/mentor/mentor-6.svg', 'rating' => 4.7, 'reviews' => 130, 'skills' => ['Finance', 'Strategy', 'Excel'], 'students' => 1100, 'courses' => 4, 'verified' => true],
];
?>
<div class="grid-3 reveal">
    <?php foreach ($mentors as $mentor): ?>
        <div class="mentor-card">
            <div class="mentor-cover"></div>
            <div class="mentor-avatar">
                <img src="<?php echo $mentor['image']; ?>" alt="<?php echo $mentor['name']; ?>">
            </div>
            <div class="mentor-body">
                <h5>
                    <?php echo $mentor['name']; ?>
                    <?php if ($mentor['verified']): ?>
                        <i class="fa-solid fa-circle-check mentor-verify" title="Verified"></i>
                    <?php endif; ?>
                </h5>
                <p class="mentor-title"><?php echo $mentor['title']; ?></p>
                <div class="mentor-rating">
                    <i class="fa-solid fa-star"></i> <?php echo $mentor['rating']; ?>
                    <span>(<?php echo $mentor['reviews']; ?> reviews)</span>
                </div>
                <div class="mentor-skills">
                    <?php foreach ($mentor['skills'] as $skill): ?>
                        <span><?php echo $skill; ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="mentor-stats">
                <div class="ms-box">
                    <h6><?php echo number_format($mentor['students']); ?></h6>
                    <p>Students</p>
                </div>
                <div class="ms-box">
                    <h6><?php echo $mentor['courses']; ?>+</h6>
                    <p>Courses</p>
                </div>
            </div>
            <div class="mentor-actions">
                <a href="mentor.php?id=<?php echo urlencode($mentor['name']); ?>" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-user-tie"></i> View Profile
                </a>
                <a href="contact.php" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-message"></i> Message
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
