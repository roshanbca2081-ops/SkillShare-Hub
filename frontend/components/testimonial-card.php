<?php
// Testimonial Cards
$testimonials = isset($testimonials) && is_array($testimonials) ? $testimonials : [
    ['name' => 'Sarah Johnson', 'role' => 'CS Fresher', 'image' => 'frontend/assets/images/profile/avatar-1.svg', 'rating' => 5, 'text' => 'The mentorship program completely transformed my career. My mentor guided me every step of the way, and I landed my dream job within 4 months!'],
    ['name' => 'David Lee', 'role' => 'Medical Student', 'image' => 'frontend/assets/images/profile/avatar-2.svg', 'rating' => 5, 'text' => 'The research guidance I received was invaluable. My first paper got published in a top journal thanks to the expert feedback from my mentor.'],
    ['name' => 'Amina Patel', 'role' => 'Business Fresher', 'image' => 'frontend/assets/images/profile/avatar-3.svg', 'rating' => 5, 'text' => 'SkillShare Hub made learning accessible and practical. The courses are well-structured and the community support is amazing. Highly recommended!'],
    ['name' => 'James Wilson', 'role' => 'Design Student', 'image' => 'frontend/assets/images/profile/avatar-4.svg', 'rating' => 4, 'text' => 'Booking sessions with mentors is so easy. I love the flexibility and the quality of the feedback. My portfolio has grown tremendously.'],
];
?>
<section class="section-padding" id="testimonials" style="background:var(--gray-100);">
    <div class="container-max">
        <div class="section-heading reveal">
            <span class="eyebrow"><i class="fa-solid fa-quote-left"></i> Testimonials</span>
            <h2>What Our <span class="text-gradient">Community Says</span></h2>
            <p>Real experiences from freshers, students, and mentors who are part of our growing family.</p>
        </div>

        <div class="grid-2 reveal">
            <?php foreach ($testimonials as $t): ?>
                <div class="testimonial-card card-base">
                    <div class="testimonial-quote"><i class="fa-solid fa-quote-left"></i></div>
                    <div class="testimonial-stars">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <i class="fa-solid fa-star <?php echo $i < $t['rating'] ? 'active' : ''; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="testimonial-text">"<?php echo $t['text']; ?>"</p>
                    <div class="testimonial-author">
                        <img src="<?php echo $t['image']; ?>" alt="<?php echo $t['name']; ?>">
                        <div>
                            <h6><?php echo $t['name']; ?></h6>
                            <p><?php echo $t['role']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    .testimonial-card { padding: var(--spacing-6); position: relative; }
    .testimonial-quote { position: absolute; top: var(--spacing-5); right: var(--spacing-5); font-size: 3rem; color: var(--primary-soft); }
    .testimonial-stars { display: flex; gap: 0.2rem; margin-bottom: var(--spacing-4); color: var(--gray-300); }
    .testimonial-stars i.active { color: var(--warning); }
    .testimonial-text { color: var(--gray-600); font-size: var(--font-size-base); font-style: italic; margin-bottom: var(--spacing-5); }
    .testimonial-author { display: flex; align-items: center; gap: var(--spacing-4); }
    .testimonial-author img { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; }
    .testimonial-author h6 { margin: 0; }
    .testimonial-author p { margin: 0; font-size: var(--font-size-sm); color: var(--gray-500); }
</style>
