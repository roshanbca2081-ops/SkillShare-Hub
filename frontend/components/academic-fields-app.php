<?php
/*
 * Academic Fields App - Shows academic fields grid only.
 * Clicking a field reveals the courses inside that field (inline).
 * Used only by academic-filed.php.
 */
?>

<!-- ============================================
   ACADEMIC FIELDS STYLES
   ============================================ -->
<style>
    .af-page-header { text-align: center; padding: 10px 0 30px; }
    .af-page-header h1 {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 2.8rem;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .af-page-header p { color: var(--text-secondary); font-size: 1.1rem; max-width: 640px; margin: 8px auto 0; }

    .af-search { display: flex; align-items: center; gap: 12px; margin-bottom: 28px; flex-wrap: wrap; }
    .af-search-box { flex: 1; min-width: 200px; display: flex; align-items: center; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-full); padding: 8px 16px; transition: all 0.3s ease; }
    .af-search-box:focus-within { border-color: var(--primary-400); box-shadow: 0 0 0 4px rgba(59,130,246,0.1); }
    .af-search-box input { flex: 1; background: transparent; border: none; padding: 8px 12px; color: var(--text-primary); font-size: 0.9rem; outline: none; }
    .af-search-box input::placeholder { color: var(--text-muted); }
    .af-search-box i { color: var(--text-muted); }

    .af-count { color: var(--text-muted); font-size: 0.85rem; }

    .af-fields-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .af-field-card { background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: var(--radius-2xl); padding: 24px 20px; transition: all 0.4s cubic-bezier(0.175,0.885,0.32,1.275); cursor: pointer; position: relative; overflow: hidden; }
    .af-field-card::before { content: ''; position: absolute; inset: 0; background: var(--gradient-primary); opacity: 0; transition: all 0.4s ease; }
    .af-field-card:hover { transform: translateY(-8px) scale(1.02); border-color: var(--primary-400); box-shadow: 0 15px 50px rgba(59,130,246,0.2); }
    .af-field-card:hover::before { opacity: 0.05; }
    .af-field-card .af-icon { font-size: 3rem; color: var(--primary-400); margin-bottom: 12px; transition: all 0.3s ease; position: relative; z-index: 1; }
    .af-field-card:hover .af-icon { transform: scale(1.1) rotate(-5deg); }
    .af-field-card .af-name { font-weight: 700; font-size: 1.1rem; color: var(--text-primary); position: relative; z-index: 1; }
    .af-field-card .af-desc { color: var(--text-muted); font-size: 0.8rem; margin: 4px 0 10px; position: relative; z-index: 1; }
    .af-field-card .af-stats { display: flex; gap: 16px; position: relative; z-index: 1; }
    .af-field-card .af-stats span { display: flex; align-items: center; gap: 4px; color: var(--text-muted); font-size: 0.75rem; }
    .af-field-card .af-stats span i { color: var(--primary-400); }
    .af-field-card .af-stats span .value { color: var(--text-secondary); font-weight: 500; }
    .af-field-card .af-arrow { position: absolute; bottom: 16px; right: 20px; color: var(--text-muted); transition: all 0.3s ease; z-index: 1; }
    .af-field-card:hover .af-arrow { color: var(--primary-400); transform: translateX(4px); }

    /* Courses (inline detail for selected field) */
    .af-field-detail { margin-top: 8px; }
    .af-back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; margin-bottom: 16px; }
    .af-back-btn:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
    .af-section-title { font-family: var(--font-heading); font-weight: 700; font-size: 1.5rem; color: var(--text-primary); margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
    .af-section-title i { color: var(--primary-400); }
    .af-section-title .count { font-weight: 400; font-size: 1rem; color: var(--text-muted); }
    .af-courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
    .af-subjects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
    .af-section-label { font-family: var(--font-heading); font-weight: 600; font-size: 1.1rem; color: var(--text-primary); margin: 20px 0 12px; display: flex; align-items: center; gap: 8px; }
    .af-section-label i { color: var(--primary-400); }
    .af-subject-tag { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 12px 16px; display: flex; align-items: center; gap: 10px; transition: all 0.3s ease; font-size: 0.85rem; color: var(--text-secondary); }
    .af-subject-tag:hover { background: rgba(255,255,255,0.06); border-color: var(--primary-400); transform: translateY(-2px); color: var(--text-primary); }
    .af-subject-tag i { color: var(--primary-400); font-size: 0.9rem; flex-shrink: 0; }
    .af-course-card { background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 20px; transition: all 0.4s cubic-bezier(0.175,0.885,0.32,1.275); cursor: pointer; text-align: center; }
    .af-course-card:hover { transform: translateY(-6px); background: rgba(255,255,255,0.06); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .af-course-card .ac-icon { width: 56px; height: 56px; border-radius: var(--radius-md); background: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-size: 1.5rem; color: var(--primary-400); transition: all 0.3s ease; }
    .af-course-card:hover .ac-icon { background: var(--gradient-primary); color: #fff; transform: scale(1.1) rotate(-5deg); }
    .af-course-card .ac-name { font-weight: 600; font-size: 0.95rem; color: var(--text-primary); }
    .af-course-card .ac-meta { display: flex; justify-content: center; gap: 12px; margin-top: 8px; font-size: 0.7rem; color: var(--text-muted); }
    .af-course-card .ac-meta span i { color: var(--primary-400); margin-right: 2px; }

    .af-empty { text-align: center; padding: 60px 20px; color: var(--text-muted); grid-column: 1/-1; }
    .af-empty i { font-size: 4rem; display: block; margin-bottom: 16px; opacity: 0.3; }

    @media (max-width: 992px) { .af-fields-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); } }
    @media (max-width: 768px) {
        .af-page-header h1 { font-size: 2rem; }
        .af-fields-grid { grid-template-columns: 1fr 1fr; }
        .af-courses-grid { grid-template-columns: 1fr 1fr; }
        .af-search { flex-direction: column; align-items: stretch; }
    }
    @media (max-width: 480px) {
        .af-fields-grid { grid-template-columns: 1fr; }
        .af-courses-grid { grid-template-columns: 1fr; }
        .af-page-header h1 { font-size: 1.6rem; }
    }
</style>

<!-- Page Header -->
<div class="af-page-header reveal">
    <h1>Academic Fields</h1>
    <p>Explore the available academic fields and discover the courses inside each field</p>
</div>

<!-- Search -->
<div class="af-search reveal">
    <div class="af-search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="afSearchInput" placeholder="Search academic fields..." oninput="afHandleSearch()">
    </div>
    <span class="af-count" id="afCount"></span>
</div>

<!-- Content -->
<div id="afContainer"><!-- Rendered by JavaScript --></div>

<script>
    // ============================================
    // ACADEMIC FIELD DATA (26 fields, each with courses)
    // ============================================
    const afFieldsData = {
        "information-technology": { name: "Information Technology", icon: "fa-laptop-code", color: "#60a5fa", description: "Learn programming, web development, and modern IT skills", mentors: 156, skills: 89, courses: ["BCA", "BSc CSIT", "BIT", "BIM", "Software Engineering", "Computer Engineering", "Information Systems", "Cyber Security", "Data Science", "Artificial Intelligence"], subjects: ["C Programming", "C++", "Java", "Python", "Web Development", "HTML & CSS", "JavaScript", "PHP", "Laravel", "Database Management", "Data Structures", "Algorithms", "Operating Systems", "Computer Networks", "Software Engineering", "Object Oriented Programming", "Mobile App Development", "Cloud Computing", "UI/UX Design", "System Analysis & Design"] },
        "computer-engineering": { name: "Computer Engineering", icon: "fa-microchip", color: "#8b5cf6", description: "Study computer architecture, hardware, and embedded systems", mentors: 98, skills: 56, courses: ["BE Computer", "BCT", "Diploma Computer", "Embedded Systems"], subjects: ["Digital Logic", "Computer Architecture", "Microprocessors", "Embedded Systems", "Programming", "Data Structures", "Algorithms", "Operating Systems", "Computer Networks", "Database Systems", "Microcontrollers", "IoT", "Electronics", "Circuit Theory", "Compiler Design", "Object Oriented Programming", "Software Engineering", "VLSI Design"] },
        "civil-engineering": { name: "Civil Engineering", icon: "fa-building", color: "#22c55e", description: "Design and build infrastructure, structures, and roads", mentors: 112, skills: 62, courses: ["BE Civil", "Diploma Civil", "Structural Engineering", "Transportation Engineering", "Water Resources Engineering"], subjects: ["Applied Mechanics", "Engineering Drawing", "Surveying", "Building Construction", "Concrete Technology", "Structural Analysis", "Soil Mechanics", "Fluid Mechanics", "Hydrology", "Transportation Engineering", "Environmental Engineering", "Geotechnical Engineering", "Estimating & Costing", "Highway Engineering", "Bridge Engineering", "AutoCAD", "Project Management", "Earthquake Engineering"] },
        "mechanical-engineering": { name: "Mechanical Engineering", icon: "fa-cog", color: "#f59e0b", description: "Design and manufacture mechanical systems and machines", mentors: 95, skills: 52, courses: ["BE Mechanical", "Diploma Mechanical", "Automobile Engineering", "Mechatronics"], subjects: ["Engineering Mechanics", "Thermodynamics", "Fluid Mechanics", "Heat Transfer", "Machine Design", "Manufacturing Processes", "Engineering Drawing", "Strength of Materials", "Internal Combustion Engines", "Refrigeration & Air Conditioning", "CAD/CAM", "Robotics", "Materials Science", "Automobile Engineering", "Production Planning", "SolidWorks", "Mechatronics", "Vibrations"] },
        "electrical-engineering": { name: "Electrical Engineering", icon: "fa-bolt", color: "#f59e0b", description: "Study power systems, circuits, and electrical design", mentors: 105, skills: 58, courses: ["BE Electrical", "Diploma Electrical", "Power Engineering", "Power & Electronics"], subjects: ["Circuit Analysis", "Electrical Machines", "Power Systems", "Power Electronics", "Control Systems", "Electromagnetic Fields", "Electrical Measurements", "Digital Electronics", "Analog Electronics", "Microprocessors", "Electrical Design", "PLC & Automation", "Renewable Energy", "Transmission & Distribution", "Electrical Wiring", "MATLAB", "Instrumentation", "Machine Design"] },
        "electronics-engineering": { name: "Electronics & Communication", icon: "fa-satellite-dish", color: "#3b82f6", description: "Study electronic devices, communication systems, and telecom", mentors: 88, skills: 50, courses: ["BE Electronics", "BE Communication", "Telecom Engineering"], subjects: ["Analog Electronics", "Digital Electronics", "Microprocessors", "Communication Systems", "Signal Processing", "Control Systems", "Network Theory", "Electromagnetics", "Microwave Engineering", "Embedded Systems", "VLSI Design", "Optical Communication", "Wireless Communication", "Satellite Communication", "IoT", "MATLAB", "Antenna Theory", "RF Engineering"] },
        "architecture": { name: "Architecture", icon: "fa-drafting-compass", color: "#ec4899", description: "Design buildings, spaces, and urban environments", mentors: 78, skills: 44, courses: ["Bachelor of Architecture", "BSc Architecture", "Interior Design", "Architectural Engineering"], subjects: ["Architectural Design", "Architectural Drawing", "Building Materials", "Construction Technology", "Environmental Design", "Urban Planning", "Landscape Architecture", "Interior Design", "CAD & 3D Modeling", "Structural Systems", "Architectural History", "Sustainable Design", "Model Making", "Site Planning", "Building Services", "Revit", "SketchUp", "Portfolio Design"] },
        "engineering-architecture": { name: "Engineering & Technology", icon: "fa-gears", color: "#3b82f6", description: "Broad engineering and technology disciplines", mentors: 150, skills: 72, courses: ["Aerospace Engineering", "Chemical Engineering", "Petroleum Engineering", "Production Engineering"], subjects: ["Engineering Mathematics", "Applied Mechanics", "Thermodynamics", "Fluid Mechanics", "Materials Science", "Engineering Design", "Manufacturing", "Quality Control", "Safety Engineering", "Robotics", "Automation", "Chemicals Engineering", "Environmental Engineering", "Project Management", "CAD/CAM", "Simulation", "Technical Communication"] },
        "business-administration": { name: "Business Administration", icon: "fa-chart-line", color: "#3b82f6", description: "Learn business management, leadership, and strategy", mentors: 145, skills: 72, courses: ["BBA", "BBM", "MBA", "BIM", "BBS"], subjects: ["Principles of Management", "Business Communication", "Financial Accounting", "Business Economics", "Marketing Management", "Human Resource Management", "Organizational Behavior", "Business Mathematics", "Business Statistics", "Entrepreneurship", "Business Law", "Strategic Management", "Operations Management", "Financial Management", "Consumer Behavior", "Digital Marketing", "Leadership", "Business Ethics"] },
        "management": { name: "Management", icon: "fa-tasks", color: "#8b5cf6", description: "Develop management skills for business and organizations", mentors: 98, skills: 52, courses: ["BBS", "BBA", "MBM"], subjects: ["Management Principles", "Organizational Behavior", "Business Communication", "Financial Management", "Marketing Management", "Human Resource Management", "Strategic Management", "Operations Management", "Business Law", "Business Statistics", "Entrepreneurship", "Leadership", "Project Management", "Business Ethics", "International Business", "Decision Making", "Negotiation", "Supply Chain Management"] },
        "commerce": { name: "Commerce", icon: "fa-coins", color: "#f59e0b", description: "Study accounting, finance, and business commerce", mentors: 112, skills: 58, courses: ["BCom", "BBS", "MBS"], subjects: ["Financial Accounting", "Cost Accounting", "Management Accounting", "Business Law", "Taxation", "Auditing", "Business Economics", "Business Mathematics", "Business Statistics", "Corporate Law", "Financial Management", "Banking & Insurance", "Company Law", "E-Commerce", "Entrepreneurship", "Economics", "Marketing", "Tally & Accounting Software"] },
        "economics": { name: "Economics", icon: "fa-chart-column", color: "#3b82f6", description: "Study economic theory, policy, and analysis", mentors: 68, skills: 42, courses: ["BA Economics", "BSc Economics", "MSc Economics", "Development Economics"], subjects: ["Microeconomics", "Macroeconomics", "Econometrics", "Development Economics", "International Economics", "Monetary Economics", "Public Finance", "Industrial Economics", "Agricultural Economics", "Mathematical Economics", "Statistics for Economics", "Labor Economics", "Environmental Economics", "Economic Thought", "Research Methodology", "Financial Economics"] },
        "tourism-hospitality": { name: "Tourism & Hospitality", icon: "fa-umbrella-beach", color: "#14b8a6", description: "Study travel, hospitality management, and services", mentors: 88, skills: 50, courses: ["BHM", "BTS", "Travel & Tourism Management", "Culinary Arts"], subjects: ["Front Office Management", "Housekeeping Operations", "Food & Beverage Service", "Culinary Arts", "Hotel Management", "Tourism Principles", "Travel Agency Operation", "Tour Guiding", "Airline & Travel Management", "Hospitality Marketing", "Customer Service", "Food Safety", "Event Management", "Reservation Systems", "Tourism Economics", "Cross-Cultural Communication", "Sustainability in Tourism"] },
        "science": { name: "Science", icon: "fa-flask", color: "#8b5cf6", description: "Study natural sciences, research, and laboratory techniques", mentors: 88, skills: 48, courses: ["BSc Physics", "BSc Chemistry", "BSc Biology", "BSc Biotechnology"], subjects: ["Physics", "Chemistry", "Biology", "Mathematics", "Microbiology", "Biochemistry", "Molecular Biology", "Genetics", "Organic Chemistry", "Inorganic Chemistry", "Physical Chemistry", "Quantum Mechanics", "Thermodynamics", "Biotechnology", "Cell Biology", "Ecology", "Statistics", "Research Methodology"] },
        "mathematics": { name: "Mathematics & Statistics", icon: "fa-square-root-variable", color: "#8b5cf6", description: "Study pure and applied mathematics and data analysis", mentors: 70, skills: 44, courses: ["BSc Mathematics", "BSc Statistics", "Actuarial Science", "Math & Computing"], subjects: ["Calculus", "Linear Algebra", "Abstract Algebra", "Real Analysis", "Complex Analysis", "Differential Equations", "Probability Theory", "Statistics", "Number Theory", "Discrete Mathematics", "Mathematical Modeling", "Optimization", "Numerical Methods", "Econometrics", "Data Analysis", "Python", "R Programming", "Graph Theory"] },
        "agriculture": { name: "Agriculture & Forestry", icon: "fa-seedling", color: "#22c55e", description: "Study crop science, forestry, and agribusiness", mentors: 82, skills: 48, courses: ["BSc Agriculture", "BSc Forestry", "BSc Horticulture", "Agricultural Business"], subjects: ["Crop Science", "Agronomy", "Soil Science", "Plant Pathology", "Horticulture", "Entomology", "Agricultural Economics", "Agribusiness", "Irrigation Management", "Farming Systems", "Forestry", "Wildlife Management", "Agroforestry", "Plant Breeding", "Organic Farming", "Agricultural Marketing", "Livestock Management"] },
        "veterinary": { name: "Veterinary Science", icon: "fa-paw", color: "#f59e0b", description: "Study animal health, medicine, and care", mentors: 65, skills: 42, courses: ["BVSc & AH", "Animal Science", "Veterinary Technology"], subjects: ["Veterinary Anatomy", "Animal Physiology", "Veterinary Pathology", "Veterinary Pharmacology", "Animal Nutrition", "Livestock Management", "Animal Husbandry", "Surgery", "Disease Diagnosis", "Vaccination", "Animal Reproduction", "Poultry Science", "Dairy Science", "Veterinary Microbiology", "Parasitology", "Zoonotic Diseases", "Clinical Practice"] },
        "nursing": { name: "Nursing", icon: "fa-user-nurse", color: "#ec4899", description: "Learn patient care, clinical skills, and healthcare", mentors: 120, skills: 64, courses: ["BSc Nursing", "BN", "Midwifery", "Community Nursing"], subjects: ["Anatomy", "Physiology", "Microbiology", "Pathology", "Pharmacology", "Nursing Fundamentals", "Medical-Surgical Nursing", "Pediatric Nursing", "Maternal Health", "Mental Health Nursing", "Community Health", "Geriatric Care", "Emergency Nursing", "Nutrition & Dietetics", "First Aid", "Nursing Ethics", "Clinical Practice", "Patient Care"] },
        "pharmacy": { name: "Pharmacy", icon: "fa-pills", color: "#22c55e", description: "Study medicines, pharmacology, and drug management", mentors: 78, skills: 46, courses: ["BPharm", "PharmD", "Pharmaceutical Science"], subjects: ["Pharmacology", "Pharmaceutical Chemistry", "Pharmacognosy", "Pharmaceutics", "Clinical Pharmacy", "Pharmaceutical Analysis", "Pharmacokinetics", "Medicinal Chemistry", "Drug Formulation", "Pharmacodynamics", "Hospital Pharmacy", "Community Pharmacy", "Toxicology", "Regulatory Affairs", "Biopharmaceutics", "Quality Control", "Pharmacovigilance"] },
        "medicine-health": { name: "Medicine & Health Sciences", icon: "fa-stethoscope", color: "#ef4444", description: "Study medicine, public health, and clinical sciences", mentors: 165, skills: 80, courses: ["MBBS", "BDS", "BPH", "BMLT", "BSc Physiotherapy"], subjects: ["Anatomy", "Physiology", "Biochemistry", "Pathology", "Microbiology", "Pharmacology", "Medicine", "Surgery", "Pediatrics", "Gynecology", "Obstetrics", "Public Health", "Community Medicine", "Epidemiology", "Diagnostics", "Medical Ethics", "Clinical Practice", "Physiotherapy"] },
        "law": { name: "Law", icon: "fa-scale-balanced", color: "#8b5cf6", description: "Study legal systems, justice, and advocacy", mentors: 95, skills: 52, courses: ["LLB", "BA LLB", "BBL", "LLM"], subjects: ["Constitutional Law", "Criminal Law", "Civil Law", "Contract Law", "Corporate Law", "Labor Law", "Property Law", "Family Law", "Tax Law", "Human Rights", "International Law", "Legal Writing", "Jurisprudence", "Evidence Law", "Cyber Law", "Intellectual Property", "Legal Research", "Moot Court"] },
        "education-teaching": { name: "Education & Teaching", icon: "fa-chalkboard-user", color: "#3b82f6", description: "Prepare for a career in teaching and education", mentors: 85, skills: 48, courses: ["BEd", "BA Education", "MEd", "ELT"], subjects: ["Educational Psychology", "Pedagogy", "Curriculum Design", "Teaching Methods", "Child Development", "Assessment & Evaluation", "Educational Technology", "Classroom Management", "Special Education", "Educational Research", "Inclusive Education", "Learning Theories", "Educational Leadership", "Instructional Design", "Communication Skills", "Educational Policy", "Micro Teaching"] },
        "humanities": { name: "Humanities & Social Sciences", icon: "fa-user-graduate", color: "#14b8a6", description: "Study society, culture, language, and history", mentors: 72, skills: 40, courses: ["BA English", "BA Social Work", "BA Sociology", "BA Psychology"], subjects: ["English Literature", "Creative Writing", "Linguistics", "Sociology", "Social Work", "Psychology", "Political Science", "History", "Philosophy", "Anthropology", "Cultural Studies", "Critical Thinking", "Academic Writing", "Research Methods", "Communication", "Human Rights"] },
        "arts-design": { name: "Arts & Design", icon: "fa-palette", color: "#ec4899", description: "Study visual arts, graphic design, and creative media", mentors: 75, skills: 46, courses: ["BFA", "Graphic Design", "Multimedia", "Fashion Design"], subjects: ["Drawing & Sketching", "Painting", "Color Theory", "Graphic Design", "Typography", "Photoshop", "Illustrator", "Digital Art", "Illustration", "Photography", "Video Editing", "Adobe After Effects", "Animation", "Fashion Design", "Textile Design", "Branding", "Portfolio Development", "Creative Thinking"] },
        "journalism-media": { name: "Journalism & Media", icon: "fa-newspaper", color: "#f59e0b", description: "Study reporting, digital media, and broadcasting", mentors: 62, skills: 40, courses: ["BJMC", "BA Journalism", "Mass Communication"], subjects: ["News Reporting", "Feature Writing", "Editing", "Mass Communication", "Broadcasting", "Media Ethics", "Public Relations", "Digital Media", "Social Media", "Photography", "Video Production", "Content Writing", "Copywriting", "Media Law", "Investigative Journalism", "Interviewing", "Podcasting"] },
        "psychology": { name: "Psychology", icon: "fa-brain", color: "#8b5cf6", description: "Study human mind, behavior, and counseling", mentors: 70, skills: 44, courses: ["BSc Psychology", "BA Psychology", "MA Clinical Psychology"], subjects: ["Cognitive Psychology", "Developmental Psychology", "Social Psychology", "Abnormal Psychology", "Clinical Psychology", "Counseling Psychology", "Psychological Assessment", "Behavioral Psychology", "Research Methods", "Statistics in Psychology", "Psychometrics", "Human Development", "Physiological Psychology", "Personality", "Motivation & Emotion", "Psychotherapy", "Ethics in Psychology"] }

    };

    // ============================================
    // STATE
    // ============================================
    let afSelectedField = null;
    let afSearchQuery = '';

    function afRenderFields() {
        const container = document.getElementById('afContainer');
        const fields = Object.values(afFieldsData);

        let filtered = fields;
        if (afSearchQuery) {
            const q = afSearchQuery.toLowerCase();
            filtered = filtered.filter(function (f) {
                return f.name.toLowerCase().indexOf(q) !== -1 || f.description.toLowerCase().indexOf(q) !== -1;
            });
        }

        document.getElementById('afCount').textContent = filtered.length + ' of ' + fields.length + ' fields';

        if (filtered.length === 0) {
            container.innerHTML = '<div class="af-empty"><i class="fas fa-search"></i><h4>No academic fields found</h4><p>Try adjusting your search</p></div>';
            return;
        }

        container.innerHTML = '<div class="af-fields-grid">' + filtered.map(function (field) {
            return '<div class="af-field-card" onclick="afSelectField(\'' + field.name.replace(/'/g, "\\'") + '\')">' +
                '<div class="af-icon" style="color:' + field.color + '"><i class="fas ' + field.icon + '"></i></div>' +
                '<div class="af-name">' + field.name + '</div>' +
                '<div class="af-desc">' + field.description + '</div>' +
                '<div class="af-stats"><span><i class="fas fa-users"></i> <span class="value">' + field.mentors + '</span> Mentors</span><span><i class="fas fa-graduation-cap"></i> <span class="value">' + field.courses.length + '</span> Courses</span></div>' +
                '<div class="af-arrow"><i class="fas fa-chevron-right"></i></div></div>';
        }).join('') + '</div>';
    }

    function afSelectField(fieldName) {
        const field = Object.values(afFieldsData).find(function (f) { return f.name === fieldName; });
        if (!field) return;
        afSelectedField = field;
        const container = document.getElementById('afContainer');

        const subjects = field.subjects || [];

        container.innerHTML = '<div class="af-field-detail">' +
            '<button class="af-back-btn" onclick="afReset()"><i class="fas fa-arrow-left"></i> Back to Academic Fields</button>' +
            '<div class="af-section-title"><i class="fas ' + field.icon + '" style="color:' + field.color + '"></i> ' + field.name + ' <span class="count">' + field.courses.length + ' courses • ' + subjects.length + ' subjects</span></div>' +

            '<div class="af-section-label"><i class="fas fa-graduation-cap"></i> Courses (' + field.courses.length + ')</div>' +
            '<div class="af-courses-grid">' + field.courses.map(function (course) {
                return '<div class="af-course-card" onclick="showToast(\'Info\', \'' + course.replace(/'/g, "\\'") + ' course details coming soon!\', \'info\')">' +
                    '<div class="ac-icon" style="color:' + field.color + '"><i class="fas fa-graduation-cap"></i></div>' +
                    '<div class="ac-name">' + course + '</div>' +
                    '<div class="ac-meta"><span><i class="fas fa-users"></i> Mentors</span><span><i class="fas fa-tools"></i> Skills</span></div></div>';
            }).join('') + '</div>' +

            '<div class="af-section-label"><i class="fas fa-book-open"></i> Subjects (' + subjects.length + ')</div>' +
            '<div class="af-subjects-grid">' + subjects.map(function (subject) {
                return '<div class="af-subject-tag"><i class="fas fa-book"></i> ' + subject + '</div>';
            }).join('') + '</div>' +

            '</div>';

        document.getElementById('afCount').textContent = 'Showing courses & subjects for ' + field.name;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function afReset() {
        afSelectedField = null;
        afSearchQuery = '';
        const input = document.getElementById('afSearchInput');
        if (input) input.value = '';
        afRenderFields();
    }

    function afHandleSearch() {
        const input = document.getElementById('afSearchInput');
        afSearchQuery = input.value.trim();
        if (!afSelectedField) afRenderFields();
    }

    // ============================================
    // INIT
    // ============================================
    document.addEventListener('DOMContentLoaded', function () {
        afRenderFields();
    });

    console.log('🏛️ SkillShare Hub - Academic Fields (' + Object.keys(afFieldsData).length + ' fields)');
</script>

