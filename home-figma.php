<?php
session_start();
require_once __DIR__ . '/config.php';

$pdo = getDB();
$fieldCount = (int)$pdo->query("SELECT COUNT(*) FROM academic_fields WHERE status = 'active'")->fetchColumn();
$mentorCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'mentor' AND status = 'active'")->fetchColumn();
$sessionCount = (int)$pdo->query("SELECT COUNT(*) FROM sessions")->fetchColumn();
$courseCount = (int)$pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active'")->fetchColumn();
$fields = $pdo->query("SELECT id, name, icon, description, total_courses FROM academic_fields WHERE status = 'active' ORDER BY sort_order, name ASC LIMIT 6")->fetchAll();
$courses = $pdo->query("SELECT c.id, c.name, c.slug, c.icon, c.description, c.duration, f.name AS field_name FROM courses c JOIN academic_fields f ON f.id = c.academic_field_id WHERE c.status = 'active' ORDER BY c.rating DESC, c.name ASC LIMIT 4")->fetchAll();
$mentors = $pdo->query("SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) AS full_name, u.profile_picture, u.bio, u.hourly_rate, m.rating, m.specialization, m.experience_years FROM users u JOIN mentors m ON u.id = m.user_id WHERE u.role = 'mentor' AND u.status = 'active' AND m.is_verified = 1 ORDER BY m.rating DESC LIMIT 3")->fetchAll();

