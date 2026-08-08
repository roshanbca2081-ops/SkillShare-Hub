-- ============================================================
-- SkillShare Hub - Skills Seed Data
-- Run AFTER skillsharehub_academic_seed.sql
-- subject_id references acad_subjects
-- ============================================================

-- ------------------------------------------------------------
-- BCA Subjects Skills (course 1)
-- ------------------------------------------------------------
-- BCA: Computer Fundamentals (subject 1)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(1, 'Computer Basics', 'Fundamentals of computers and hardware'),
(1, 'Operating Systems Basics', 'Introduction to operating systems'),
(1, 'MS Office', 'Word, Excel, PowerPoint productivity'),
(1, 'Typing Skills', 'Keyboard and typing proficiency');

-- BCA: Programming in C (subject 2)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(2, 'C Programming', 'C language fundamentals'),
(2, 'Pointers', 'Pointer arithmetic and memory'),
(2, 'Data Structures in C', 'Linked lists, stacks, queues in C'),
(2, 'File Handling', 'File I/O in C'),
(2, 'Debugging in C', 'Debugging C programs');

-- BCA: Digital Logic (subject 3)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(3, 'Boolean Algebra', 'Boolean logic simplification'),
(3, 'Logic Gates', 'AND, OR, NOT, NAND, NOR gates'),
(3, 'Karnaugh Maps', 'K-map minimization'),
(3, 'Combinational Circuits', 'Adders, multiplexers, decoders'),
(3, 'Sequential Circuits', 'Flip-flops and counters');

-- BCA: Mathematics (subject 4)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(4, 'Discrete Mathematics', 'Sets, relations, graphs'),
(4, 'Linear Algebra', 'Matrices and vectors'),
(4, 'Calculus', 'Derivatives and integrals'),
(4, 'Probability', 'Probability theory basics');

-- BCA: Data Structures and Algorithms (subject 5)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(5, 'Arrays & Strings', 'Array and string manipulation'),
(5, 'Linked Lists', 'Singly and doubly linked lists'),
(5, 'Stacks & Queues', 'Stack and queue implementations'),
(5, 'Trees', 'Binary trees and BST'),
(5, 'Graphs', 'Graph traversal and algorithms'),
(5, 'Sorting Algorithms', 'Bubble, merge, quick sort'),
(5, 'Searching Algorithms', 'Linear and binary search'),
(5, 'Algorithm Complexity', 'Big-O analysis');

-- BCA: Database Management System (subject 6)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(6, 'MySQL', 'MySQL database management'),
(6, 'SQL', 'Structured Query Language'),
(6, 'Database Design', 'Schema and database design'),
(6, 'ER Diagram', 'Entity-relationship modeling'),
(6, 'Normalization', 'Database normalization'),
(6, 'PostgreSQL', 'PostgreSQL database'),
(6, 'MongoDB', 'NoSQL MongoDB database');

-- BCA: Web Technology (subject 7)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(7, 'HTML', 'HyperText Markup Language'),
(7, 'CSS', 'Cascading Style Sheets'),
(7, 'JavaScript', 'Client-side scripting'),
(7, 'PHP', 'Server-side PHP programming'),
(7, 'MySQL for Web', 'Database integration in web'),
(7, 'XML', 'Extensible Markup Language'),
(7, 'Web Development', 'Full stack web development');

-- BCA: Object-Oriented Programming (subject 8)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(8, 'OOP Concepts', 'Encapsulation, inheritance, polymorphism'),
(8, 'C++', 'C++ programming'),
(8, 'Java OOP', 'OOP in Java'),
(8, 'Design Patterns', 'Common OOP design patterns');

-- BCA: Java Programming (subject 9)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(9, 'Core Java', 'Java fundamentals and syntax'),
(9, 'JVM', 'Java Virtual Machine concepts'),
(9, 'Collections Framework', 'Java collections API'),
(9, 'Exception Handling', 'Java exception handling'),
(9, 'Streams & Files', 'Java I/O and streams');

-- BCA: Operating Systems (subject 10)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(10, 'Process Management', 'Processes, threads and scheduling'),
(10, 'Memory Management', 'Memory allocation and paging'),
(10, 'File Systems', 'File system structures'),
(10, 'Linux Basics', 'Linux commands and administration');

-- BCA: Software Engineering (subject 11)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(11, 'SDLC', 'Software development life cycle'),
(11, 'Requirements Engineering', 'Gathering and analyzing requirements'),
(11, 'UML', 'Unified Modeling Language'),
(11, 'Software Testing', 'Testing methodologies'),
(11, 'Agile Methodology', 'Agile and Scrum practices');

-- BCA: Computer Networks (subject 12)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(12, 'OSI Model', 'OSI reference model'),
(12, 'TCP/IP', 'TCP/IP protocol suite'),
(12, 'IP Addressing', 'IPv4 and IPv6 addressing'),
(12, 'Network Security', 'Network security fundamentals'),
(12, 'Routing & Switching', 'Cisco routing and switching');

