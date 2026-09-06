# SkillShare Hub Database

This directory contains the complete database structure, migrations, seeders, views, stored procedures, triggers, and events for the SkillShare Hub application.

## Directory Structure

```
database/
├── schema/                          # Table definitions (canonical schema)
│   ├── 01_users.sql
│   ├── 02_academic_fields.sql
│   ├── 03_courses.sql
│   ├── 04_course_modules.sql
│   ├── 05_course_lessons.sql
│   ├── 06_enrollments.sql
│   ├── 07_lesson_progress.sql
│   ├── 08_sessions.sql
│   ├── 09_bookings.sql
│   ├── 10_assignments.sql
│   ├── 11_assignment_submissions.sql
│   ├── 12_resources.sql
│   ├── 13_research_projects.sql
│   ├── 14_research_applications.sql
│   ├── 15_interview_questions.sql
│   ├── 16_certificates.sql
│   ├── 17_payments.sql
│   ├── 18_messages.sql
│   ├── 19_notifications.sql
│   ├── 20_ratings.sql
│   ├── 21_wishlist.sql
│   ├── 22_blog_posts.sql
│   ├── 23_blog_comments.sql
│   ├── 24_system_logs.sql
│   └── 25_settings.sql
├── migrations/                      # Versioned migration files
│   ├── 2024_01_01_000001_create_users_table.sql
│   ├── 2024_01_01_000002_create_academic_fields_table.sql
│   ├── 2024_01_01_000025_create_settings_table.sql
│   ├── 2024_01_15_000001_add_slug_to_courses.sql
│   ├── 2024_01_15_000002_add_social_links_to_users.sql
│   ├── 2024_02_01_000001_add_indexes_for_performance.sql
│   └── 2024_02_15_000001_add_soft_deletes.sql
├── seeders/                         # Sample data for development
│   ├── 01_users_seeder.sql
│   ├── 02_academic_fields_seeder.sql
│   ├── 03_courses_seeder.sql
│   ├── 04_sessions_seeder.sql
│   ├── 05_enrollments_seeder.sql
│   ├── 06_notifications_seeder.sql
│   └── 07_sample_data.sql
├── views/                           # Database views
│   ├── v_course_details.sql
│   ├── v_student_progress.sql
│   ├── v_mentor_stats.sql
│   ├── v_course_analytics.sql
│   ├── v_revenue_summary.sql
│   └── v_user_activity.sql
├── procedures/                      # Stored procedures
│   ├── sp_get_user_dashboard.sql
│   ├── sp_get_mentor_dashboard.sql
│   ├── sp_get_admin_dashboard.sql
│   ├── sp_enroll_student.sql
│   ├── sp_update_course_progress.sql
│   ├── sp_generate_certificate.sql
│   ├── sp_process_payment.sql
│   ├── sp_get_course_recommendations.sql
│   ├── sp_get_top_performers.sql
│   └── sp_cleanup_expired_sessions.sql
├── triggers/                        # Database triggers
│   ├── tr_update_course_rating.sql
│   ├── tr_update_enrollment_progress.sql
│   ├── tr_update_course_student_count.sql
│   ├── tr_log_user_activity.sql
│   └── tr_update_certificate_status.sql
├── events/                          # Scheduled events
│   ├── ev_update_session_status.sql
│   ├── ev_cleanup_expired_tokens.sql
│   ├── ev_generate_monthly_reports.sql
│   └── ev_archive_old_notifications.sql
├── backups/                         # Database backups
│   ├── automatic/
│   └── manual/
├── exports/                         # Data exports
│   └── reports/
├── skillshare_hub.sql               # Complete setup script
├── seed_data.sql                    # All seed data in one file
└── full_database_backup.sql         # Full backup with schema + data
```

## Quick Start

### Setup Database

```bash
# Using MySQL command line
mysql -u root -p < database/skillshare_hub.sql

# Or import via phpMyAdmin
# 1. Open phpMyAdmin
# 2. Select/create database: skillshare_hub
# 3. Import: database/skillshare_hub.sql
```

### Run Migrations Only

```bash
# Run all migrations in order
mysql -u root -p skillshare_hub < database/migrations/2024_01_01_000001_create_users_table.sql
mysql -u root -p skillshare_hub < database/migrations/2024_01_01_000002_create_academic_fields_table.sql
# ... continue for all migration files
```

### Seed Data

```bash
# Seed all sample data
mysql -u root -p skillshare_hub < database/seed_data.sql
```

### Enable Events (Optional)

```sql
-- Enable MySQL Event Scheduler
SET GLOBAL event_scheduler = ON;

-- Verify it's running
SHOW VARIABLES LIKE 'event_scheduler';
```

## Tables

| # | Table | Description |
|---|-------|-------------|
| 1 | users | Core user table (admin, mentor, fresher) |
| 2 | academic_fields | Academic field/category lookup |
| 3 | courses | Course catalog |
| 4 | course_modules | Course modules |
| 5 | course_lessons | Course lessons within modules |
| 6 | enrollments | Student course enrollments |
| 7 | lesson_progress | Track lesson completion |
| 8 | sessions | Live mentoring sessions |
| 9 | bookings | Session bookings |
| 10 | assignments | Course assignments |
| 11 | assignment_submissions | Assignment submissions |
| 12 | resources | Course resources |
| 13 | research_projects | Research projects |
| 14 | research_applications | Research applications |
| 15 | interview_questions | Interview preparation questions |
| 16 | certificates | Course completion certificates |
| 17 | payments | Payment records |
| 18 | messages | Direct messages |
| 19 | notifications | User notifications |
| 20 | ratings | Mentor ratings/reviews |
| 21 | wishlist | Student wishlists |
| 22 | blog_posts | Blog posts |
| 23 | blog_comments | Blog comments |
| 24 | system_logs | System activity logs |
| 25 | settings | Application settings |

## Views

| View | Description |
|------|-------------|
| v_course_details | Course details with mentor and field info |
| v_student_progress | Student enrollment progress |
| v_mentor_stats | Mentor statistics and metrics |
| v_course_analytics | Course analytics and metrics |
| v_revenue_summary | Monthly revenue summary |
| v_user_activity | User activity overview |

## Stored Procedures

| Procedure | Description |
|-----------|-------------|
| sp_get_user_dashboard | Get user dashboard statistics |
| sp_get_mentor_dashboard | Get mentor dashboard data |
| sp_get_admin_dashboard | Get admin dashboard statistics |
| sp_enroll_student | Enroll a student in a course |
| sp_update_course_progress | Update course progress |
| sp_generate_certificate | Generate course certificate |
| sp_process_payment | Process course payment |
| sp_get_course_recommendations | Get recommended courses |
| sp_get_top_performers | Get top performing students |
| sp_cleanup_expired_sessions | Clean up expired sessions |

## Notes

- All tables use `utf8mb4` charset and `utf8mb4_unicode_ci` collation
- Soft deletes are supported via `deleted_at` column
- Foreign keys enforce referential integrity
- Indexes are created for common query patterns
- Default password for seeded users: `password`
- MySQL Event Scheduler must be enabled for scheduled events