function homeIcon($icon, $fallback = 'fa-layer-group') {
    return htmlspecialchars($icon ?: $fallback, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillShare Hub | Learn with people who have been there</title>
    <meta name="description" content="Find practical mentorship, courses, and community for your next academic or career milestone.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --ink: #0b0f14;
            --ink-soft: #121922;
            --ink-card: #18222d;
            --line: rgba(220, 237, 245, .13);
            --text: #f4f7f5;
            --muted: #a5b2b8;
            --lime: #c4f26b;
            --lime-dark: #9fd03d;
            --cyan: #83dbe5;
            --orange: #ffb454;
            --radius: 18px;
            --display: 'Space Grotesk', sans-serif;
            --body: 'Manrope', sans-serif;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; color: var(--text); background: var(--ink); font-family: var(--body); line-height: 1.55; }
        a { color: inherit; text-decoration: none; }
        .site-shell { overflow: hidden; background: radial-gradient(circle at 80% 0%, rgba(132, 219, 229, .10), transparent 30rem), var(--ink); }
        .topbar { max-width: 1240px; margin: auto; padding: 22px 28px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
        .brand { display: inline-flex; align-items: center; gap: 10px; font: 700 1.15rem var(--display); letter-spacing: -.03em; }
        .brand-mark { width: 34px; height: 34px; display: grid; place-items: center; color: var(--ink); background: var(--lime); border-radius: 10px; transform: rotate(-6deg); }
        .brand span { color: var(--lime); }
        .nav-links { display: flex; align-items: center; gap: 26px; color: var(--muted); font-size: .88rem; }
        .nav-links a:hover { color: var(--text); }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .button { display: inline-flex; align-items: center; justify-content: center; gap: 9px; min-height: 44px; padding: 0 18px; border: 1px solid transparent; border-radius: 999px; font-weight: 800; font-size: .84rem; transition: transform .2s ease, background .2s ease, border-color .2s ease; }
        .button:hover { transform: translateY(-2px); }
        .button-primary { color: var(--ink); background: var(--lime); }
        .button-primary:hover { background: #d7ff89; }
        .button-quiet { border-color: var(--line); color: var(--text); }
        .button-quiet:hover { background: rgba(255,255,255,.06); }
        .hero { max-width: 1240px; margin: auto; padding: 84px 28px 76px; display: grid; grid-template-columns: minmax(0, 1.02fr) minmax(360px, .98fr); align-items: center; gap: 72px; }
        .eyebrow { display: inline-flex; align-items: center; gap: 9px; color: var(--lime); font-size: .78rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .eyebrow i { font-size: .7rem; }
        h1, h2, h3, p { margin-top: 0; }
        h1, h2, h3 { font-family: var(--display); letter-spacing: -.055em; line-height: 1.02; }
        h1 { max-width: 720px; margin: 20px 0 24px; font-size: clamp(3.4rem, 7vw, 6.9rem); font-weight: 600; }
        h1 em { color: var(--lime); font-style: normal; }
        .hero-copy { max-width: 580px; color: var(--muted); font-size: 1.05rem; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 30px; }
        .hero-note { display: flex; align-items: center; gap: 10px; margin-top: 22px; color: var(--muted); font-size: .78rem; }
        .avatar-stack { display: flex; }
        .avatar-stack span { width: 28px; height: 28px; margin-right: -7px; display: grid; place-items: center; border: 2px solid var(--ink); border-radius: 50%; color: var(--ink); background: var(--cyan); font-size: .65rem; font-weight: 900; }
        .avatar-stack span:nth-child(2) { background: var(--orange); }
        .avatar-stack span:nth-child(3) { background: var(--lime); }
        .hero-art { min-height: 480px; position: relative; display: grid; place-items: center; }
        .hero-art::before { content: ''; position: absolute; width: 410px; height: 410px; border-radius: 50%; background: var(--lime); opacity: .9; box-shadow: 0 0 120px rgba(196,242,107,.15); }
        .art-panel { position: relative; width: min(100%, 460px); min-height: 410px; padding: 22px; border: 1px solid rgba(11,15,20,.15); border-radius: 32px; color: var(--ink); background: linear-gradient(145deg, rgba(255,255,255,.55), rgba(255,255,255,.18)); box-shadow: 28px 32px 0 rgba(11,15,20,.28); transform: rotate(3deg); backdrop-filter: blur(10px); }
        .art-top { display: flex; align-items: center; justify-content: space-between; font-size: .76rem; font-weight: 800; }
        .art-dots { display: flex; gap: 5px; }
        .art-dots i { width: 7px; height: 7px; border-radius: 50%; background: rgba(11,15,20,.45); }
        .art-title { max-width: 300px; margin: 70px 0 28px; font: 600 2.8rem/1 var(--display); letter-spacing: -.07em; }
        .art-title span { color: #4c8f9c; }
        .art-tags { display: flex; flex-wrap: wrap; gap: 8px; }
        .art-tags span { padding: 7px 11px; border: 1px solid rgba(11,15,20,.2); border-radius: 999px; font-size: .72rem; font-weight: 800; }
        .art-card { position: absolute; right: -12px; bottom: 22px; width: 210px; padding: 14px; border: 1px solid var(--line); border-radius: 16px; color: var(--text); background: var(--ink-card); transform: rotate(-7deg); box-shadow: 0 18px 40px rgba(11,15,20,.28); }
        .art-card small { color: var(--muted); font-size: .68rem; }
        .art-card strong { display: block; margin: 5px 0 10px; font: 600 1.15rem var(--display); }
        .progress { height: 7px; overflow: hidden; border-radius: 99px; background: rgba(255,255,255,.12); }
        .progress span { display: block; width: 76%; height: 100%; border-radius: inherit; background: var(--lime); }
        .stats { max-width: 1240px; margin: auto; padding: 0 28px 92px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px; }
        .stat { padding: 24px 18px; border-top: 1px solid var(--line); }
        .stat strong { display: block; color: var(--lime); font: 600 2.2rem var(--display); }
        .stat span { color: var(--muted); font-size: .8rem; }
        .section { max-width: 1240px; margin: auto; padding: 78px 28px; }
        .section-heading { display: flex; align-items: end; justify-content: space-between; gap: 24px; margin-bottom: 34px; }
        .section-heading h2 { max-width: 480px; margin: 13px 0 0; font-size: clamp(2.3rem, 4vw, 4rem); font-weight: 600; }
        .section-heading p { max-width: 330px; margin: 0; color: var(--muted); font-size: .9rem; }
        .field-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .field { min-height: 190px; padding: 23px; display: flex; flex-direction: column; justify-content: space-between; border: 1px solid var(--line); border-radius: var(--radius); background: rgba(255,255,255,.025); transition: transform .2s ease, border-color .2s ease; }
        .field:hover { transform: translateY(-4px); border-color: rgba(196,242,107,.55); }
        .field-icon { color: var(--lime); font-size: 1.45rem; }
        .field h3 { margin: 24px 0 5px; font-size: 1.25rem; letter-spacing: -.035em; }
        .field p { margin: 0; color: var(--muted); font-size: .78rem; }
        .field-count { color: var(--cyan); font-size: .72rem; font-weight: 800; }
        .course-grid, .mentor-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .course, .mentor { border: 1px solid var(--line); border-radius: var(--radius); background: var(--ink-soft); overflow: hidden; }
        .course { padding: 20px; min-height: 215px; display: flex; flex-direction: column; }
        .course-icon { color: var(--orange); font-size: 1.35rem; }
        .course h3 { margin: 42px 0 7px; font-size: 1.2rem; }
        .course p { margin: 0 0 17px; color: var(--muted); font-size: .78rem; }
        .course-meta { margin-top: auto; display: flex; justify-content: space-between; color: var(--cyan); font-size: .7rem; font-weight: 800; }
        .mentor { padding: 19px; }
        .mentor-head { display: flex; align-items: center; gap: 12px; }
        .mentor-avatar { width: 48px; height: 48px; display: grid; place-items: center; overflow: hidden; border-radius: 14px; color: var(--ink); background: var(--lime); font: 700 1rem var(--display); }
        .mentor-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .mentor h3 { margin: 22px 0 3px; font-size: 1.1rem; }
        .mentor small { color: var(--muted); font-size: .75rem; }
        .mentor-rating { margin-top: 20px; color: var(--orange); font-size: .75rem; }
        .mentor-rating span { color: var(--muted); }
        .feature-band { max-width: 1240px; margin: 60px auto; padding: 70px 28px; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }
        .feature-list { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        .feature-item { padding-top: 20px; border-top: 2px solid var(--lime); }
        .feature-item i { color: var(--lime); font-size: 1.3rem; }
        .feature-item h3 { margin: 28px 0 8px; font-size: 1.25rem; }
        .feature-item p { margin: 0; color: var(--muted); font-size: .82rem; }
        .cta { max-width: 1184px; margin: 30px auto 80px; padding: 68px 28px; position: relative; overflow: hidden; border-radius: 24px; color: var(--ink); background: var(--lime); text-align: center; }
        .cta::after { content: ''; position: absolute; width: 240px; height: 240px; right: -60px; top: -90px; border: 30px solid rgba(11,15,20,.08); border-radius: 50%; }
        .cta h2 { position: relative; z-index: 1; margin: 0 auto 20px; max-width: 700px; font-size: clamp(2.3rem, 5vw, 4.7rem); font-weight: 600; }
        .cta p { position: relative; z-index: 1; max-width: 500px; margin: auto; color: rgba(11,15,20,.7); font-size: .95rem; }
        .cta .button { position: relative; z-index: 1; margin-top: 25px; color: var(--text); background: var(--ink); }
        .site-footer { max-width: 1240px; margin: auto; padding: 0 28px 28px; display: flex; align-items: center; justify-content: space-between; gap: 20px; color: var(--muted); font-size: .75rem; }
        .site-footer nav { display: flex; gap: 18px; flex-wrap: wrap; }
        .site-footer a:hover { color: var(--text); }
        @media (max-width: 900px) { .nav-links { display: none; } .hero { grid-template-columns: 1fr; padding-top: 54px; } .hero-art { min-height: 410px; } .course-grid, .mentor-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 620px) { .topbar, .hero, .stats, .section, .feature-band, .site-footer { padding-left: 18px; padding-right: 18px; } .nav-actions .button-quiet { display: none; } h1 { font-size: clamp(3rem, 16vw, 5rem); } .hero-art { min-height: 350px; } .hero-art::before { width: 280px; height: 280px; } .art-panel { min-height: 310px; } .art-title { margin-top: 45px; font-size: 2.1rem; } .art-card { right: -4px; bottom: 0; width: 180px; } .stats { grid-template-columns: repeat(2, 1fr); padding-bottom: 35px; } .section-heading { display: block; } .section-heading p { margin-top: 18px; } .field-grid, .course-grid, .mentor-grid, .feature-list { grid-template-columns: 1fr; } .site-footer { display: block; } .site-footer nav { margin-top: 18px; } }
    </style>
</head>
<body>
<div class="site-shell">
    <header class="topbar">
        <a class="brand" href="index.php"><span class="brand-mark"><i class="fa-solid fa-arrow-up-right-dots"></i></span>SkillShare <span>Hub</span></a>
        <nav class="nav-links" aria-label="Main navigation">
            <a href="index.php">Home</a><a href="courses.php">Courses</a><a href="mentors.php">Mentors</a><a href="research.php">Research</a><a href="about.php">About</a>
        </nav>
        <div class="nav-actions"><a class="button button-quiet" href="login.php">Log in</a><a class="button button-primary" href="register.php">Join free <i class="fa-solid fa-arrow-right"></i></a></div>
    </header>

    <main>
        <section class="hero">
            <div>
                <div class="eyebrow"><i class="fa-solid fa-sparkles"></i> India's peer mentorship platform</div>
                <h1>Learn from people who have <em>been there.</em></h1>
                <p class="hero-copy">Find the right mentor, build practical skills, and move through your academic journey with a community that understands the next step.</p>
                <div class="hero-actions"><a class="button button-primary" href="register.php">Start learning <i class="fa-solid fa-arrow-right"></i></a><a class="button button-quiet" href="mentors.php">Find a mentor</a></div>
                <div class="hero-note"><div class="avatar-stack"><span>AK</span><span>JM</span><span>PS</span></div> Trusted by students building what comes next</div>
            </div>
            <div class="hero-art" aria-label="Mentorship progress preview">
                <div class="art-panel"><div class="art-top"><span>YOUR NEXT CHAPTER</span><div class="art-dots"><i></i><i></i><i></i></div></div><div class="art-title">Make the gap between <span>study</span> and work smaller.</div><div class="art-tags"><span>Mentorship</span><span>Projects</span><span>Research</span></div><div class="art-card"><small>WEEKLY PROGRESS</small><strong>Keep going, Alex.</strong><div class="progress"><span></span></div></div></div>
            </div>
        </section>

        <section class="stats" aria-label="Platform statistics"><div class="stat"><strong><?php echo number_format($mentorCount); ?>+</strong><span>active mentors</span></div><div class="stat"><strong><?php echo number_format($courseCount); ?>+</strong><span>courses to explore</span></div><div class="stat"><strong><?php echo number_format($sessionCount); ?>+</strong><span>sessions tracked</span></div><div class="stat"><strong><?php echo number_format($fieldCount); ?></strong><span>academic fields</span></div></section>

        <section class="section" id="fields"><div class="section-heading"><div><div class="eyebrow">Start with what interests you</div><h2>Your field. Your direction.</h2></div><p>Explore live academic data, then follow a field into its courses, skills, and mentors.</p></div><div class="field-grid"><?php foreach ($fields as $field): ?><a class="field" href="courses.php?field_id=<?php echo (int)$field['id']; ?>"><div><div class="field-icon"><i class="fa-solid <?php echo homeIcon($field['icon']); ?>"></i></div><h3><?php echo htmlspecialchars($field['name']); ?></h3><p><?php echo htmlspecialchars($field['description'] ?: 'Explore courses and mentors in this field.'); ?></p></div><span class="field-count"><?php echo (int)$field['total_courses']; ?> courses <i class="fa-solid fa-arrow-up-right-from-square"></i></span></a><?php endforeach; ?></div></section>

        <section class="section"><div class="section-heading"><div><div class="eyebrow">Popular right now</div><h2>Small steps. Real momentum.</h2></div><a class="button button-quiet" href="courses.php">Browse all courses <i class="fa-solid fa-arrow-right"></i></a></div><div class="course-grid"><?php foreach ($courses as $course): ?><a class="course" href="course-detail.php?id=<?php echo (int)$course['id']; ?>"><div class="course-icon"><i class="fa-solid <?php echo homeIcon($course['icon'], 'fa-graduation-cap'); ?>"></i></div><h3><?php echo htmlspecialchars($course['name']); ?></h3><p><?php echo htmlspecialchars($course['description'] ?: 'Build a practical foundation with guided learning.'); ?></p><div class="course-meta"><span><?php echo htmlspecialchars($course['field_name']); ?></span><span><?php echo htmlspecialchars($course['duration'] ?: 'Explore'); ?></span></div></a><?php endforeach; ?></div></section>

        <section class="feature-band"><div class="section-heading"><div><div class="eyebrow">More than a course catalog</div><h2>Everything moves with you.</h2></div><p>Keep the same place for the people, practice, and proof that help you grow.</p></div><div class="feature-list"><div class="feature-item"><i class="fa-solid fa-people-arrows"></i><h3>Human guidance</h3><p>Ask someone who has already crossed the bridge you are standing in front of.</p></div><div class="feature-item"><i class="fa-solid fa-video"></i><h3>Live sessions</h3><p>Turn a question into a focused conversation with a mentor who gets your goal.</p></div><div class="feature-item"><i class="fa-solid fa-chart-line"></i><h3>Visible progress</h3><p>Keep your bookings, sessions, assignments, and milestones moving in one place.</p></div></div></section>

        <section class="section" id="mentors"><div class="section-heading"><div><div class="eyebrow">Meet your next mentor</div><h2>Advice is better when it is specific.</h2></div><a class="button button-quiet" href="mentors.php">See all mentors <i class="fa-solid fa-arrow-right"></i></a></div><div class="mentor-grid"><?php if (!$mentors): ?><p class="hero-copy">Verified mentors will appear here as they join the platform.</p><?php else: foreach ($mentors as $mentor): ?><article class="mentor"><div class="mentor-head"><div class="mentor-avatar"><?php if (!empty($mentor['profile_picture'])): ?><img src="frontend/assets/images/profile/<?php echo htmlspecialchars($mentor['profile_picture']); ?>" alt=""><?php else: echo htmlspecialchars(strtoupper(substr($mentor['full_name'], 0, 2))); endif; ?></div><div><strong><?php echo htmlspecialchars($mentor['full_name']); ?></strong><small><?php echo htmlspecialchars($mentor['specialization'] ?: 'Verified mentor'); ?></small></div></div><h3><?php echo htmlspecialchars($mentor['bio'] ?: 'Here to help you make a confident next move.'); ?></h3><div class="mentor-rating"><i class="fa-solid fa-star"></i> <?php echo number_format((float)$mentor['rating'], 1); ?> <span> · <?php echo (int)$mentor['experience_years']; ?> years experience</span></div></article><?php endforeach; endif; ?></div></section>

        <section class="cta"><h2>Your next chapter starts with one good conversation.</h2><p>Join SkillShare Hub and turn the distance between where you are and where you want to be into a plan.</p><a class="button" href="register.php">Create your free account <i class="fa-solid fa-arrow-right"></i></a></section>
    </main>

    <footer class="site-footer"><span>© <?php echo date('Y'); ?> SkillShare Hub</span><nav><a href="about.php">About</a><a href="contact.php">Contact</a><a href="privacy-policy.php">Privacy</a><a href="terms.php">Terms</a></nav><span>Learn · Connect · Grow</span></footer>
</div>
</body>
</html>
