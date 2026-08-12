<?php
/*
 * Academic Fields (Name only) App
 * Shows ONLY the academic field names initially.
 * Clicking a field name opens its course section where you can explore courses.
 * Used only by academic-filed.php. Keeps shared navbar.
 */
?>

<style>
    .afn-page-header { text-align: center; padding: 20px 0 30px; }
    .afn-page-header h1 {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 2.8rem;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 8px;
    }
    .afn-page-header p { color: var(--text-secondary); font-size: 1.1rem; max-width: 640px; margin: 0 auto; }

    /* Search bar */
    .afn-search-wrap { max-width: 480px; margin: 0 auto 30px; }
    .afn-search-box {
        display: flex; align-items: center; gap: 10px;
        background: var(--glass-bg); border: 1px solid var(--glass-border);
        border-radius: var(--radius-full); padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .afn-search-box:focus-within { border-color: var(--primary-400); box-shadow: 0 0 0 4px rgba(59,130,246,0.1); }
    .afn-search-box i { color: var(--text-muted); }
    .afn-search-box input {
        flex: 1; background: transparent; border: none; outline: none;
        color: var(--text-primary); font-size: 0.9rem; font-family: var(--font-primary);
    }
    .afn-search-box input::placeholder { color: var(--text-muted); }

    /* Fields (name only) */
    .afn-fields-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 18px; }
    .afn-field-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-xl);
        padding: 24px 18px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        text-align: center;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        min-height: 130px;
    }
    .afn-field-card::before { content: ''; position: absolute; inset: 0; background: var(--gradient-primary); opacity: 0; transition: all 0.4s ease; }
    .afn-field-card:hover { transform: translateY(-6px) scale(1.02); border-color: var(--primary-400); box-shadow: 0 15px 50px rgba(59,130,246,0.2); }
    .afn-field-card:hover::before { opacity: 0.05; }
    .afn-field-card .afn-icon { font-size: 2.2rem; color: var(--primary-400); position: relative; z-index: 1; transition: all 0.3s ease; }
    .afn-field-card:hover .afn-icon { transform: scale(1.1) rotate(-5deg); }
    .afn-field-card .afn-name { font-weight: 600; font-size: 1rem; color: var(--text-primary); position: relative; z-index: 1; line-height: 1.3; }
    .afn-field-card .afn-arrow { position: absolute; bottom: 12px; right: 14px; color: var(--text-muted); transition: all 0.3s ease; z-index: 1; font-size: 0.8rem; }
    .afn-field-card:hover .afn-arrow { color: var(--primary-400); transform: translateX(4px); }

    /* Courses view */
    .afn-back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; margin-bottom: 20px; }
    .afn-back-btn:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
    .afn-section-title { font-family: var(--font-heading); font-weight: 700; font-size: 1.5rem; color: var(--text-primary); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .afn-section-title i { color: var(--primary-400); }
    .afn-section-title .count { font-weight: 400; font-size: 1rem; color: var(--text-muted); }
    .afn-courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 18px; }
    .afn-course-card { background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 24px 18px; transition: all 0.4s cubic-bezier(0.175,0.885,0.32,1.275); cursor: pointer; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .afn-course-card:hover { transform: translateY(-6px); background: rgba(255,255,255,0.06); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .afn-course-card .afn-c-icon { width: 56px; height: 56px; border-radius: var(--radius-md); background: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--primary-400); transition: all 0.3s ease; }
    .afn-course-card:hover .afn-c-icon { background: var(--gradient-primary); color: #fff; transform: scale(1.1) rotate(-5deg); }
    .afn-course-card .afn-c-name { font-weight: 600; font-size: 0.95rem; color: var(--text-primary); }
    .afn-course-card .afn-c-meta { display: flex; justify-content: center; gap: 12px; font-size: 0.75rem; color: var(--text-muted); }
    .afn-course-card .afn-c-meta span i { color: var(--primary-400); margin-right: 4px; }

    /* Subjects view */
    .afn-subjects-wrap { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 24px; }
    .afn-subjects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; }
    .afn-subject-tag { background: rgba(255,255,255,0.04); border: 1px solid var(--glass-border); border-radius: var(--radius-md); padding: 12px 16px; display: flex; align-items: center; gap: 10px; transition: all 0.3s ease; font-size: 0.85rem; color: var(--text-secondary); }
    .afn-subject-tag:hover { background: rgba(255,255,255,0.08); border-color: var(--primary-400); transform: translateY(-2px); color: var(--text-primary); }
    .afn-subject-tag i { color: var(--primary-400); font-size: 0.9rem; flex-shrink: 0; }

    .afn-empty { text-align: center; padding: 60px 20px; color: var(--text-muted); grid-column: 1/-1; }
    .afn-empty i { font-size: 4rem; display: block; margin-bottom: 16px; opacity: 0.3; }

    @media (max-width: 768px) {
        .afn-page-header h1 { font-size: 2rem; }
        .afn-fields-grid { grid-template-columns: 1fr 1fr; }
        .afn-courses-grid { grid-template-columns: 1fr 1fr; }
        .afn-subjects-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 480px) {
        .afn-page-header h1 { font-size: 1.6rem; }
        .afn-fields-grid { grid-template-columns: 1fr; }
        .afn-courses-grid { grid-template-columns: 1fr; }
        .afn-subjects-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- Page Header -->
<div class="afn-page-header reveal">
    <h1>Academic Fields</h1>
    <p>Choose an academic field to explore its courses and subjects</p>
</div>

<!-- Search -->
<div class="afn-search-wrap reveal">
    <div class="afn-search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search academic fields..." id="afnSearchInput" oninput="afnFilterFields()">
    </div>
</div>

<!-- Fields (Name only) -->
<div id="afnFieldsContainer"><!-- rendered by JS --></div>

<!-- Courses for selected field -->
<div id="afnCoursesContainer" style="display:none;"></div>

<script>
    // ============================================
    // FIELDS (name + courses for drill-down)
    // ============================================
const afnFieldsData = {
        "information-technology": { name: "Information Technology", icon: "fa-laptop-code", color: "#60a5fa", courses: [
            { name: "BCA", subjects: ["English", "Mathematics", "C Programming", "Data Structures", "Database Management", "Operating Systems", "Web Technology", "Software Engineering", "Computer Networks", "Object Oriented Programming", "Java", "Python"] },
            { name: "BSc CSIT", subjects: ["Discrete Structure", "Calculus", "Computer Architecture", "C Programming", "Data Structures", "Operating Systems", "Computer Networks", "Database Systems", "Software Engineering", "Artificial Intelligence", "Machine Learning", "Web Technology"] },
            { name: "BIT", subjects: ["Programming", "Database", "Web Development", "Networking", "System Analysis", "Project Management", "Mobile Development", "Cloud Computing", "Data Analytics", "Information Systems"] },
            { name: "BIM", subjects: ["Business Statistics", "Financial Accounting", "Programming", "Database", "Marketing", "E-Commerce", "Web Technology", "Management Information Systems", "Human Resource Management"] },
            { name: "Software Engineering", subjects: ["Software Requirements", "System Design", "Testing", "Project Management", "Agile", "DevOps", "Cloud", "APIs", "Databases", "Security", "UI/UX"] },
            { name: "Computer Engineering", subjects: ["Digital Logic", "Computer Architecture", "Microprocessors", "Embedded Systems", "Programming", "Data Structures", "Operating Systems", "Networks", "Microcontrollers", "IoT"] },
            { name: "Information Systems", subjects: ["Systems Analysis", "Database Design", "Networking", "Web Development", "Business Process", "Data Management", "Security", "Project Management"] },
            { name: "Cyber Security", subjects: ["Network Security", "Cryptography", "Ethical Hacking", "Digital Forensics", "Security Compliance", "Risk Management", "Penetration Testing", "Firewalls"] },
            { name: "Data Science", subjects: ["Python", "Statistics", "Machine Learning", "Data Visualization", "Big Data", "SQL", "Deep Learning", "Data Mining", "Probability", "Analytics"] },
            { name: "Artificial Intelligence", subjects: ["AI Fundamentals", "Machine Learning", "Deep Learning", "Natural Language Processing", "Computer Vision", "Reinforcement Learning", "Neural Networks", "Data Science"] }
        ] },
        "computer-engineering": { name: "Computer Engineering", icon: "fa-microchip", color: "#8b5cf6", courses: [
            { name: "BE Computer", subjects: ["Digital Logic", "Computer Architecture", "Microprocessors", "Embedded Systems", "Programming", "Data Structures", "Algorithms", "Operating Systems", "Networks", "Databases"] },
            { name: "BCT", subjects: ["Circuit Theory", "Electronics", "Digital Systems", "Programming", "Data Structures", "Operating Systems", "Networks", "Microcontrollers", "Embedded Systems", "IoT"] },
            { name: "Diploma Computer", subjects: ["C Programming", "Computer Fundamentals", "Digital Electronics", "Networking", "Database", "Web Basics", "Operating Systems"] },
            { name: "Embedded Systems", subjects: ["Microcontrollers", "RTOS", "Embedded C", "IoT", "Hardware Design", "Sensor Interfacing", "Firmware", "PCB Design"] }
        ] },
        "civil-engineering": { name: "Civil Engineering", icon: "fa-building", color: "#22c55e", courses: [
            { name: "BE Civil", subjects: ["Applied Mechanics", "Engineering Drawing", "Surveying", "Building Construction", "Concrete Technology", "Structural Analysis", "Soil Mechanics", "Fluid Mechanics", "Transportation Engineering", "Environmental Engineering"] },
            { name: "Diploma Civil", subjects: ["Engineering Drawing", "Surveying", "Concrete Technology", "Building Construction", "Estimation", "Soil Mechanics", "Basic Structures"] },
            { name: "Structural Engineering", subjects: ["Structural Analysis", "Steel Design", "Reinforced Concrete", "Earthquake Engineering", "Bridge Engineering", "Finite Element Analysis", "Structural Dynamics"] },
            { name: "Transportation Engineering", subjects: ["Highway Engineering", "Traffic Engineering", "Transportation Planning", "Pavement Design", "Railway Engineering", "Airport Engineering"] },
            { name: "Water Resources Engineering", subjects: ["Hydrology", "Hydraulic Structures", "Irrigation", "Water Supply", "Flood Control", "Environmental Hydraulics", "Dams & Reservoirs"] }
        ] },
        "mechanical-engineering": { name: "Mechanical Engineering", icon: "fa-cog", color: "#f59e0b", courses: [
            { name: "BE Mechanical", subjects: ["Engineering Mechanics", "Thermodynamics", "Fluid Mechanics", "Heat Transfer", "Machine Design", "Manufacturing", "Materials Science", "Dynamics", "Vibrations"] },
            { name: "Diploma Mechanical", subjects: ["Engineering Drawing", "Thermodynamics", "Machine Shop", "Fluid Mechanics", "Manufacturing", "Mechanics", "Wiring & Fitting"] },
            { name: "Automobile Engineering", subjects: ["Internal Combustion Engines", "Chassis", "Suspension", "Braking Systems", "Transmission", "Vehicle Dynamics", "Engine Design"] },
            { name: "Mechatronics", subjects: ["Robotics", "Control Systems", "Sensors & Actuators", "PLC", "Embedded Systems", "Mechatronics Design", "Automation"] }
        ] },
        "electrical-engineering": { name: "Electrical Engineering", icon: "fa-bolt", color: "#f59e0b", courses: [
            { name: "BE Electrical", subjects: ["Circuit Analysis", "Electrical Machines", "Power Systems", "Power Electronics", "Control Systems", "Electromagnetics", "Electrical Measurements", "Digital Electronics"] },
            { name: "Diploma Electrical", subjects: ["Basic Electrical", "Electrical Machines", "Wiring", "Power Systems", "Measurements", "Control", "Electronics"] },
            { name: "Power Engineering", subjects: ["Power Generation", "Transmission", "Distribution", "Switchgear", "Protection", "Renewable Energy", "Power Quality"] },
            { name: "Power & Electronics", subjects: ["Power Electronics", "Drives", "Converters", "Control", "Power Systems", "Electronics", "Embedded Systems"] }
        ] },
        "electronics-engineering": { name: "Electronics & Communication", icon: "fa-satellite-dish", color: "#3b82f6", courses: [
            { name: "BE Electronics", subjects: ["Analog Electronics", "Digital Electronics", "Microprocessors", "Communication", "Signal Processing", "Control Systems", "VLSI", "Embedded Systems"] },
            { name: "BE Communication", subjects: ["Communication Systems", "Digital Communication", "Wireless Communication", "Signal Processing", "Antenna Theory", "Networking", "Microwave"] },
            { name: "Telecom Engineering", subjects: ["Telecommunication", "Switching", "Optical Communication", "Satellite Communication", "Mobile Networks", "Network Security"] }
        ] },
        "architecture": { name: "Architecture", icon: "fa-drafting-compass", color: "#ec4899", courses: [
            { name: "Bachelor of Architecture", subjects: ["Architectural Design", "Drawing", "Building Materials", "Construction", "Urban Planning", "History", "Digital Modeling", "Sustainable Design"] },
            { name: "BSc Architecture", subjects: ["Design Studio", "Architectural Drawing", "Building Technology", "Environmental Design", "CAD", "History of Architecture"] },
            { name: "Interior Design", subjects: ["Space Planning", "Color Theory", "Furniture Design", "Lighting", "Materials", "3D Modeling", "Portfolio"] },
            { name: "Architectural Engineering", subjects: ["Structural Systems", "Building Services", "Construction", "Environmental Systems", "Design", "Project Management"] }
        ] },
        "engineering-architecture": { name: "Engineering & Technology", icon: "fa-gears", color: "#3b82f6", courses: [
            { name: "Aerospace Engineering", subjects: ["Aerodynamics", "Propulsion", "Flight Mechanics", "Structures", "Materials", "Control Systems", "Spacecraft Design"] },
            { name: "Chemical Engineering", subjects: ["Chemical Thermodynamics", "Fluid Mechanics", "Heat Transfer", "Mass Transfer", "Reaction Engineering", "Process Control", "Separation"] },
            { name: "Petroleum Engineering", subjects: ["Reservoir Engineering", "Drilling", "Production", "Petroleum Geology", "Well Testing", "Formation Evaluation"] },
            { name: "Production Engineering", subjects: ["Manufacturing", "Production Planning", "Quality Control", "Automation", "CAD/CAM", "Industrial Management"] }
        ] },
        "business-administration": { name: "Business Administration", icon: "fa-chart-line", color: "#3b82f6", courses: [
            { name: "BBA", subjects: ["Management", "Marketing", "Finance", "Accounting", "Business Communication", "Economics", "Human Resources", "Entrepreneurship", "Business Law"] },
            { name: "BBM", subjects: ["Business Management", "Marketing", "Finance", "Accounting", "Economics", "Statistics", "Operations", "Leadership"] },
            { name: "MBA", subjects: ["Strategic Management", "Financial Management", "Marketing Management", "Operations", "Organizational Behavior", "Business Analytics", "Leadership", "Entrepreneurship"] },
            { name: "BIM", subjects: ["Business Statistics", "Programming", "Database", "E-Commerce", "Marketing", "Management Information Systems", "Web Technology"] },
            { name: "BBS", subjects: ["Business English", "Accounting", "Economics", "Management", "Finance", "Marketing", "Business Law", "Statistics"] }
        ] },
        "management": { name: "Management", icon: "fa-tasks", color: "#8b5cf6", courses: [
            { name: "BBS", subjects: ["Management", "Accounting", "Economics", "Finance", "Marketing", "Business Law", "Statistics", "English"] },
            { name: "BBA", subjects: ["Management", "Marketing", "Finance", "Accounting", "HR", "Economics", "Operations", "Communication"] },
            { name: "MBM", subjects: ["Strategic Management", "Operations", "HR Management", "Marketing", "Finance", "Leadership", "Research"] }
        ] },
        "commerce": { name: "Commerce", icon: "fa-coins", color: "#f59e0b", courses: [
            { name: "BCom", subjects: ["Financial Accounting", "Cost Accounting", "Business Law", "Economics", "Taxation", "Auditing", "Management Accounting", "Business Statistics"] },
            { name: "BBS", subjects: ["Accounting", "Economics", "Management", "Finance", "Marketing", "Business Law", "Statistics", "English"] },
            { name: "MBS", subjects: ["Advanced Accounting", "Financial Management", "Taxation", "Auditing", "Economics", "Research Methods", "Business Environment"] }
        ] },
        "economics": { name: "Economics", icon: "fa-chart-column", color: "#3b82f6", courses: [
            { name: "BA Economics", subjects: ["Microeconomics", "Macroeconomics", "Statistics", "Econometrics", "Development Economics", "International Economics", "Public Finance"] },
            { name: "BSc Economics", subjects: ["Microeconomics", "Macroeconomics", "Mathematics", "Statistics", "Econometrics", "Economic Theory", "Data Analysis"] },
            { name: "MSc Economics", subjects: ["Advanced Econometrics", "Economic Policy", "Development Economics", "International Trade", "Mathematical Economics", "Research"] },
            { name: "Development Economics", subjects: ["Economic Development", "Poverty Analysis", "Agriculture Economics", "Health Economics", "Education Economics", "Policy"] }
        ] },
        "tourism-hospitality": { name: "Tourism & Hospitality", icon: "fa-umbrella-beach", color: "#14b8a6", courses: [
            { name: "BHM", subjects: ["Front Office", "Housekeeping", "Food & Beverage", "Hotel Management", "Culinary", "Hospitality Marketing", "Customer Service"] },
            { name: "BTS", subjects: ["Tourism Principles", "Travel Operations", "Tour Guiding", "Reservation", "Airline Management", "Tourism Marketing"] },
            { name: "Travel & Tourism Management", subjects: ["Tourism Planning", "Destination Management", "Travel Agency", "Event Management", "Eco-Tourism", "Tourism Economics"] },
            { name: "Culinary Arts", subjects: ["Cooking Techniques", "Baking", "Food Safety", "Menu Planning", "Kitchen Management", "Garde Manger", "Pastry"] }
        ] },
        "science": { name: "Science", icon: "fa-flask", color: "#8b5cf6", courses: [
            { name: "BSc Physics", subjects: ["Mechanics", "Electromagnetism", "Quantum Mechanics", "Thermodynamics", "Optics", "Solid State Physics", "Mathematical Physics"] },
            { name: "BSc Chemistry", subjects: ["Organic Chemistry", "Inorganic Chemistry", "Physical Chemistry", "Analytical Chemistry", "Biochemistry", "Lab Techniques"] },
            { name: "BSc Biology", subjects: ["Cell Biology", "Genetics", "Ecology", "Zoology", "Botany", "Microbiology", "Molecular Biology"] },
            { name: "BSc Biotechnology", subjects: ["Biochemistry", "Molecular Biology", "Microbiology", "Genetics", "Bioinformatics", "Cell Culture", "Immunology"] }
        ] },
        "mathematics": { name: "Mathematics & Statistics", icon: "fa-square-root-variable", color: "#8b5cf6", courses: [
            { name: "BSc Mathematics", subjects: ["Calculus", "Linear Algebra", "Abstract Algebra", "Real Analysis", "Differential Equations", "Number Theory", "Probability"] },
            { name: "BSc Statistics", subjects: ["Probability", "Statistical Inference", "Regression", "Sampling", "Time Series", "Data Analysis", "R Programming"] },
            { name: "Actuarial Science", subjects: ["Probability", "Financial Mathematics", "Risk Theory", "Life Insurance", "Pensions", "Statistics", "Economics"] },
            { name: "Math & Computing", subjects: ["Discrete Mathematics", "Programming", "Algorithms", "Data Structures", "Linear Algebra", "Numerical Methods"] }
        ] },
        "agriculture": { name: "Agriculture & Forestry", icon: "fa-seedling", color: "#22c55e", courses: [
            { name: "BSc Agriculture", subjects: ["Crop Science", "Agronomy", "Soil Science", "Horticulture", "Plant Pathology", "Entomology", "Agricultural Economics", "Irrigation"] },
            { name: "BSc Forestry", subjects: ["Forest Ecology", "Silviculture", "Wildlife Management", "Forest Inventory", "Agroforestry", "Forest Policy", "Dendrology"] },
            { name: "BSc Horticulture", subjects: ["Fruit Science", "Vegetable Science", "Floriculture", "Plantation", "Post-Harvest", "Greenhouse"] },
            { name: "Agricultural Business", subjects: ["Agribusiness", "Farm Management", "Agricultural Marketing", "Supply Chain", "Agri-Finance", "Entrepreneurship"] }
        ] },
        "veterinary": { name: "Veterinary Science", icon: "fa-paw", color: "#f59e0b", courses: [
            { name: "BVSc & AH", subjects: ["Veterinary Anatomy", "Animal Physiology", "Pharmacology", "Pathology", "Surgery", "Animal Nutrition", "Microbiology", "Clinical Practice"] },
            { name: "Animal Science", subjects: ["Livestock Management", "Animal Nutrition", "Breeding", "Poultry Science", "Dairy Science", "Animal Welfare"] },
            { name: "Veterinary Technology", subjects: ["Lab Diagnostics", "Imaging", "Anesthesia", "Animal Care", "Surgical Support", "Clinical Pathology"] }
        ] },
        "nursing": { name: "Nursing", icon: "fa-user-nurse", color: "#ec4899", courses: [
            { name: "BSc Nursing", subjects: ["Anatomy", "Physiology", "Microbiology", "Pharmacology", "Medical-Surgical Nursing", "Pediatric Nursing", "Maternal Health", "Community Health"] },
            { name: "BN", subjects: ["Nursing Fundamentals", "Pathology", "Pharmacology", "Mental Health", "Community Nursing", "Clinical Practice", "Ethics"] },
            { name: "Midwifery", subjects: ["Antenatal Care", "Postnatal Care", "Labor Management", "Newborn Care", "Reproductive Health", "Family Planning"] },
            { name: "Community Nursing", subjects: ["Community Health", "Epidemiology", "Health Promotion", "Public Health", "Home Care", "Health Education"] }
        ] },
        "pharmacy": { name: "Pharmacy", icon: "fa-pills", color: "#22c55e", courses: [
            { name: "BPharm", subjects: ["Pharmacology", "Pharmaceutical Chemistry", "Pharmacognosy", "Pharmaceutics", "Pharmacokinetics", "Medicinal Chemistry", "Clinical Pharmacy"] },
            { name: "PharmD", subjects: ["Clinical Pharmacology", "Drug Interactions", "Pharmacotherapeutics", "Hospital Pharmacy", "Patient Care", "Pharmacovigilance"] },
            { name: "Pharmaceutical Science", subjects: ["Drug Formulation", "Pharmaceutical Analysis", "Quality Control", "GMP", "Biopharmaceutics", "Regulatory Affairs"] }
        ] },
        "medicine-health": { name: "Medicine & Health Sciences", icon: "fa-stethoscope", color: "#ef4444", courses: [
            { name: "MBBS", subjects: ["Anatomy", "Physiology", "Biochemistry", "Pathology", "Microbiology", "Pharmacology", "Medicine", "Surgery", "Pediatrics", "Gynecology", "Community Medicine"] },
            { name: "BDS", subjects: ["Dental Anatomy", "Oral Pathology", "Periodontics", "Orthodontics", "Prosthodontics", "Oral Surgery", "Endodontics"] },
            { name: "BPH", subjects: ["Public Health", "Epidemiology", "Health Policy", "Biostatistics", "Environmental Health", "Health Promotion", "Nutrition"] },
            { name: "BMLT", subjects: ["Clinical Biochemistry", "Hematology", "Microbiology", "Immunology", "Histopathology", "Serology", "Lab Management"] },
            { name: "BSc Physiotherapy", subjects: ["Anatomy", "Physiology", "Kinesiology", "Exercise Therapy", "Orthopedics", "Neuro-Rehab", "Cardio-Respiratory"] }
        ] },
        "law": { name: "Law", icon: "fa-scale-balanced", color: "#8b5cf6", courses: [
            { name: "LLB", subjects: ["Constitutional Law", "Criminal Law", "Civil Law", "Contract Law", "Corporate Law", "Labor Law", "Property Law", "Human Rights"] },
            { name: "BA LLB", subjects: ["Constitutional Law", "Criminal Law", "Torts", "Legal Writing", "Political Science", "Sociology", "Jurisprudence"] },
            { name: "BBL", subjects: ["Business Law", "Contract Law", "Company Law", "Tax Law", "Banking Law", "Economics", "Management"] },
            { name: "LLM", subjects: ["Advanced Constitutional Law", "International Law", "Human Rights", "Corporate Law", "Legal Research", "Thesis"] }
        ] },
        "education-teaching": { name: "Education & Teaching", icon: "fa-chalkboard-user", color: "#3b82f6", courses: [
            { name: "BEd", subjects: ["Educational Psychology", "Pedagogy", "Curriculum", "Teaching Methods", "Assessment", "Educational Technology", "Classroom Management"] },
            { name: "BA Education", subjects: ["Education Foundations", "Child Development", "Learning Theories", "Educational Research", "Counseling", "Curriculum"] },
            { name: "MEd", subjects: ["Educational Leadership", "Educational Policy", "Research Methods", "Curriculum Development", "Educational Psychology", "Thesis"] },
            { name: "ELT", subjects: ["Linguistics", "English Language Teaching", "Applied Linguistics", "Phonetics", "Language Testing", "Materials Development"] }
        ] },
        "humanities": { name: "Humanities & Social Sciences", icon: "fa-user-graduate", color: "#14b8a6", courses: [
            { name: "BA English", subjects: ["English Literature", "Critical Theory", "Creative Writing", "Linguistics", "Poetry", "Drama", "Academic Writing"] },
            { name: "BA Social Work", subjects: ["Social Work Methods", "Case Work", "Community Development", "Human Rights", "Counseling", "Social Policy", "Field Work"] },
            { name: "BA Sociology", subjects: ["Sociological Theory", "Social Research", "Urban Sociology", "Family", "Deviance", "Gender Studies", "Methods"] },
            { name: "BA Psychology", subjects: ["Cognitive Psychology", "Social Psychology", "Developmental Psychology", "Abnormal Psychology", "Statistics", "Research Methods"] }
        ] },
        "arts-design": { name: "Arts & Design", icon: "fa-palette", color: "#ec4899", courses: [
            { name: "BFA", subjects: ["Drawing", "Painting", "Sculpture", "Art History", "Color Theory", "Composition", "Printmaking"] },
            { name: "Graphic Design", subjects: ["Typography", "Branding", "Layout", "Photoshop", "Illustrator", "Logo Design", "Portfolio"] },
            { name: "Multimedia", subjects: ["Video Editing", "Animation", "Motion Graphics", "Web Design", "Sound Design", "3D Modeling", "After Effects"] },
            { name: "Fashion Design", subjects: ["Fashion Illustration", "Pattern Making", "Garment Construction", "Textile Science", "Fashion History", "Draping"] }
        ] },
        "journalism-media": { name: "Journalism & Media", icon: "fa-newspaper", color: "#f59e0b", courses: [
            { name: "BJMC", subjects: ["News Reporting", "Editing", "Mass Communication", "Media Ethics", "Public Relations", "Broadcasting", "Content Writing"] },
            { name: "BA Journalism", subjects: ["Journalism", "Feature Writing", "Investigative Reporting", "Media Law", "Photography", "Interviewing"] },
            { name: "Mass Communication", subjects: ["Communication Theory", "Advertising", "Public Relations", "Digital Media", "Film Studies", "Radio & TV"] }
        ] },
        "psychology": { name: "Psychology", icon: "fa-brain", color: "#8b5cf6", courses: [
            { name: "BSc Psychology", subjects: ["Cognitive Psychology", "Biological Psychology", "Developmental Psychology", "Social Psychology", "Statistics", "Psychometrics"] },
            { name: "BA Psychology", subjects: ["Social Psychology", "Personality", "Abnormal Psychology", "Counseling", "Research Methods", "Statistics"] },
            { name: "MA Clinical Psychology", subjects: ["Clinical Assessment", "Psychotherapy", "Psychopathology", "Behavior Therapy", "Ethics", "Practicum"] }
        ] }
    };

    // ============================================
    // RENDER FIELDS (name + icon only)
    // ============================================
    function afnFilterFields() {
        const q = (document.getElementById('afnSearchInput').value || '').toLowerCase();
        const fields = Object.values(afnFieldsData).filter(function (f) {
            return f.name.toLowerCase().includes(q);
        });

        const container = document.getElementById('afnFieldsContainer');
        if (fields.length === 0) {
            container.innerHTML = '<div class="afn-empty"><i class="fas fa-search"></i><p>No academic fields found for "' + q + '"</p></div>';
            return;
        }

        container.innerHTML = '<div class="afn-fields-grid">' + fields.map(function (field) {
            return '<div class="afn-field-card" onclick="afnSelectField(\'' + field.name.replace(/'/g, "\\'") + '\')">' +
                '<div class="afn-icon" style="color:' + field.color + '"><i class="fas ' + field.icon + '"></i></div>' +
                '<div class="afn-name">' + field.name + '</div>' +
                '<div class="afn-arrow"><i class="fas fa-chevron-right"></i></div></div>';
        }).join('') + '</div>';
    }

    function afnRenderFields() {
        const input = document.getElementById('afnSearchInput');
        if (input) input.value = '';
        const coursesContainer = document.getElementById('afnCoursesContainer');
        coursesContainer.style.display = 'none';
        coursesContainer.innerHTML = '';
        afnFilterFields();
    }

    // ============================================
    // SELECT FIELD -> open course section
    // ============================================
    function afnSelectField(fieldName) {
        const field = Object.values(afnFieldsData).find(function (f) { return f.name === fieldName; });
        if (!field) return;

        document.getElementById('afnFieldsContainer').style.display = 'none';
        const coursesContainer = document.getElementById('afnCoursesContainer');
        coursesContainer.style.display = 'block';

        coursesContainer.innerHTML = '<button class="afn-back-btn" onclick="afnShowFields()"><i class="fas fa-arrow-left"></i> Back to Academic Fields</button>' +
            '<div class="afn-section-title"><i class="fas ' + field.icon + '" style="color:' + field.color + '"></i> ' + field.name + ' <span class="count">(' + field.courses.length + ' courses)</span></div>' +
            '<div class="afn-courses-grid">' + field.courses.map(function (course) {
                return '<div class="afn-course-card" onclick="afnSelectCourse(\'' + field.name.replace(/'/g, "\\'") + '\', \'' + course.name.replace(/'/g, "\\'") + '\')">' +
                    '<div class="afn-c-icon" style="color:' + field.color + '"><i class="fas fa-graduation-cap"></i></div>' +
                    '<div class="afn-c-name">' + course.name + '</div>' +
                    '<div class="afn-c-meta"><span><i class="fas fa-book-open"></i> ' + course.subjects.length + ' Subjects</span></div></div>';
            }).join('') + '</div>';

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function afnSelectCourse(fieldName, courseName) {
        // Redirect to courses.php where the user can select a subject, then a mentor.
        window.location.href = 'courses.php?afield=' + encodeURIComponent(fieldName) + '&acourse=' + encodeURIComponent(courseName);
    }

    function afnShowFields() {
        document.getElementById('afnFieldsContainer').style.display = 'block';
        document.getElementById('afnCoursesContainer').style.display = 'none';
        document.getElementById('afnCoursesContainer').innerHTML = '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ============================================
    // INIT
    // ============================================
    document.addEventListener('DOMContentLoaded', function () {
        afnRenderFields();
    });

    console.log('🏛️ ShareSkill Hub - Academic Fields (names only) (' + Object.keys(afnFieldsData).length + ' fields)');
</script>