-- BCA: System Analysis and Design (subject 13)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(13, 'System Analysis', 'System analysis techniques'),
(13, 'DFD', 'Data flow diagrams'),
(13, 'System Design', 'System design and architecture'),
(13, 'Feasibility Study', 'Technical and economic feasibility');

-- BCA: Statistics (subject 14)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(14, 'Descriptive Statistics', 'Mean, median, mode, variance'),
(14, 'Inferential Statistics', 'Hypothesis testing and estimation'),
(14, 'Data Visualization', 'Charts and graphs'),
(14, 'SPSS', 'Statistical analysis with SPSS'),
(14, 'R Programming', 'Statistical computing with R');

-- BCA: Artificial Intelligence (subject 15)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(15, 'Machine Learning', 'ML algorithms and models'),
(15, 'Neural Networks', 'ANN and deep learning'),
(15, 'Python for AI', 'Python AI libraries'),
(15, 'Knowledge Representation', 'AI knowledge systems');

-- BCA: Project Work (subject 16)
INSERT INTO acad_skills (subject_id, name, description) VALUES
(16, 'Project Planning', 'Project scope and planning'),
(16, 'Git & GitHub', 'Version control with Git'),
(16, 'Project Documentation', 'Project reports and documentation'),
(16, 'Presentation Skills', 'Project presentation and defense');

-- ------------------------------------------------------------
-- CSIT Subjects Skills (course 2)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(17, 'Set Theory', 'Sets and operations'),          -- Discrete Structure
(17, 'Graph Theory', 'Graphs and applications'),
(17, 'Combinatorics', 'Counting and arrangements'),
(18, 'CPU Architecture', 'CPU design and pipeline'),  -- Computer Architecture
(18, 'Memory Hierarchy', 'Cache and storage'),
(19, 'Data Structures', 'Data structures implementation'),  -- Data Structures
(19, 'Algorithms', 'Algorithm design'),
(20, 'Process Scheduling', 'OS scheduling'),           -- Operating Systems
(20, 'Memory Management', 'OS memory'),
(21, 'SQL', 'SQL fundamentals'),                        -- Database Systems
(21, 'DBMS Design', 'Relational DB design'),
(22, 'Supervised Learning', 'ML supervised models'),   -- Machine Learning
(22, 'Python ML', 'ML with Python'),
(23, 'HTML/CSS/JS', 'Web technologies'),               -- Web Technology
(23, 'React Basics', 'React framework');

-- ------------------------------------------------------------
-- BIT Subjects Skills (course 3)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(24, 'Programming Logic', 'Programming fundamentals'),   -- Programming
(24, 'Python Basics', 'Python programming'),
(25, 'SQL', 'SQL for databases'),                         -- Database
(25, 'Database Design', 'DB design'),
(26, 'Frontend Development', 'HTML, CSS, JS'),           -- Web Development
(26, 'Backend Development', 'PHP, Node.js'),
(27, 'Networking Basics', 'Network fundamentals'),        -- Networking
(27, 'Network Security', 'Security fundamentals'),
(28, 'Data Analysis', 'Data analysis tools'),            -- Data Analytics
(28, 'Power BI', 'BI tools'),
(29, 'Cloud Platforms', 'AWS, Azure'),                    -- Cloud Computing
(29, 'DevOps Basics', 'CI/CD essentials');

-- ------------------------------------------------------------
-- Software Engineering Subjects Skills (course 5)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(30, 'Requirements Gathering', 'Gathering requirements'),  -- Software Requirements
(30, 'User Stories', 'Writing user stories'),
(31, 'System Architecture', 'Architecture design'),        -- System Design
(31, 'UML Diagrams', 'UML modeling'),
(32, 'Test Automation', 'Automated testing'),              -- Software Testing
(32, 'QA & QC', 'Quality assurance'),
(33, 'Agile & Scrum', 'Agile practices'),                   -- Agile & DevOps
(33, 'CI/CD Pipeline', 'DevOps CI/CD'),
(34, 'Full Stack Development', 'Full stack skills'),        -- Web Development
(34, 'APIs', 'REST API design');

-- ------------------------------------------------------------
-- Computer Science Subjects Skills (course 6)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(35, 'Big-O Analysis', 'Complexity analysis'),              -- Algorithms
(35, 'Dynamic Programming', 'DP techniques'),
(36, 'Automata', 'Finite automata'),                        -- Theory of Computation
(36, 'Turing Machines', 'Computability'),
(37, 'Lexical Analysis', 'Compilers lexical'),              -- Compilers
(37, 'Parsing', 'Parser design'),
(38, 'Trees & Graphs', 'Advanced data structures'),         -- Data Structures
(38, 'Hash Tables', 'Hashing');

-- ------------------------------------------------------------
-- B.Tech Computer Engineering Subjects Skills (course 4)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(39, 'Digital Circuits', 'Digital logic design'),           -- Digital Electronics
(39, 'VHDL', 'Hardware description language'),
(40, '8086 Microprocessor', 'Microprocessor programming'),  -- Microprocessors
(40, 'Assembly Language', 'Assembly programming'),
(41, 'Computer Networks', 'Networking protocols'),          -- Computer Networks
(41, 'Network Security', 'Security'),
(42, 'Database Systems', 'DBMS concepts'),                  -- Database Systems
(42, 'SQL', 'SQL programming');

