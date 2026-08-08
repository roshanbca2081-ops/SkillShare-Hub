-- ============================================================
-- SkillShare Hub - Seed Data for Academic Navigation
-- Flow: academic_fields -> courses -> subjects -> skills -> mentors
-- Run AFTER skillsharehub_academic_schema.sql
-- ============================================================

-- ------------------------------------------------------------
-- ACADEMIC FIELDS
-- ------------------------------------------------------------
INSERT INTO acad_fields (name, slug, icon, color, description, status) VALUES
('Information Technology', 'information-technology', 'fa-laptop-code', '#60a5fa', 'Learn programming, software development, and modern IT skills', 'active'),
('Computer Engineering', 'computer-engineering', 'fa-microchip', '#8b5cf6', 'Study computer architecture, hardware, and embedded systems', 'active'),
('Civil Engineering', 'civil-engineering', 'fa-building', '#22c55e', 'Design and build infrastructure, structures, and roads', 'active');

-- ------------------------------------------------------------
-- COURSES for Information Technology (field_id = 1)
-- ------------------------------------------------------------
INSERT INTO acad_courses (field_id, name, slug, icon, description, status) VALUES
(1, 'BCA', 'bca', 'fa-graduation-cap', 'Bachelor of Computer Application - programming, web, databases & software', 'active'),
(1, 'BSc CSIT', 'bsc-csit', 'fa-code', 'BSc in Computer Science & Information Technology', 'active'),
(1, 'BIT', 'bit', 'fa-laptop', 'Bachelor of Information Technology', 'active'),
(1, 'B.Tech Computer Engineering', 'btech-computer-engineering', 'fa-microchip', 'Bachelor of Technology in Computer Engineering', 'active'),
(1, 'Software Engineering', 'software-engineering', 'fa-cogs', 'Software design, development, testing and project management', 'active'),
(1, 'Computer Science', 'computer-science', 'fa-server', 'Core computer science theory and application', 'active'),
(1, 'Information Technology', 'it-course', 'fa-network-wired', 'Applied information technology and systems', 'active');

-- COURSES for Computer Engineering (field_id = 2)
INSERT INTO acad_courses (field_id, name, slug, icon, description, status) VALUES
(2, 'BE Computer Engineering', 'be-computer-engineering', 'fa-microchip', 'Bachelor of Engineering in Computer Engineering', 'active'),
(2, 'Computer Engineering', 'computer-engineering-course', 'fa-cpu', 'Study of computer hardware, software and networks', 'active'),
(2, 'Embedded Systems', 'embedded-systems', 'fa-robot', 'Embedded programming, microcontrollers and IoT systems', 'active'),
(2, 'Computer Hardware and Networking', 'computer-hardware-networking', 'fa-sitemap', 'Hardware fundamentals, networking and system administration', 'active');

-- COURSES for Civil Engineering (field_id = 3)
INSERT INTO acad_courses (field_id, name, slug, icon, description, status) VALUES
(3, 'BE Civil Engineering', 'be-civil-engineering', 'fa-building', 'Bachelor of Engineering in Civil Engineering', 'active'),
(3, 'Civil Engineering', 'civil-engineering-course', 'fa-hard-hat', 'Core civil engineering principles and practice', 'active'),
(3, 'Structural Engineering', 'structural-engineering', 'fa-cubes', 'Design and analysis of structures', 'active'),
(3, 'Transportation Engineering', 'transportation-engineering', 'fa-road', 'Highway, traffic and transportation systems', 'active'),
(3, 'Geotechnical Engineering', 'geotechnical-engineering', 'fa-mountain-sun', 'Soil mechanics and foundation engineering', 'active');

-- ------------------------------------------------------------
-- SUBJECTS for BCA (course_id = 1)
-- ------------------------------------------------------------
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(1, 'Computer Fundamentals', 'computer-fundamentals', 'Basics of computers, hardware, software and operating systems', 'active'),
(1, 'Programming in C', 'programming-in-c', 'C programming fundamentals', 'active'),
(1, 'Digital Logic', 'digital-logic', 'Boolean algebra, logic gates and digital circuits', 'active'),
(1, 'Mathematics', 'mathematics', 'Discrete and applied mathematics', 'active'),
(1, 'Data Structures and Algorithms', 'data-structures-algorithms', 'Data structures, algorithms and complexity', 'active'),
(1, 'Database Management System', 'database-management-system', 'Database design, SQL and DBMS concepts', 'active'),
(1, 'Web Technology', 'web-technology', 'HTML, CSS, JavaScript, PHP, MySQL and web development', 'active'),
(1, 'Object-Oriented Programming', 'object-oriented-programming', 'OOP concepts using languages like Java and C++', 'active'),
(1, 'Java Programming', 'java-programming', 'Java language, JVM and standard libraries', 'active'),
(1, 'Operating Systems', 'operating-systems', 'OS principles, processes, memory and file systems', 'active'),
(1, 'Software Engineering', 'software-engineering', 'Software lifecycle, requirements, design and testing', 'active'),
(1, 'Computer Networks', 'computer-networks', 'Networking models, protocols and security', 'active'),
(1, 'System Analysis and Design', 'system-analysis-design', 'System analysis, design and development methodologies', 'active'),
(1, 'Statistics', 'statistics', 'Descriptive and inferential statistics', 'active'),
(1, 'Artificial Intelligence', 'artificial-intelligence', 'AI concepts, search, learning and applications', 'active'),
(1, 'Project Work', 'project-work', 'Capstone and final year project development', 'active');

