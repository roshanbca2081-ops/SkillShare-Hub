<?php
session_start();
$page_title = 'Courses | SkillShare Hub';
$page_active = 'Academic Fields';
include 'config.php';
include 'frontend/components/platform-header.php';
include 'frontend/components/acad-db.php';

$field_id = isset($_GET['field_id']) ? (int)$_GET['field_id'] : 0;
$pdo = getDB();

$field = null;
if ($field_id) {
    $field = acad_query("SELECT * FROM academic_fields WHERE id = ? AND status = 'active'", [$field_id], true);
}

$courses = [];
if ($field_id) {
    $courses = acad_query("SELECT c.*,
                    (SELECT COUNT(*) FROM skills s WHERE s.course_id = c.id AND s.status='active') AS skill_count
                    FROM courses c
                    WHERE c.academic_field_id = ? AND c.status='active'
                    ORDER BY c.name ASC", [$field_id]);
} else {
    $courses = acad_query("SELECT c.*,
                    (SELECT COUNT(*) FROM skills s WHERE s.course_id = c.id AND s.status='active') AS skill_count
                    FROM courses c
                    WHERE c.status='active'
                    ORDER BY c.name ASC");
}

$all_fields = acad_query("SELECT id, name, slug FROM academic_fields WHERE status = 'active' ORDER BY sort_order, name ASC");

// Build field-course hierarchy for JS
$fieldCourseMap = [];
foreach ($all_fields as $f) {
    $fieldCourseMap[$f['id']] = [
        'name' => $f['name'],
        'slug' => $f['slug'],
        'courses' => []
    ];
}
foreach ($courses as $c) {
    $fid = $c['academic_field_id'] ?: $c['field_id'];
    if (isset($fieldCourseMap[$fid])) {
        $fieldCourseMap[$fid]['courses'][] = [
            'id' => (int)$c['id'],
            'name' => $c['name'],
            'slug' => $c['slug'],
            'description' => $c['description'],
            'icon' => $c['icon'] ?: 'fa-graduation-cap',
                'duration' => $c['duration'] ?: '',
                'level' => $c['level'] ?: '',
                'rating' => $c['rating'] ?: '0',
                'skill_count' => (int)$c['skill_count']
            ];
    }
}

$coursesJson = json_encode(array_values($fieldCourseMap));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShareSkill Hub - Courses</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-400: #60a5fa;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --secondary-400: #a78bfa;
            --secondary-500: #8b5cf6;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --text-primary: #ffffff;
            --text-secondary: rgba(255,255,255,0.8);
            --text-muted: rgba(255,255,255,0.4);
            --glass-bg: rgba(255,255,255,0.05);
            --glass-border: rgba(255,255,255,0.1);
            --shadow-lg: 0 8px 40px rgba(0,0,0,0.4);
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-full: 50px;
            --transition-bounce: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            --gradient-primary: linear-gradient(135deg, #3b82f6, #8b5cf6);
            --font-heading: 'Poppins', sans-serif;
            --font-primary: 'Inter', sans-serif;
            --sidebar-width: 240px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-primary);
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #1a1a2e 100%);
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        ::-webkit-scrollbar-thumb { background: var(--gradient-primary); border-radius: 10px; }

        .bg-animated {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .bg-animated::before {
            content: '';
            position: absolute;
            inset: -50%;
            background: 
                radial-gradient(ellipse at 20% 50%, rgba(59,130,246,0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(139,92,246,0.08) 0%, transparent 50%);
            animation: bgShift 20s ease-in-out infinite alternate;
        }
        @keyframes bgShift {
            0% { transform: translate(0,0) scale(1); }
            100% { transform: translate(5%,-5%) scale(1.05); }
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(10,10,30,0.92);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--glass-border);
            display: flex;
            flex-direction: column;
            padding: 20px 12px;
            z-index: 100;
            overflow-y: auto;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 8px 20px;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 16px;
        }
        .sidebar-brand i { font-size: 1.8rem; color: var(--primary-400); }
        .sidebar-brand h4 {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--text-primary);
        }
        .sidebar-brand h4 span { color: var(--primary-400); }

        .sidebar-nav .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 2px;
            cursor: pointer;
        }
        .sidebar-nav .nav-item:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-primary);
        }
        .sidebar-nav .nav-item.active {
            background: rgba(59,130,246,0.12);
            color: var(--primary-400);
        }
        .sidebar-nav .nav-item i { width: 20px; font-size: 1rem; }
        .sidebar-nav .nav-item .badge {
            margin-left: auto;
            background: var(--danger);
            color: #fff;
            font-size: 0.6rem;
            padding: 2px 8px;
            border-radius: var(--radius-full);
        }

        .sidebar-footer {
            border-top: 1px solid var(--glass-border);
            padding-top: 12px;
            margin-top: auto;
        }
        .sidebar-footer .nav-item { color: var(--text-muted); }
        .sidebar-footer .nav-item:hover { color: var(--danger); }

        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 20px 30px 40px;
            min-height: 100vh;
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0 20px;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .top-header .greeting h2 {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--text-primary);
        }
        .top-header .greeting p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .top-header .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .top-header .header-actions .search-box {
            display: flex;
            align-items: center;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-full);
            padding: 6px 16px;
            transition: all 0.3s ease;
        }
        .top-header .header-actions .search-box:focus-within {
            border-color: var(--primary-400);
            box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
        }
        .top-header .header-actions .search-box input {
            background: transparent;
            border: none;
            padding: 6px 8px;
            color: var(--text-primary);
            font-size: 0.85rem;
            outline: none;
            min-width: 160px;
        }
        .top-header .header-actions .search-box input::placeholder {
            color: var(--text-muted);
        }
        .top-header .header-actions .search-box i {
            color: var(--text-muted);
        }
        .top-header .header-actions .profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 12px 4px 4px;
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .top-header .header-actions .profile:hover {
            background: var(--glass-bg);
        }
        .top-header .header-actions .profile img {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-full);
            object-fit: cover;
            border: 2px solid var(--glass-border);
        }
        .top-header .header-actions .profile span {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            background: rgba(255,255,255,0.06);
            border-color: var(--border-hover);
        }
        .stat-card .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stat-card .stat-number {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--text-primary);
        }
        .stat-card .stat-label {
            color: var(--text-muted);
            font-size: 0.8rem;
        }
        .stat-card .stat-icon {
            font-size: 1.6rem;
            color: var(--primary-400);
            opacity: 0.6;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 8px;
        }
        .section-header h3 {
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--text-primary);
        }
        .section-header .filter-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .section-header .filter-group .filter-btn {
            padding: 6px 16px;
            border-radius: var(--radius-full);
            border: 1px solid var(--glass-border);
            background: transparent;
            color: var(--text-secondary);
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .section-header .filter-group .filter-btn:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-primary);
        }
        .section-header .filter-group .filter-btn.active {
            background: rgba(59,130,246,0.12);
            border-color: var(--primary-400);
            color: var(--primary-400);
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .course-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: 22px 20px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }
        .course-card:hover {
            transform: translateY(-6px);
            background: rgba(255,255,255,0.06);
            border-color: var(--border-hover);
            box-shadow: var(--shadow-lg);
        }

        .course-card .course-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .course-card .course-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            background: rgba(59,130,246,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: var(--primary-400);
            transition: all 0.3s ease;
        }
        .course-card:hover .course-icon {
            background: var(--gradient-primary);
            color: #fff;
            transform: scale(1.1) rotate(-5deg);
        }
        .course-card .course-badge {
            font-size: 0.6rem;
            padding: 3px 12px;
            border-radius: var(--radius-full);
            background: rgba(59,130,246,0.12);
            color: var(--primary-400);
            font-weight: 500;
        }
        .course-card .course-badge.engineering {
            background: rgba(59,130,246,0.12);
            color: var(--primary-400);
        }
        .course-card .course-badge.it {
            background: rgba(139,92,246,0.12);
            color: var(--secondary-400);
        }
        .course-card .course-badge.science {
            background: rgba(34,197,94,0.12);
            color: var(--success);
        }
        .course-card .course-badge.management {
            background: rgba(245,158,11,0.12);
            color: var(--warning);
        }

        .course-card .course-name {
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--text-primary);
            margin: 10px 0 4px;
        }
        .course-card .course-desc {
            color: var(--text-secondary);
            font-size: 0.85rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-card .course-meta {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            padding: 12px 0;
            border-top: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
            margin: 10px 0 12px;
        }
        .course-card .course-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
            color: var(--text-muted);
            font-size: 0.75rem;
        }
        .course-card .course-meta span i {
            color: var(--primary-400);
        }
        .course-card .course-meta span .value {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .course-card .course-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .course-card .course-footer .course-btn {
            padding: 6px 20px;
            border-radius: var(--radius-full);
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .course-card .course-footer .course-btn:hover {
            background: var(--gradient-primary);
            border-color: var(--primary-500);
            color: #fff;
        }
        .course-card .course-footer .enrolled-badge {
            font-size: 0.7rem;
            color: var(--success);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .course-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(20px);
            z-index: 9999;
            padding: 40px 20px;
            overflow-y: auto;
            align-items: center;
            justify-content: center;
        }
        .course-overlay.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .course-detail {
            max-width: 800px;
            width: 100%;
            background: rgba(20,20,40,0.95);
            backdrop-filter: blur(30px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: 40px;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.4s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .course-detail .close-btn {
            position: absolute;
            top: 16px;
            right: 20px;
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.4rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .course-detail .close-btn:hover {
            color: var(--text-primary);
            transform: rotate(90deg);
        }

        .course-detail .detail-header {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .course-detail .detail-header .detail-icon {
            width: 64px;
            height: 64px;
            border-radius: var(--radius-md);
            background: rgba(59,130,246,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--primary-400);
            flex-shrink: 0;
        }
        .course-detail .detail-header .detail-info .name {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--text-primary);
        }
        .course-detail .detail-header .detail-info .field {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        .course-detail .detail-header .detail-info .field i {
            color: var(--primary-400);
        }

        .course-detail .detail-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            padding: 16px 0;
            border-top: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 16px;
        }
        .course-detail .detail-meta .meta-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .course-detail .detail-meta .meta-item .label {
            color: var(--text-muted);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .course-detail .detail-meta .meta-item .value {
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.95rem;
        }
        .course-detail .detail-meta .meta-item .value i {
            color: var(--primary-400);
            margin-right: 4px;
        }

        .course-detail .detail-desc {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.8;
            margin-bottom: 16px;
        }

        .detail-semesters {
            margin-top: 16px;
        }
        .detail-semesters .semester-tabs {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            padding: 4px 0 12px;
            flex-wrap: nowrap;
            scrollbar-width: none;
        }
        .detail-semesters .semester-tabs::-webkit-scrollbar {
            display: none;
        }
        .detail-semesters .semester-tabs .tab {
            padding: 6px 16px;
            border-radius: var(--radius-full);
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .detail-semesters .semester-tabs .tab:hover {
            background: rgba(255,255,255,0.06);
            color: var(--text-primary);
        }
        .detail-semesters .semester-tabs .tab.active {
            background: var(--gradient-primary);
            border-color: var(--primary-500);
            color: #fff;
        }

        .detail-subjects {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 12px;
        }
        .detail-subjects .subject-item {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }
        .detail-subjects .subject-item:hover {
            background: rgba(255,255,255,0.06);
            border-color: var(--border-hover);
        }
        .detail-subjects .subject-item .sub-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            background: rgba(59,130,246,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: var(--primary-400);
            flex-shrink: 0;
        }
        .detail-subjects .subject-item .sub-info .sub-name {
            font-weight: 500;
            font-size: 0.85rem;
            color: var(--text-primary);
        }
        .detail-subjects .subject-item .sub-info .sub-code {
            font-size: 0.7rem;
            color: var(--text-muted);
        }
        .detail-subjects .subject-item .sub-credits {
            margin-left: auto;
            font-size: 0.7rem;
            color: var(--text-muted);
            background: var(--glass-bg);
            padding: 2px 8px;
            border-radius: var(--radius-full);
        }
        .detail-subjects .subject-item .sub-credits i {
            color: var(--warning);
        }

        .detail-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--glass-border);
            flex-wrap: wrap;
        }
        .detail-actions .btn {
            padding: 10px 28px;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
        }
        .detail-actions .btn-primary {
            background: var(--gradient-primary);
            color: #fff;
        }
        .detail-actions .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59,130,246,0.3);
        }
        .detail-actions .btn-outline {
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
        }
        .detail-actions .btn-outline:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-primary);
        }

        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-width: 380px;
            width: 100%;
        }
        .toast {
            background: rgba(20,20,40,0.95);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            color: var(--text-primary);
            box-shadow: var(--shadow-lg);
            animation: slideUp 0.4s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .toast.success { border-left: 4px solid var(--success); }
        .toast.error { border-left: 4px solid var(--danger); }
        .toast.info { border-left: 4px solid var(--primary-500); }
        .toast .icon { font-size: 1.2rem; flex-shrink: 0; }
        .toast .content { flex: 1; }
        .toast .title { font-weight: 600; font-size: 0.9rem; }
        .toast .message { font-size: 0.8rem; color: var(--text-secondary); }
        .toast .close {
            cursor: pointer;
            color: var(--text-muted);
            background: none;
            border: none;
            font-size: 1rem;
            padding: 4px;
        }
        .toast .close:hover { color: var(--text-primary); }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                width: 220px;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
            .menu-toggle {
                display: block !important;
            }
            .courses-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.3rem;
            cursor: pointer;
            padding: 4px 8px;
        }

        @media (max-width: 768px) {
            .top-header {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }
            .top-header .header-actions {
                flex-wrap: wrap;
            }
            .top-header .header-actions .search-box input {
                min-width: 120px;
            }
            .courses-grid {
                grid-template-columns: 1fr;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .course-detail {
                padding: 24px;
            }
            .detail-subjects {
                grid-template-columns: 1fr;
            }
            .course-detail .detail-meta {
                grid-template-columns: 1fr 1fr;
            }
            .detail-actions {
                flex-direction: column;
            }
            .detail-actions .btn {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
            .section-header {
                flex-direction: column;
                align-items: stretch;
            }
            .section-header .filter-group {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .course-detail {
                padding: 16px;
            }
            .course-detail .detail-header .detail-info .name {
                font-size: 1.2rem;
            }
            .course-detail .detail-meta {
                grid-template-columns: 1fr;
            }
            .detail-semesters .semester-tabs .tab {
                font-size: 0.65rem;
                padding: 4px 12px;
            }
            .top-header .greeting h2 {
                font-size: 1.2rem;
            }
            .top-header .header-actions .profile span {
                display: none;
            }
        }
    </style>
    <link rel="stylesheet" href="frontend/assets/css/figma-modules.css?v=2">
</head>
<body>



    <main class="main-content">
        <header class="top-header">
            <div class="greeting">
                <div style="display:flex;align-items:center;gap:12px;">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h2>📚 Explore Courses</h2>
                        <p>Browse our wide range of academic courses across all fields</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search courses..." id="courseSearch" oninput="filterCourses()">
                </div>
                <div class="profile">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='36' height='36' viewBox='0 0 36 36'%3E%3Crect width='36' height='36' rx='18' fill='%2360a5fa'/%3E%3Ctext x='18' y='24' text-anchor='middle' fill='white' font-size='18' font-weight='bold' font-family='Arial'%3EA%3C/text%3E%3C/svg%3E" alt="Profile">
                    <span>Alex</span>
                </div>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-top">
                    <div>
                        <div class="stat-number"><?php echo count($courses); ?></div>
                        <div class="stat-label">Total Courses</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div>
                        <div class="stat-number"><?php echo count($all_fields); ?></div>
                        <div class="stat-label">Academic Fields</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-book"></i></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div>
                        <div class="stat-number">1.2k</div>
                        <div class="stat-label">Students Enrolled</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div>
                        <div class="stat-number">4.8</div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-star" style="color:#fbbf24;"></i></div>
                </div>
            </div>
        </div>

        <div class="section-header">
            <h3>
                <i class="fas fa-graduation-cap" style="color:var(--primary-400);margin-right:8px;"></i>
                <?php if ($field): ?>
                    Courses in <?php echo htmlspecialchars($field['name']); ?>
                <?php else: ?>
                    All Courses
                <?php endif; ?>
            </h3>
            <div class="filter-group">
                <button class="filter-btn active" data-filter="all" onclick="filterCourses('all')">All</button>
                <?php foreach ($all_fields as $f): 
                    $count = 0;
                    foreach ($courses as $c) {
                        if (($c['academic_field_id'] ?: $c['field_id']) == $f['id']) $count++;
                    }
                    if ($count > 0):
                ?>
                    <button class="filter-btn" data-filter="<?php echo htmlspecialchars($f['slug']); ?>" onclick="filterCourses('<?php echo htmlspecialchars($f['slug']); ?>')"><?php echo htmlspecialchars($f['name']); ?></button>
                <?php endif; endforeach; ?>
            </div>
        </div>

        <div class="courses-grid" id="coursesGrid">
            <?php if (count($courses) === 0): ?>
                <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted);">
                    <i class="fas fa-book-open" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
                    <p>No courses available.</p>
                </div>
            <?php else: foreach ($courses as $c): 
                $fid = $c['academic_field_id'] ?: $c['field_id'];
                $fieldSlug = '';
                foreach ($all_fields as $f) {
                    if ($f['id'] == $fid) $fieldSlug = $f['slug'];
                }
                $badgeClass = 'it';
                if (stripos($c['name'], 'engineering') !== false) $badgeClass = 'engineering';
                elseif (stripos($c['name'], 'science') !== false || $fieldSlug == 'science') $badgeClass = 'science';
                elseif (stripos($c['name'], 'management') !== false || $fieldSlug == 'management') $badgeClass = 'management';
            ?>
                <div class="course-card" onclick="openCourseDetail(<?php echo (int)$c['id']; ?>, '<?php echo htmlspecialchars($c['slug']); ?>')">
                    <div class="course-top">
                        <div class="course-icon"><i class="fas <?php echo htmlspecialchars($c['icon'] ?: 'fa-graduation-cap'); ?>"></i></div>
                        <span class="course-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($fieldSlug ? ucfirst($fieldSlug) : 'Course'); ?></span>
                    </div>
                    <div class="course-name"><?php echo htmlspecialchars($c['name']); ?></div>
                    <div class="course-desc"><?php echo htmlspecialchars($c['description']); ?></div>
                    <div class="course-meta">
                        <span><i class="far fa-clock"></i> <span class="value"><?php echo htmlspecialchars($c['duration'] ?: 'N/A'); ?></span></span>
                        <span><i class="fas fa-signal"></i> <span class="value"><?php echo htmlspecialchars($c['level'] ?: 'N/A'); ?></span></span>
                        <span><i class="fas fa-star" style="color:#fbbf24;"></i> <span class="value"><?php echo htmlspecialchars($c['rating'] ?: '0'); ?></span></span>
                    </div>
                    <div class="course-footer">
                        <span class="enrolled-badge"><i class="fas fa-check-circle"></i> <?php echo (int)$c['skill_count']; ?> skills</span>
                        <button class="course-btn" onclick="event.stopPropagation();openCourseDetail(<?php echo (int)$c['id']; ?>, '<?php echo htmlspecialchars($c['slug']); ?>')">
                            <i class="fas fa-arrow-right"></i> View Course
                        </button>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </main>
</div>

<div class="course-overlay" id="courseOverlay">
    <div class="course-detail" id="courseDetail">
        <button class="close-btn" onclick="closeCourseDetail()">
            <i class="fas fa-times"></i>
        </button>
        <div id="courseDetailContent">
            <div style="text-align:center;padding:40px;color:var(--text-muted);">
                <i class="fas fa-spinner fa-spin" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
                <p>Loading course details...</p>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
    const fieldCourseData = <?php echo $coursesJson; ?>;
    const fieldSlugMap = {};
    <?php foreach ($all_fields as $f): ?>
        fieldSlugMap['<?php echo htmlspecialchars($f['slug']); ?>'] = <?php echo (int)$f['id']; ?>;
    <?php endforeach; ?>

    let currentFilter = 'all';
    let searchQuery = '';

    function renderCourses() {
        const grid = document.getElementById('coursesGrid');
        let allCourses = [];
        
        fieldCourseData.forEach(field => {
            field.courses.forEach(course => {
                allCourses.push({
                    ...course,
                    fieldName: field.name,
                    fieldSlug: field.slug
                });
            });
        });

        let filtered = allCourses;
        if (currentFilter !== 'all') {
            filtered = filtered.filter(c => c.fieldSlug === currentFilter);
        }

        if (searchQuery) {
            const q = searchQuery.toLowerCase();
            filtered = filtered.filter(c => 
                c.name.toLowerCase().includes(q) || 
                c.fieldName.toLowerCase().includes(q) ||
                (c.description && c.description.toLowerCase().includes(q))
            );
        }

        if (filtered.length === 0) {
            grid.innerHTML = `
                <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted);">
                    <i class="fas fa-search" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
                    <p>No courses found matching your criteria</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = filtered.map(course => {
            const badgeClass = course.name.toLowerCase().includes('engineering') ? 'engineering' :
                              course.fieldSlug === 'science' ? 'science' :
                              course.fieldSlug === 'management' ? 'management' : 'it';
            
            return `
            <div class="course-card" onclick="openCourseDetail(${course.id}, '${course.slug}')">
                <div class="course-top">
                    <div class="course-icon"><i class="fas ${course.icon}"></i></div>
                    <span class="course-badge ${badgeClass}">${course.fieldName}</span>
                </div>
                <div class="course-name">${course.name}</div>
                <div class="course-desc">${course.description || ''}</div>
                <div class="course-meta">
                    <span><i class="far fa-clock"></i> <span class="value">${course.duration || 'N/A'}</span></span>
                    <span><i class="fas fa-signal"></i> <span class="value">${course.level || 'N/A'}</span></span>
                    <span><i class="fas fa-star" style="color:#fbbf24;"></i> <span class="value">${course.rating || '0'}</span></span>
                </div>
                <div class="course-footer">
                    <span class="enrolled-badge"><i class="fas fa-check-circle"></i> ${course.skill_count || 0} skills</span>
                    <button class="course-btn" onclick="event.stopPropagation();openCourseDetail(${course.id}, '${course.slug}')">
                        <i class="fas fa-arrow-right"></i> View Course
                    </button>
                </div>
            </div>
        `;
        }).join('');
    }

    function filterCourses(filter) {
        if (filter) {
            currentFilter = filter;
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.filter === filter);
            });
        }
        renderCourses();
    }

    function filterCourses() {
        const input = document.getElementById('courseSearch');
        searchQuery = input.value.trim();
        renderCourses();
    }

    async function openCourseDetail(courseId, slug) {
        const overlay = document.getElementById('courseOverlay');
        const content = document.getElementById('courseDetailContent');

        content.innerHTML = `
            <div style="text-align:center;padding:40px;color:var(--text-muted);">
                <i class="fas fa-spinner fa-spin" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
                <p>Loading course details...</p>
            </div>
        `;
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';

        try {
            const res = await fetch('api/courses.php?action=show&slug=' + encodeURIComponent(slug));
            const json = await res.json();
            if (!json.success) throw new Error(json.message || 'Course not found');

            const c = json.data;
            const skills = c.skills || [];
            const mentors = c.mentors || [];

            let skillsHtml = '';
            if (skills.length === 0) {
                skillsHtml = '<p style="color:var(--text-muted);">No skills available for this course yet.</p>';
            } else {
                skillsHtml = '<div class="detail-subjects">' + skills.map(s => `
                    <div class="subject-item">
                        <div class="sub-icon"><i class="fas fa-star"></i></div>
                        <div class="sub-info">
                            <div class="sub-name">${s.name}</div>
                            <div class="sub-code">${s.category || 'Skill'}</div>
                        </div>
                        <span class="sub-credits"><i class="fas fa-signal"></i> ${s.difficulty || 'All'}</span>
                    </div>
                `).join('') + '</div>';
            }

            let mentorsHtml = '';
            if (mentors.length > 0) {
                mentorsHtml = `
                    <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--glass-border);">
                        <h4 style="font-family:var(--font-heading);font-weight:600;margin-bottom:12px;color:var(--text-primary);">
                            <i class="fas fa-users" style="color:var(--primary-400);margin-right:8px;"></i>Available Mentors
                        </h4>
                        <div style="display:flex;flex-wrap:wrap;gap:10px;">
                            ${mentors.slice(0, 6).map(m => `
                                <div style="display:flex;align-items:center;gap:8px;background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-md);padding:8px 12px;">
                                    <img src="${m.profile_picture ? 'frontend/assets/images/profiles/' + m.profile_picture : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2232%22 height=%2232%22 viewBox=%220 0 36 36%22%3E%3Crect width=%2236%22 height=%2236%22 rx=%2218%22 fill=%22%2360a5fa%22/%3E%3Ctext x=%2218%22 y=%2224%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2218%22 font-weight=%22bold%22 font-family=%22Arial%22%3EM%3C/text%3E%3C/svg%3E'}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                                    <div>
                                        <div style="font-size:0.8rem;font-weight:600;color:var(--text-primary);">${m.full_name}</div>
                                        <div style="font-size:0.7rem;color:var(--text-muted);">${m.specialization || 'Mentor'}</div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            content.innerHTML = `
                <div class="detail-header">
                    <div class="detail-icon"><i class="fas ${c.icon || 'fa-graduation-cap'}"></i></div>
                    <div class="detail-info">
                        <div class="name">${c.name}</div>
                        <div class="field"><i class="fas fa-bookmark"></i> ${c.field_name || ''}</div>
                    </div>
                </div>

                <div class="detail-meta">
                    <div class="meta-item">
                        <span class="label">Duration</span>
                        <span class="value"><i class="far fa-clock"></i> ${c.duration || 'N/A'}</span>
                    </div>
                    <div class="meta-item">
                        <span class="label">Level</span>
                        <span class="value"><i class="fas fa-signal"></i> ${c.level || 'N/A'}</span>
                    </div>
                    <div class="meta-item">
                        <span class="label">Rating</span>
                        <span class="value"><i class="fas fa-star" style="color:#fbbf24;"></i> ${c.rating || '0'}</span>
                    </div>
                    <div class="meta-item">
                        <span class="label">Skills</span>
                        <span class="value"><i class="fas fa-star"></i> ${skills.length}</span>
                    </div>
                    <div class="meta-item">
                        <span class="label">Mentors</span>
                        <span class="value"><i class="fas fa-user-tie"></i> ${mentors.length}</span>
                    </div>
                </div>

                <div class="detail-desc">${c.description || ''}</div>

                <div class="detail-semesters">
                    <div class="semester-tabs">
                        <button class="tab active" onclick="showSemester(0)">Skills</button>
                    </div>
                    <div id="semesterContent">
                        <div class="semester-content">
                            ${skillsHtml}
                        </div>
                    </div>
                </div>

                ${mentorsHtml}

                <div class="detail-actions">
                    <button class="btn btn-primary" onclick="enrollCourse('${c.name}')">
                        <i class="fas fa-user-plus"></i> Enroll Now
                    </button>
                    <button class="btn btn-outline" onclick="showToast('Info', 'Course bookmarked!', 'info')">
                        <i class="fas fa-bookmark"></i> Bookmark
                    </button>
                    <button class="btn btn-outline" onclick="showToast('Info', 'Course shared!', 'info')">
                        <i class="fas fa-share-alt"></i> Share
                    </button>
                </div>
            `;
        } catch (err) {
            content.innerHTML = `
                <div style="text-align:center;padding:40px;color:var(--text-muted);">
                    <i class="fas fa-exclamation-circle" style="font-size:2rem;display:block;margin-bottom:12px;color:var(--danger);"></i>
                    <p>Failed to load course details.</p>
                    <button class="btn btn-outline" style="margin-top:16px;" onclick="closeCourseDetail()">Close</button>
                </div>
            `;
        }
    }

    function showSemester(index) {
        const tabs = document.querySelectorAll('.detail-semesters .tab');
        tabs.forEach((tab, i) => tab.classList.toggle('active', i === index));
        const contents = document.querySelectorAll('#semesterContent .semester-content');
        contents.forEach((content, i) => {
            content.style.display = i === index ? 'block' : 'none';
        });
    }

    function closeCourseDetail() {
        const overlay = document.getElementById('courseOverlay');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function enrollCourse(courseName) {
        showToast('Enrolled!', `You have successfully enrolled in "${courseName}"`, 'success', 4000);
    }

    function showToast(title, message, type = 'info', duration = 4000) {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
        const colors = { success: '#22c55e', error: '#ef4444', info: '#3b82f6' };
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <span class="icon" style="color:${colors[type]}"><i class="fas ${icons[type] || icons.info}"></i></span>
            <div class="content">
                <div class="title">${title}</div>
                <div class="message">${message}</div>
            </div>
            <button class="close"><i class="fas fa-times"></i></button>
        `;
        toast.querySelector('.close').addEventListener('click', () => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(50px)';
            setTimeout(() => toast.remove(), 300);
        });
        container.appendChild(toast);
        if (duration > 0) {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(50px)';
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');

        if (toggle && sidebar) {
            toggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
            });

            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 992) {
                    if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                        sidebar.classList.remove('open');
                    }
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCourseDetail();
            }
        });

        document.getElementById('courseOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCourseDetail();
            }
        });

        renderCourses();
    });
</script>

</body>
</html>
