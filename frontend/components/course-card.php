<?php
// Course Card
// Accepts optional $courses array (each: id, title, image, category, price, old_price, rating, reviews, instructor, instructor_img, lessons, duration, students)
$courses = isset($courses) && is_array($courses) ? $courses : [
    ['id' => 1, 'title' => 'Full-Stack Web Development Bootcamp', 'image' => 'frontend/assets/images/course/web-dev.svg', 'category' => 'Web Development', 'price' => 899, 'old_price' => 1299, 'rating' => 4.9, 'reviews' => 1240, 'instructor' => 'Dr. Sarah Wilson', 'instructor_img' => 'frontend/assets/images/profile/avatar-1.svg', 'lessons' => 82, 'duration' => '12 weeks', 'students' => 3200],
    ['id' => 2, 'title' => 'Data Science & Machine Learning', 'image' => 'frontend/assets/images/course/data-science.svg', 'category' => 'Data Science', 'price' => 749, 'old_price' => 999, 'rating' => 4.8, 'reviews' => 980, 'instructor' => 'Prof. James Carter', 'instructor_img' => 'frontend/assets/images/profile/avatar-2.svg', 'lessons' => 64, 'duration' => '10 weeks', 'students' => 2100],
    ['id' => 3, 'title' => 'UI/UX Design Masterclass', 'image' => 'frontend/assets/images/course/uiux.svg', 'category' => 'Design', 'price' => 599, 'old_price' => 899, 'rating' => 4.7, 'reviews' => 760, 'instructor' => 'Emily Chen', 'instructor_img' => 'frontend/assets/images/profile/avatar-3.svg', 'lessons' => 48, 'duration' => '8 weeks', 'students' => 1500],
    ['id' => 4, 'title' => 'Digital Marketing Fundamentals', 'image' => 'frontend/assets/images/course/marketing.svg', 'category' => 'Marketing', 'price' => 449, 'old_price' => 699, 'rating' => 4.6, 'reviews' => 540, 'instructor' => 'Michael Brown', 'instructor_img' => 'frontend/assets/images/profile/avatar-4.svg', 'lessons' => 36, 'duration' => '6 weeks', 'students' => 980],
    ['id' => 5, 'title' => 'Medical Research Methodology', 'image' => 'frontend/assets/images/course/medical.svg', 'category' => 'Medical', 'price' => 799, 'old_price' => 1099, 'rating' => 4.9, 'reviews' => 430, 'instructor' => 'Dr. Aisha Khan', 'instructor_img' => 'frontend/assets/images/profile/avatar-5.svg', 'lessons' => 52, 'duration' => '9 weeks', 'students' => 760],
    ['id' => 6, 'title' => 'Business Analytics & Strategy', 'image' => 'frontend/assets/images/course/business.svg', 'category' => 'Business', 'price' => 679, 'old_price' => 949, 'rating' => 4.7, 'reviews' => 620, 'instructor' => 'Robert Garcia', 'instructor_img' => 'frontend/assets/images/profile/avatar-6.svg', 'lessons' => 44, 'duration' => '8 weeks', 'students' => 1100],
];
?>
<div class="course-grid reveal">
    <?php foreach ($courses as $course): ?>
        <article class="course-card">
            <div class="course-media">
                <img src="<?php echo $course['image']; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">
                <span class="course-category"><?php echo $course['category']; ?></span>
                <span class="course-price">$<?php echo $course['price']; ?></span>
                <button class="course-wishlist" aria-label="Add to wishlist">
                    <i class="fa-regular fa-heart"></i>
                </button>
            </div>
            <div class="course-body">
                <div class="course-rating">
                    <i class="fa-solid fa-star"></i> <?php echo $course['rating']; ?>
                    <span>(<?php echo number_format($course['reviews']); ?> reviews)</span>
                </div>
                <h5><a href="courses.php?id=<?php echo $course['id']; ?>"><?php echo $course['title']; ?></a></h5>
                <div class="course-instructor">
                    <img src="<?php echo $course['instructor_img']; ?>" alt="<?php echo $course['instructor']; ?>">
                    <span><?php echo $course['instructor']; ?></span>
                </div>
                <div class="course-meta">
                    <span><i class="fa-solid fa-book-open"></i> <?php echo $course['lessons']; ?> Lessons</span>
                    <span><i class="fa-solid fa-clock"></i> <?php echo $course['duration']; ?></span>
                    <span><i class="fa-solid fa-users"></i> <?php echo number_format($course['students']); ?></span>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</div>