-- SUBJECTS for BSc CSIT (course_id = 2)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(2, 'Discrete Structure', 'discrete-structure', 'Sets, logic, relations, graphs and combinatorics', 'active'),
(2, 'Computer Architecture', 'computer-architecture', 'Processor design, memory and instruction set', 'active'),
(2, 'Data Structures', 'data-structures', 'Linear and non-linear data structures', 'active'),
(2, 'Operating Systems', 'os-csit', 'OS concepts and management', 'active'),
(2, 'Database Systems', 'database-systems', 'DBMS and SQL', 'active'),
(2, 'Machine Learning', 'machine-learning', 'ML algorithms and models', 'active'),
(2, 'Web Technology', 'web-tech-csit', 'Modern web development', 'active');

-- SUBJECTS for BIT (course_id = 3)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(3, 'Programming', 'programming-bit', 'Programming fundamentals and logic', 'active'),
(3, 'Database', 'database-bit', 'Relational databases and SQL', 'active'),
(3, 'Web Development', 'web-development-bit', 'Frontend and backend web development', 'active'),
(3, 'Networking', 'networking-bit', 'Computer networking fundamentals', 'active'),
(3, 'Data Analytics', 'data-analytics-bit', 'Data analysis techniques and tools', 'active'),
(3, 'Cloud Computing', 'cloud-computing-bit', 'Cloud platforms and services', 'active');

-- SUBJECTS for Software Engineering (course_id = 5)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(5, 'Software Requirements', 'software-requirements', 'Gathering and analyzing requirements', 'active'),
(5, 'System Design', 'system-design', 'Architecture and system design', 'active'),
(5, 'Software Testing', 'software-testing', 'Testing strategies and QA', 'active'),
(5, 'Agile & DevOps', 'agile-devops', 'Agile practices and DevOps pipelines', 'active'),
(5, 'Web Development', 'web-dev-se', 'Full stack web development', 'active');

-- SUBJECTS for Computer Science (course_id = 6)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(6, 'Algorithms', 'algorithms-cs', 'Algorithm design and analysis', 'active'),
(6, 'Theory of Computation', 'theory-computation', 'Automata, languages and computability', 'active'),
(6, 'Compilers', 'compilers', 'Compiler design and implementation', 'active'),
(6, 'Data Structures', 'data-structures-cs', 'Data structures in depth', 'active');

-- SUBJECTS for B.Tech Computer Engineering (course_id = 4)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(4, 'Digital Electronics', 'digital-electronics', 'Digital circuits and logic design', 'active'),
(4, 'Microprocessors', 'microprocessors', 'Microprocessor architecture and programming', 'active'),
(4, 'Computer Networks', 'networks-btech', 'Networking protocols', 'active'),
(4, 'Database Systems', 'dbms-btech', 'Database management', 'active');

-- SUBJECTS for Computer Engineering course (course_id = 8)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(8, 'Digital Logic', 'digital-logic-ce', 'Logic design fundamentals', 'active'),
(8, 'Computer Architecture', 'arch-ce', 'System architecture', 'active'),
(8, 'Microcontrollers', 'microcontrollers', 'Microcontroller systems', 'active'),
(8, 'Data Structures', 'ds-ce', 'Data structures for engineers', 'active');

-- SUBJECTS for Embedded Systems (course_id = 9)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(9, 'Embedded C', 'embedded-c', 'C programming for embedded systems', 'active'),
(9, 'RTOS', 'rtos', 'Real-time operating systems', 'active'),
(9, 'IoT', 'iot-embedded', 'Internet of Things systems', 'active'),
(9, 'Firmware Development', 'firmware', 'Firmware design and development', 'active');

-- SUBJECTS for Computer Hardware and Networking (course_id = 10)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(10, 'Computer Hardware', 'computer-hardware', 'Hardware components and assembly', 'active'),
(10, 'Network Administration', 'network-admin', 'Network setup and administration', 'active'),
(10, 'Cisco Networking', 'cisco', 'Cisco networking fundamentals', 'active'),
(10, 'System Administration', 'sys-admin', 'System administration and maintenance', 'active');

-- SUBJECTS for BE Civil Engineering (course_id = 11)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(11, 'Surveying', 'surveying', 'Surveying techniques and instruments', 'active'),
(11, 'Structural Analysis', 'structural-analysis', 'Analysis of structures', 'active'),
(11, 'Concrete Technology', 'concrete-technology', 'Concrete materials and testing', 'active'),
(11, 'Fluid Mechanics', 'fluid-mechanics', 'Fluid properties and flow', 'active');

-- SUBJECTS for Civil Engineering course (course_id = 12)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(12, 'Engineering Drawing', 'engineering-drawing', 'Technical and engineering drawing', 'active'),
(12, 'Building Construction', 'building-construction', 'Construction materials and methods', 'active'),
(12, 'Soil Mechanics', 'soil-mechanics', 'Soil properties and behavior', 'active');

-- SUBJECTS for Structural Engineering (course_id = 13)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(13, 'Steel Design', 'steel-design', 'Steel structure design', 'active'),
(13, 'Reinforced Concrete', 'reinforced-concrete', 'RC design and detailing', 'active'),
(13, 'Earthquake Engineering', 'earthquake-engineering', 'Seismic design of structures', 'active'),
(13, 'Finite Element Analysis', 'fea', 'FEA methods and applications', 'active');

-- SUBJECTS for Transportation Engineering (course_id = 14)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(14, 'Highway Engineering', 'highway-engineering', 'Highway design and construction', 'active'),
(14, 'Traffic Engineering', 'traffic-engineering', 'Traffic flow and control', 'active'),
(14, 'Transportation Planning', 'transportation-planning', 'Transport systems planning', 'active');

-- SUBJECTS for Geotechnical Engineering (course_id = 15)
INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES
(15, 'Foundation Engineering', 'foundation-engineering', 'Foundation design', 'active'),
(15, 'Geotechnical Investigation', 'geotechnical-investigation', 'Site investigation and testing', 'active');