-- ------------------------------------------------------------
-- Computer Engineering course Subjects (course 8)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(43, 'Logic Gates', 'Logic design'),                        -- Digital Logic
(43, 'Boolean Algebra', 'Boolean simplification'),
(44, 'CPU Architecture', 'Processor architecture'),         -- Computer Architecture
(44, 'Pipeline Design', 'Pipelining'),
(45, 'Microcontroller Programming', 'MCU programming'),     -- Microcontrollers
(45, 'Interfacing', 'Peripheral interfacing'),
(46, 'Data Structures', 'DS implementation'),               -- Data Structures
(46, 'Algorithms', 'Algorithm design');

-- ------------------------------------------------------------
-- Embedded Systems Subjects (course 9)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(47, 'Embedded C', 'C for embedded'),                      -- Embedded C
(47, 'Register Programming', 'Register manipulation'),
(48, 'RTOS Concepts', 'Real-time scheduling'),              -- RTOS
(48, 'Task Management', 'RTOS tasks'),
(49, 'IoT Protocols', 'MQTT, CoAP'),                        -- IoT
(49, 'Sensor Interfacing', 'Sensor integration'),
(50, 'Firmware Design', 'Firmware architecture'),          -- Firmware Development
(50, 'Bootloaders', 'Bootloader development');

-- ------------------------------------------------------------
-- Computer Hardware and Networking Subjects (course 10)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(51, 'Hardware Assembly', 'PC assembly'),                  -- Computer Hardware
(51, 'Troubleshooting', 'Hardware troubleshooting'),
(52, 'Network Setup', 'Network configuration'),            -- Network Administration
(52, 'Server Management', 'Server admin'),
(53, 'Cisco CCNA', 'Cisco certification basics'),          -- Cisco Networking
(53, 'Routing Protocols', 'OSPF, EIGRP'),
(54, 'System Administration', 'Linux/Windows admin'),      -- System Administration
(54, 'Backup & Recovery', 'Backup solutions');

-- ------------------------------------------------------------
-- BE Civil Subjects Skills (course 11)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(55, 'Total Station', 'Modern surveying'),                  -- Surveying
(55, 'Leveling', 'Leveling techniques'),
(56, 'Stiffness Method', 'Structural analysis'),           -- Structural Analysis
(56, 'Moment Distribution', 'Analysis methods'),
(57, 'Mix Design', 'Concrete mix design'),                 -- Concrete Technology
(57, 'Concrete Testing', 'Testing concrete'),
(58, 'Bernoulli Equation', 'Fluid dynamics'),              -- Fluid Mechanics
(58, 'Pipe Flow', 'Fluid in pipes');

-- ------------------------------------------------------------
-- Civil Engineering course Subjects (course 12)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(59, 'AutoCAD', 'CAD drafting'),                           -- Engineering Drawing
(59, 'Technical Drawing', 'Engineering drawing'),
(60, 'Construction Materials', 'Materials technology'),    -- Building Construction
(60, 'Building Methods', 'Construction methods'),
(61, 'Atterberg Limits', 'Soil classification'),           -- Soil Mechanics
(61, 'Consolidation', 'Soil consolidation');

-- ------------------------------------------------------------
-- Structural Engineering Subjects (course 13)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(62, 'Steel Connections', 'Steel design'),                 -- Steel Design
(62, 'IS Codes', 'Indian standards'),
(63, 'RC Detailing', 'Reinforced concrete detailing'),     -- Reinforced Concrete
(63, 'Beam & Column Design', 'RC design'),
(64, 'Seismic Analysis', 'Earthquake-resistant design'),   -- Earthquake Engineering
(64, 'Base Isolation', 'Seismic isolation'),
(65, 'FEA Modeling', 'Finite element analysis'),           -- FEA
(65, 'ABAQUS', 'FEA software');

-- ------------------------------------------------------------
-- Transportation Engineering Subjects (course 14)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(66, 'Highway Design', 'Highway geometry'),                -- Highway Engineering
(66, 'Pavement Design', 'Pavement systems'),
(67, 'Traffic Flow', 'Traffic engineering'),               -- Traffic Engineering
(67, 'Signal Design', 'Traffic signals'),
(68, 'Transport Modeling', 'Travel demand modeling'),      -- Transportation Planning
(68, 'GIS in Transport', 'GIS applications');

-- ------------------------------------------------------------
-- Geotechnical Engineering Subjects (course 15)
-- ------------------------------------------------------------
INSERT INTO acad_skills (subject_id, name, description) VALUES
(69, 'Foundation Design', 'Shallow foundations'),          -- Foundation Engineering
(69, 'Pile Foundations', 'Deep foundations'),
(70, 'Site Investigation', 'Geotechnical site testing'),   -- Geotechnical Investigation
(70, 'SPT Test', 'Standard penetration test');

