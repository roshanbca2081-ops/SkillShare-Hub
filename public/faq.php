<?php
$page_title = 'FAQ';
require_once '../config/session.php';

$faqs = [
    [
        'question' => 'What is SkillShare Hub?',
        'answer' => 'SkillShare Hub is an online learning platform that connects freshers with experienced mentors across various academic and professional fields.'
    ],
    [
        'question' => 'How do I enroll in a course?',
        'answer' => 'Simply create an account, browse our course catalog, and click "Enroll Now" on any course you\'re interested in.'
    ],
    [
        'question' => 'Are the courses free?',
        'answer' => 'We offer both free and premium courses. Free courses are clearly marked, and premium courses provide in-depth content and certification.'
    ],
    [
        'question' => 'Do I get a certificate?',
        'answer' => 'Yes! Upon successful completion of a course, you\'ll receive a certificate that you can share with employers.'
    ],
    [
        'question' => 'How can I become a mentor?',
        'answer' => 'If you\'re an industry professional with expertise to share, you can apply to become a mentor through our mentor registration process.'
    ],
    [
        'question' => 'What are live sessions?',
        'answer' => 'Live sessions are real-time interactive classes conducted by mentors. You can ask questions and get immediate feedback.'
    ],
    [
        'question' => 'How do I contact a mentor?',
        'answer' => 'You can send direct messages to mentors through our messaging system. Mentors are usually responsive within 24 hours.'
    ],
    [
        'question' => 'Can I get a refund?',
        'answer' => 'Yes, we offer a 30-day money-back guarantee for premium courses if you\'re not satisfied with the content.'
    ]
];
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <h1 class="display-4 fw-bold text-center mb-3">Frequently Asked Questions</h1>
    <p class="text-center text-muted mb-5">Find answers to common questions about SkillShare Hub</p>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="accordion" id="faqAccordion">
                <?php foreach ($faqs as $index => $faq): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?php echo $index; ?>">
                            <?php echo htmlspecialchars($faq['question']); ?>
                        </button>
                    </h2>
                    <div id="faq<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index == 0 ? 'show' : ''; ?>" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <?php echo htmlspecialchars($faq['answer']); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>