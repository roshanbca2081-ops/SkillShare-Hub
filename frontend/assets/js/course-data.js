// ============================================
// ShareSkill Hub - Complete Course Data
// with semester-wise subjects for all fields
// ============================================
const courseData = {
    // ===== IT SECTOR COURSES =====
    "software-engineering": {
        id: "software-engineering",
        name: "Software Engineering",
        field: "Information Technology",
        fieldIcon: "fa-laptop-code",
        description: "Learn software development lifecycle, agile methodologies, and modern programming practices. Master the art of building scalable, maintainable, and high-quality software applications.",
        duration: "4 Years", level: "Bachelor", students: "1,200+",
        semesters: [
            { number: 1, subjects: [
                { name: "Programming Fundamentals", code: "CS101", credits: 3 },
                { name: "Mathematics I", code: "MATH101", credits: 3 },
                { name: "English Communication", code: "ENG101", credits: 2 },
                { name: "Digital Logic", code: "CS102", credits: 3 },
                { name: "Computer Fundamentals", code: "CS103", credits: 3 },
                { name: "Introduction to IT", code: "IT101", credits: 2 } ] },
            { number: 2, subjects: [
                { name: "Object Oriented Programming", code: "CS201", credits: 3 },
                { name: "Mathematics II", code: "MATH201", credits: 3 },
                { name: "Data Structures", code: "CS202", credits: 3 },
                { name: "Database Management", code: "CS203", credits: 3 },
                { name: "Web Technologies", code: "IT201", credits: 3 },
                { name: "Communication Skills", code: "ENG201", credits: 2 } ] },
            { number: 3, subjects: [
                { name: "Algorithms", code: "CS301", credits: 3 },
                { name: "Software Engineering", code: "CS302", credits: 3 },
                { name: "Operating Systems", code: "CS303", credits: 3 },
                { name: "Computer Networks", code: "CS304", credits: 3 },
                { name: "Database Design", code: "CS305", credits: 3 },
                { name: "Probability & Statistics", code: "MATH301", credits: 3 } ] },
            { number: 4, subjects: [
                { name: "Web Application Development", code: "CS401", credits: 3 },
                { name: "Software Testing", code: "CS402", credits: 3 },
                { name: "System Analysis", code: "CS403", credits: 3 },
                { name: "Agile Methodologies", code: "CS404", credits: 3 },
                { name: "Cloud Computing", code: "IT401", credits: 3 },
                { name: "Technical Writing", code: "ENG401", credits: 2 } ] },
            { number: 5, subjects: [
                { name: "Advanced Web Development", code: "CS501", credits: 3 },
                { name: "Mobile Application Development", code: "CS502", credits: 3 },
                { name: "Software Architecture", code: "CS503", credits: 3 },
                { name: "DevOps Principles", code: "CS504", credits: 3 },
                { name: "Machine Learning Basics", code: "CS505", credits: 3 },
                { name: "Professional Ethics", code: "IT501", credits: 2 } ] },
            { number: 6, subjects: [
                { name: "Full Stack Development", code: "CS601", credits: 3 },
                { name: "System Integration", code: "CS602", credits: 3 },
                { name: "Software Project Management", code: "CS603", credits: 3 },
                { name: "Cybersecurity", code: "CS604", credits: 3 },
                { name: "Data Analytics", code: "CS605", credits: 3 },
                { name: "Research Methodology", code: "IT601", credits: 2 } ] },
            { number: 7, subjects: [
                { name: "Enterprise Architecture", code: "CS701", credits: 3 },
                { name: "Microservices", code: "CS702", credits: 3 },
                { name: "AI Applications", code: "CS703", credits: 3 },
                { name: "Blockchain Technology", code: "CS704", credits: 3 },
                { name: "Quality Assurance", code: "CS705", credits: 3 },
                { name: "Business Communication", code: "ENG701", credits: 2 } ] },
            { number: 8, subjects: [
                { name: "Final Year Project", code: "CS801", credits: 6 },
                { name: "IT Entrepreneurship", code: "CS802", credits: 3 },
                { name: "Industry Internship", code: "CS803", credits: 3 },
                { name: "Professional Development", code: "CS804", credits: 3 },
                { name: "Capstone Project", code: "CS805", credits: 3 },
                { name: "Career Planning", code: "IT801", credits: 2 } ] }
        ]
    },
    "web-development": {
        id: "web-development",
        name: "Web Development",
        field: "Information Technology",
        fieldIcon: "fa-laptop-code",
        description: "Master front-end and back-end web development. Learn HTML, CSS, JavaScript, React, Node.js, and modern web frameworks.",
        duration: "4 Years", level: "Bachelor", students: "2,500+",
        semesters: [
            { number: 1, subjects: [
                { name: "HTML & CSS", code: "WD101", credits: 3 },
                { name: "JavaScript Basics", code: "WD102", credits: 3 },
                { name: "Web Design Principles", code: "WD103", credits: 3 },
                { name: "Computer Science Fundamentals", code: "CS101", credits: 3 },
                { name: "Mathematics I", code: "MATH101", credits: 3 },
                { name: "English Communication", code: "ENG101", credits: 2 } ] },
            { number: 2, subjects: [
                { name: "Advanced JavaScript", code: "WD201", credits: 3 },
                { name: "React.js", code: "WD202", credits: 3 },
                { name: "UI/UX Design", code: "WD203", credits: 3 },
                { name: "Responsive Design", code: "WD204", credits: 3 },
                { name: "Database Systems", code: "CS203", credits: 3 },
                { name: "Mathematics II", code: "MATH201", credits: 3 } ] },
            { number: 3, subjects: [
                { name: "Node.js", code: "WD301", credits: 3 },
                { name: "Express.js", code: "WD302", credits: 3 },
                { name: "MongoDB", code: "WD303", credits: 3 },
                { name: "REST APIs", code: "WD304", credits: 3 },
                { name: "Authentication & Security", code: "WD305", credits: 3 },
                { name: "Data Structures", code: "CS202", credits: 3 } ] },
            { number: 4, subjects: [
                { name: "Angular", code: "WD401", credits: 3 },
                { name: "Vue.js", code: "WD402", credits: 3 },
                { name: "GraphQL", code: "WD403", credits: 3 },
                { name: "Web Performance", code: "WD404", credits: 3 },
                { name: "Testing & Debugging", code: "WD405", credits: 3 },
                { name: "Algorithms", code: "CS301", credits: 3 } ] },
            { number: 5, subjects: [
                { name: "Django Framework", code: "WD501", credits: 3 },
                { name: "Laravel", code: "WD502", credits: 3 },
                { name: "Microservices", code: "WD503", credits: 3 },
                { name: "DevOps for Web", code: "WD504", credits: 3 },
                { name: "Web Security", code: "WD505", credits: 3 },
                { name: "Agile Development", code: "WD506", credits: 3 } ] },
            { number: 6, subjects: [
                { name: "Full Stack Project", code: "WD601", credits: 3 },
                { name: "Progressive Web Apps", code: "WD602", credits: 3 },
                { name: "Serverless Architecture", code: "WD603", credits: 3 },
                { name: "Web Analytics", code: "WD604", credits: 3 },
                { name: "Technical Writing", code: "ENG401", credits: 2 },
                { name: "Professional Ethics", code: "IT501", credits: 2 } ] },
            { number: 7, subjects: [
                { name: "Advanced React", code: "WD701", credits: 3 },
                { name: "Next.js", code: "WD702", credits: 3 },
                { name: "Web3 Development", code: "WD703", credits: 3 },
                { name: "Cloud Deployment", code: "WD704", credits: 3 },
                { name: "Business Strategy", code: "WD705", credits: 3 },
                { name: "Research Methodology", code: "IT601", credits: 2 } ] },
            { number: 8, subjects: [
                { name: "Capstone Project", code: "WD801", credits: 6 },
                { name: "Industry Internship", code: "WD802", credits: 3 },
                { name: "Digital Marketing", code: "WD803", credits: 3 },
                { name: "Product Management", code: "WD804", credits: 3 },
                { name: "Career Development", code: "WD805", credits: 2 } ] }
        ]
    },
    "cyber-security": {
        id: "cyber-security",
        name: "Cyber Security",
        field: "Information Technology",
        fieldIcon: "fa-shield-alt",
        description: "Master network security, ethical hacking, digital forensics, and cybersecurity risk management.",
        duration: "4 Years", level: "Bachelor", students: "850+",
        semesters: [
            { number: 1, subjects: [
                { name: "Introduction to Cybersecurity", code: "CS101", credits: 3 },
                { name: "Computer Networks", code: "CS102", credits: 3 },
                { name: "Operating Systems", code: "CS103", credits: 3 },
                { name: "Mathematics I", code: "MATH101", credits: 3 },
                { name: "Programming Fundamentals", code: "CS104", credits: 3 },
                { name: "English Communication", code: "ENG101", credits: 2 } ] },
            { number: 2, subjects: [
                { name: "Network Security", code: "CS201", credits: 3 },
                { name: "Cryptography", code: "CS202", credits: 3 },
                { name: "Ethical Hacking", code: "CS203", credits: 3 },
                { name: "Information Security", code: "CS204", credits: 3 },
                { name: "Data Structures", code: "CS205", credits: 3 },
                { name: "Mathematics II", code: "MATH201", credits: 3 } ] },
            { number: 3, subjects: [
                { name: "Digital Forensics", code: "CS301", credits: 3 },
                { name: "Malware Analysis", code: "CS302", credits: 3 },
                { name: "Penetration Testing", code: "CS303", credits: 3 },
                { name: "Security Architecture", code: "CS304", credits: 3 },
                { name: "Database Security", code: "CS305", credits: 3 },
                { name: "Algorithms", code: "CS306", credits: 3 } ] },
            { number: 4, subjects: [
                { name: "Cloud Security", code: "CS401", credits: 3 },
                { name: "Application Security", code: "CS402", credits: 3 },
                { name: "Security Operations", code: "CS403", credits: 3 },
                { name: "Risk Management", code: "CS404", credits: 3 },
                { name: "Cyber Law", code: "CS405", credits: 3 },
                { name: "Technical Writing", code: "ENG401", credits: 2 } ] },
            { number: 5, subjects: [
                { name: "Advanced Cryptography", code: "CS501", credits: 3 },
                { name: "IoT Security", code: "CS502", credits: 3 },
                { name: "SOC Analysis", code: "CS503", credits: 3 },
                { name: "Security Automation", code: "CS504", credits: 3 },
                { name: "Blockchain Security", code: "CS505", credits: 3 },
                { name: "Research Methodology", code: "CS506", credits: 2 } ] },
            { number: 6, subjects: [
                { name: "Incident Response", code: "CS601", credits: 3 },
                { name: "Threat Intelligence", code: "CS602", credits: 3 },
                { name: "Secure Coding", code: "CS603", credits: 3 },
                { name: "Security Auditing", code: "CS604", credits: 3 },
                { name: "Professional Ethics", code: "IT501", credits: 2 },
                { name: "Business Communication", code: "ENG601", credits: 2 } ] },
            { number: 7, subjects: [
                { name: "Advanced Forensics", code: "CS701", credits: 3 },
                { name: "Zero Trust Architecture", code: "CS702", credits: 3 },
                { name: "Red Team Operations", code: "CS703", credits: 3 },
                { name: "Security Leadership", code: "CS704", credits: 3 },
                { name: "Compliance & Governance", code: "CS705", credits: 3 },
                { name: "Career Planning", code: "CS706", credits: 2 } ] },
            { number: 8, subjects: [
                { name: "Capstone Project", code: "CS801", credits: 6 },
                { name: "Industry Internship", code: "CS802", credits: 3 },
                { name: "CISSP Preparation", code: "CS803", credits: 3 },
                { name: "Security Strategy", code: "CS804", credits: 3 },
                { name: "Professional Development", code: "CS805", credits: 2 } ] }
        ]
    },
    "artificial-intelligence": {
        id: "artificial-intelligence",
        name: "Artificial Intelligence",
        field: "Information Technology",
        fieldIcon: "fa-brain",
        description: "Study machine learning, neural networks, deep learning, natural language processing, and AI applications.",
        duration: "4 Years", level: "Bachelor", students: "950+",
        semesters: [
            { number: 1, subjects: [
                { name: "Introduction to AI", code: "AI101", credits: 3 },
                { name: "Programming Python", code: "AI102", credits: 3 },
                { name: "Mathematics I", code: "MATH101", credits: 3 },
                { name: "Statistics Basics", code: "AI103", credits: 3 },
                { name: "Data Structures", code: "CS202", credits: 3 },
                { name: "English Communication", code: "ENG101", credits: 2 } ] },
            { number: 2, subjects: [
                { name: "Machine Learning", code: "AI201", credits: 3 },
                { name: "Linear Algebra", code: "MATH201", credits: 3 },
                { name: "Probability", code: "AI202", credits: 3 },
                { name: "Database Systems", code: "CS203", credits: 3 },
                { name: "Data Visualization", code: "AI203", credits: 3 },
                { name: "Technical Writing", code: "ENG201", credits: 2 } ] },
            { number: 3, subjects: [
                { name: "Neural Networks", code: "AI301", credits: 3 },
                { name: "Deep Learning", code: "AI302", credits: 3 },
                { name: "Natural Language Processing", code: "AI303", credits: 3 },
                { name: "Computer Vision", code: "AI304", credits: 3 },
                { name: "Big Data Analytics", code: "AI305", credits: 3 },
                { name: "Calculus", code: "MATH301", credits: 3 } ] },
            { number: 4, subjects: [
                { name: "Reinforcement Learning", code: "AI401", credits: 3 },
                { name: "AI Ethics", code: "AI402", credits: 3 },
                { name: "Speech Recognition", code: "AI403", credits: 3 },
                { name: "Generative AI", code: "AI404", credits: 3 },
                { name: "Robotics", code: "AI405", credits: 3 },
                { name: "Research Methodology", code: "AI406", credits: 2 } ] },
            { number: 5, subjects: [
                { name: "Advanced Machine Learning", code: "AI501", credits: 3 },
                { name: "AI for Healthcare", code: "AI502", credits: 3 },
                { name: "AI in Finance", code: "AI503", credits: 3 },
                { name: "Recommender Systems", code: "AI504", credits: 3 },
                { name: "Cloud AI Services", code: "AI505", credits: 3 },
                { name: "Professional Ethics", code: "AI506", credits: 2 } ] },
            { number: 6, subjects: [
                { name: "AI Research Project", code: "AI601", credits: 3 },
                { name: "Industry Applications", code: "AI602", credits: 3 },
                { name: "AI Deployment", code: "AI603", credits: 3 },
                { name: "Data Engineering", code: "AI604", credits: 3 },
                { name: "Business Communication", code: "ENG601", credits: 2 },
                { name: "Entrepreneurship", code: "AI605", credits: 2 } ] },
            { number: 7, subjects: [
                { name: "Advanced Deep Learning", code: "AI701", credits: 3 },
                { name: "Edge AI", code: "AI702", credits: 3 },
                { name: "AI Security", code: "AI703", credits: 3 },
                { name: "Quantum AI", code: "AI704", credits: 3 },
                { name: "AI Strategy", code: "AI705", credits: 3 },
                { name: "Leadership Skills", code: "AI706", credits: 2 } ] },
            { number: 8, subjects: [
                { name: "Capstone Project", code: "AI801", credits: 6 },
                { name: "Industry Internship", code: "AI802", credits: 3 },
                { name: "AI Product Management", code: "AI803", credits: 3 },
                { name: "Future Trends", code: "AI804", credits: 3 },
                { name: "Career Development", code: "AI805", credits: 2 } ] }
        ]
    },
    "data-science": {
        id: "data-science",
        name: "Data Science",
        field: "Information Technology",
        fieldIcon: "fa-chart-bar",
        description: "Learn big data, statistical analysis, data visualization, and machine learning techniques.",
        duration: "4 Years", level: "Bachelor", students: "1,100+",
        semesters: [
            { number: 1, subjects: [
                { name: "Introduction to Data Science", code: "DS101", credits: 3 },
                { name: "Python Programming", code: "DS102", credits: 3 },
                { name: "Mathematics I", code: "MATH101", credits: 3 },
                { name: "Statistics I", code: "DS103", credits: 3 },
                { name: "Data Structures", code: "CS202", credits: 3 },
                { name: "English Communication", code: "ENG101", credits: 2 } ] },
            { number: 2, subjects: [
                { name: "Data Wrangling", code: "DS201", credits: 3 },
                { name: "SQL for Data", code: "DS202", credits: 3 },
                { name: "Linear Algebra", code: "MATH201", credits: 3 },
                { name: "Probability", code: "DS203", credits: 3 },
                { name: "Data Visualization", code: "DS204", credits: 3 },
                { name: "Technical Writing", code: "ENG201", credits: 2 } ] },
            { number: 3, subjects: [
                { name: "Machine Learning I", code: "DS301", credits: 3 },
                { name: "Big Data Technologies", code: "DS302", credits: 3 },
                { name: "Statistical Methods", code: "DS303", credits: 3 },
                { name: "Data Mining", code: "DS304", credits: 3 },
                { name: "Cloud Data Platforms", code: "DS305", credits: 3 },
                { name: "Research Methodology", code: "DS306", credits: 2 } ] },
            { number: 4, subjects: [
                { name: "Machine Learning II", code: "DS401", credits: 3 },
                { name: "Deep Learning", code: "DS402", credits: 3 },
                { name: "Business Intelligence", code: "DS403", credits: 3 },
                { name: "Time Series Analysis", code: "DS404", credits: 3 },
                { name: "Data Ethics", code: "DS405", credits: 3 },
                { name: "Professional Communication", code: "ENG401", credits: 2 } ] },
            { number: 5, subjects: [
                { name: "Natural Language Processing", code: "DS501", credits: 3 },
                { name: "Computer Vision", code: "DS502", credits: 3 },
                { name: "Advanced Statistics", code: "DS503", credits: 3 },
                { name: "Data Engineering", code: "DS504", credits: 3 },
                { name: "Data Product Management", code: "DS505", credits: 3 },
                { name: "Professional Ethics", code: "DS506", credits: 2 } ] },
            { number: 6, subjects: [
                { name: "AI in Data Science", code: "DS601", credits: 3 },
                { name: "Data Governance", code: "DS602", credits: 3 },
                { name: "Healthcare Analytics", code: "DS603", credits: 3 },
                { name: "Financial Analytics", code: "DS604", credits: 3 },
                { name: "Business Strategy", code: "DS605", credits: 3 },
                { name: "Entrepreneurship", code: "DS606", credits: 2 } ] },
            { number: 7, subjects: [
                { name: "AI Research Project", code: "DS701", credits: 3 },
                { name: "Data Science Leadership", code: "DS702", credits: 3 },
                { name: "Data Security", code: "DS703", credits: 3 },
                { name: "Data Science in Practice", code: "DS704", credits: 3 },
                { name: "Advanced Analytics", code: "DS705", credits: 3 },
                { name: "Career Planning", code: "DS706", credits: 2 } ] },
            { number: 8, subjects: [
                { name: "Capstone Project", code: "DS801", credits: 6 },
                { name: "Industry Internship", code: "DS802", credits: 3 },
                { name: "Data Science Consulting", code: "DS803", credits: 3 },
                { name: "Future Technologies", code: "DS804", credits: 3 },
                { name: "Professional Development", code: "DS805", credits: 2 } ] }
        ]
    },
    "cloud-computing": {
        id: "cloud-computing",
        name: "Cloud Computing",
        field: "Information Technology",
        fieldIcon: "fa-cloud",
        description: "Master cloud architecture, AWS, Azure, GCP, containerization, and cloud-native development.",
        duration: "4 Years", level: "Bachelor", students: "680+",
        semesters: [
            { number: 1, subjects: [
                { name: "Cloud Computing Fundamentals", code: "CC101", credits: 3 },
                { name: "Linux Administration", code: "CC102", credits: 3 },
                { name: "Networking Basics", code: "CC103", credits: 3 },
                { name: "Programming Fundamentals", code: "CS101", credits: 3 },
                { name: "Mathematics I", code: "MATH101", credits: 3 },
                { name: "English Communication", code: "ENG101", credits: 2 } ] },
            { number: 2, subjects: [
                { name: "AWS Fundamentals", code: "CC201", credits: 3 },
                { name: "Virtualization", code: "CC202", credits: 3 },
                { name: "Storage Technologies", code: "CC203", credits: 3 },
                { name: "Database Systems", code: "CS203", credits: 3 },
                { name: "Scripting Languages", code: "CC204", credits: 3 },
                { name: "Technical Writing", code: "ENG201", credits: 2 } ] },
            { number: 3, subjects: [
                { name: "Azure Cloud", code: "CC301", credits: 3 },
                { name: "GCP Platform", code: "CC302", credits: 3 },
                { name: "Containerization (Docker)", code: "CC303", credits: 3 },
                { name: "Orchestration (Kubernetes)", code: "CC304", credits: 3 },
                { name: "CI/CD Pipelines", code: "CC305", credits: 3 },
                { name: "Data Structures", code: "CS202", credits: 3 } ] },
            { number: 4, subjects: [
                { name: "Cloud Security", code: "CC401", credits: 3 },
                { name: "Infrastructure as Code", code: "CC402", credits: 3 },
                { name: "Serverless Architecture", code: "CC403", credits: 3 },
                { name: "Cloud Networking", code: "CC404", credits: 3 },
                { name: "Disaster Recovery", code: "CC405", credits: 3 },
                { name: "Research Methodology", code: "CC406", credits: 2 } ] },
            { number: 5, subjects: [
                { name: "Advanced AWS", code: "CC501", credits: 3 },
                { name: "Multi-Cloud Strategy", code: "CC502", credits: 3 },
                { name: "Cloud Native Development", code: "CC503", credits: 3 },
                { name: "Data Analytics in Cloud", code: "CC504", credits: 3 },
                { name: "DevOps Culture", code: "CC505", credits: 3 },
                { name: "Professional Ethics", code: "CC506", credits: 2 } ] },
            { number: 6, subjects: [
                { name: "Cloud Project Management", code: "CC601", credits: 3 },
                { name: "AI in Cloud", code: "CC602", credits: 3 },
                { name: "Cloud Cost Management", code: "CC603", credits: 3 },
                { name: "Performance Optimization", code: "CC604", credits: 3 },
                { name: "Business Communication", code: "ENG601", credits: 2 },
                { name: "Entrepreneurship", code: "CC605", credits: 2 } ] },
            { number: 7, subjects: [
                { name: "Cloud Architecture Design", code: "CC701", credits: 3 },
                { name: "Edge Computing", code: "CC702", credits: 3 },
                { name: "Cloud Governance", code: "CC703", credits: 3 },
                { name: "Security Compliance", code: "CC704", credits: 3 },
                { name: "Cloud Strategy", code: "CC705", credits: 3 },
                { name: "Leadership Skills", code: "CC706", credits: 2 } ] },
            { number: 8, subjects: [
                { name: "Capstone Project", code: "CC801", credits: 6 },
                { name: "Industry Internship", code: "CC802", credits: 3 },
                { name: "Cloud Consulting", code: "CC803", credits: 3 },
                { name: "Professional Development", code: "CC804", credits: 3 },
                { name: "Career Planning", code: "CC805", credits: 2 } ] }
        ]
    },

    // ===== ENGINEERING COURSES =====
    "software-engineering-eng": {
        id: "software-engineering-eng",
        name: "Software Engineering (Engineering)",
        field: "Engineering",
        fieldIcon: "fa-robot",
        description: "Comprehensive software engineering with focus on engineering principles, algorithms, and system design.",
        duration: "4 Years", level: "Bachelor", students: "1,500+",
        semesters: [
            { number: 1, subjects: [
                { name: "Engineering Mathematics I", code: "EM101", credits: 3 },
                { name: "Programming for Engineers", code: "SE101", credits: 3 },
                { name: "Engineering Physics", code: "EP101", credits: 3 },
                { name: "Digital Logic Design", code: "SE102", credits: 3 },
                { name: "Computer Organization", code: "SE103", credits: 3 },
                { name: "English for Engineers", code: "ENG101", credits: 2 } ] },
            { number: 2, subjects: [
                { name: "Engineering Mathematics II", code: "EM201", credits: 3 },
                { name: "Data Structures & Algorithms", code: "SE201", credits: 3 },
                { name: "Object Oriented Programming", code: "SE202", credits: 3 },
                { name: "Software Engineering I", code: "SE203", credits: 3 },
                { name: "Database Management", code: "SE204", credits: 3 },
                { name: "Technical Writing", code: "ENG201", credits: 2 } ] },
            { number: 3, subjects: [
                { name: "Engineering Mathematics III", code: "EM301", credits: 3 },
                { name: "Algorithms Analysis", code: "SE301", credits: 3 },
                { name: "Software Engineering II", code: "SE302", credits: 3 },
                { name: "Operating Systems", code: "SE303", credits: 3 },
                { name: "Computer Networks", code: "SE304", credits: 3 },
                { name: "Probability & Statistics", code: "SE305", credits: 3 } ] },
            { number: 4, subjects: [
                { name: "Discrete Mathematics", code: "SE401", credits: 3 },
                { name: "Software Testing & QA", code: "SE402", credits: 3 },
                { name: "System Analysis & Design", code: "SE403", credits: 3 },
                { name: "Agile Methodologies", code: "SE404", credits: 3 },
                { name: "Web Engineering", code: "SE405", credits: 3 },
                { name: "Research Methodology", code: "SE406", credits: 2 } ] },
            { number: 5, subjects: [
                { name: "Advanced Algorithms", code: "SE501", credits: 3 },
                { name: "Software Architecture", code: "SE502", credits: 3 },
                { name: "Distributed Systems", code: "SE503", credits: 3 },
                { name: "Embedded Systems", code: "SE504", credits: 3 },
                { name: "Mobile Application Development", code: "SE505", credits: 3 },
                { name: "Professional Ethics", code: "SE506", credits: 2 } ] },
            { number: 6, subjects: [
                { name: "Software Project Management", code: "SE601", credits: 3 },
                { name: "Cloud Computing", code: "SE602", credits: 3 },
                { name: "Cybersecurity", code: "SE603", credits: 3 },
                { name: "Data Analytics", code: "SE604", credits: 3 },
                { name: "Business Communication", code: "ENG601", credits: 2 },
                { name: "Entrepreneurship", code: "SE605", credits: 2 } ] },
            { number: 7, subjects: [
                { name: "Advanced Software Engineering", code: "SE701", credits: 3 },
                { name: "AI in Engineering", code: "SE702", credits: 3 },
                { name: "Blockchain Technology", code: "SE703", credits: 3 },
                { name: "Internet of Things", code: "SE704", credits: 3 },
                { name: "Quality Assurance", code: "SE705", credits: 3 },
                { name: "Leadership Skills", code: "SE706", credits: 2 } ] },
            { number: 8, subjects: [
                { name: "Final Year Project", code: "SE801", credits: 6 },
                { name: "Industry Internship", code: "SE802", credits: 3 },
                { name: "Professional Development", code: "SE803", credits: 3 },
                { name: "Engineering Management", code: "SE804", credits: 3 },
                { name: "Career Planning", code: "SE805", credits: 2 } ] }
        ]
    },
    "computer-engineering": {
        id: "computer-engineering",
        name: "Computer Engineering",
        field: "Engineering",
        fieldIcon: "fa-microchip",
        description: "Study computer architecture, embedded systems, hardware-software integration, and digital systems design.",
        duration: "4 Years", level: "Bachelor", students: "1,300+",
        semesters: [
            { number: 1, subjects: [
                { name: "Engineering Mathematics I", code: "EM101", credits: 3 },
                { name: "Digital Logic", code: "CE101", credits: 3 },
                { name: "Engineering Physics", code: "EP101", credits: 3 },
                { name: "Programming Fundamentals", code: "CE102", credits: 3 },
                { name: "Computer Fundamentals", code: "CE103", credits: 3 },
                { name: "English for Engineers", code: "ENG101", credits: 2 } ] },
            { number: 2, subjects: [
                { name: "Engineering Mathematics II", code: "EM201", credits: 3 },
                { name: "Computer Architecture", code: "CE201", credits: 3 },
                { name: "Data Structures", code: "CE202", credits: 3 },
                { name: "Object Oriented Programming", code: "CE203", credits: 3 },
                { name: "Digital Electronics", code: "CE204", credits: 3 },
                { name: "Technical Writing", code: "ENG201", credits: 2 } ] },
            { number: 3, subjects: [
                { name: "Engineering Mathematics III", code: "EM301", credits: 3 },
                { name: "Computer Networks", code: "CE301", credits: 3 },
                { name: "Operating Systems", code: "CE302", credits: 3 },
                { name: "Microprocessors", code: "CE303", credits: 3 },
                { name: "Database Systems", code: "CE304", credits: 3 },
                { name: "Probability & Statistics", code: "CE305", credits: 3 } ] },
            { number: 4, subjects: [
                { name: "Discrete Mathematics", code: "CE401", credits: 3 },
                { name: "Algorithms", code: "CE402", credits: 3 },
                { name: "Embedded Systems", code: "CE403", credits: 3 },
                { name: "VLSI Design", code: "CE404", credits: 3 },
                { name: "Compiler Design", code: "CE405", credits: 3 },
                { name: "Research Methodology", code: "CE406", credits: 2 } ] },
            { number: 5, subjects: [
                { name: "Advanced Computer Architecture", code: "CE501", credits: 3 },
                { name: "Distributed Systems", code: "CE502", credits: 3 },
                { name: "Computer Graphics", code: "CE503", credits: 3 },
                { name: "Image Processing", code: "CE504", credits: 3 },
                { name: "Wireless Networks", code: "CE505", credits: 3 },
                { name: "Professional Ethics", code: "CE506", credits: 2 } ] },
            { number: 6, subjects: [
                { name: "Cloud Computing", code: "CE601", credits: 3 },
                { name: "Cyber Security", code: "CE602", credits: 3 },
                { name: "Machine Learning", code: "CE603", credits: 3 },
                { name: "Internet of Things", code: "CE604", credits: 3 },
                { name: "Business Communication", code: "ENG601", credits: 2 },
                { name: "Entrepreneurship", code: "CE605", credits: 2 } ] },
            { number: 7, subjects: [
                { name: "Advanced Networking", code: "CE701", credits: 3 },
                { name: "AI in Engineering", code: "CE702", credits: 3 },
                { name: "Blockchain Technology", code: "CE703", credits: 3 },
                { name: "Quantum Computing", code: "CE704", credits: 3 },
                { name: "Computer Vision", code: "CE705", credits: 3 },
                { name: "Leadership Skills", code: "CE706", credits: 2 } ] },
            { number: 8, subjects: [
                { name: "Final Year Project", code: "CE801", credits: 6 },
                { name: "Industry Internship", code: "CE802", credits: 3 },
                { name: "Professional Development", code: "CE803", credits: 3 },
                { name: "Engineering Management", code: "CE804", credits: 3 },
                { name: "Career Planning", code: "CE805", credits: 2 } ] }
        ]
    }
};

// ============================================
// COURSE LIST FOR NAVIGATION
// ============================================
const courseList = [
    { id: "software-engineering", name: "Software Engineering", field: "Information Technology" },
    { id: "web-development", name: "Web Development", field: "Information Technology" },
    { id: "cyber-security", name: "Cyber Security", field: "Information Technology" },
    { id: "artificial-intelligence", name: "Artificial Intelligence", field: "Information Technology" },
    { id: "data-science", name: "Data Science", field: "Information Technology" },
    { id: "cloud-computing", name: "Cloud Computing", field: "Information Technology" },
    { id: "software-engineering-eng", name: "Software Engineering (Engineering)", field: "Engineering" },
    { id: "computer-engineering", name: "Computer Engineering", field: "Engineering" }
];
