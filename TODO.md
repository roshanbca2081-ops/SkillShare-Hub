# TODO - ShareSkill Hub premium homepage + academic fields

- [x] Update `index.php` homepage:
  - [x] Replace the current Academic Fields cards with your requested 12 fields
  - [x] Render each field as a clickable circular glassmorphism icon
  - [x] Add premium background elements: blurred center watermark logo, animated gradient, particles, glowing circles, parallax-friendly layers

- [x] Update `assets/css/home.css`:
  - [x] Add CSS for the new field icon grid (float, hover glow, scaling, shadows)
  - [x] Add CSS for particles/glowing circles/watermark/parallax layers + fade-in-on-scroll hooks
- [x] Update `assets/js/main.js`:
  - [x] Implement smooth throttled parallax scrolling
- [x] Ensure any scroll-trigger fade-in works with existing structure
- [x] Update `field.php` course section:
  - [x] Use course images from `includes/field-data.php`
  - [x] Add course meta (duration, skill level, mentors count, practical projects, assignments, placement support)
  - [x] Add “View Details” button per course card
- [ ] Quick verification:
  - [ ] Confirm clicking a field icon routes to `field.php?field=<slug>` and renders the correct field data
  - [ ] Validate responsiveness on desktop/tablet/mobile

