<?php
$certificateRecipient = (string)($certificate['recipient_name'] ?? $certificate['student_name'] ?? getUserName());
$certificateCourse = (string)($certificate['course_title'] ?? '');
$certificateMentor = (string)($certificate['mentor_name'] ?? 'SkillShare Hub');
$certificateCode = (string)($certificate['certificate_code'] ?? '');
$certificateDate = formatDate($certificate['issued_at'] ?? null);
?>
<style>
    @page { size: landscape; margin: 14mm; }
    .certificate-document {
        width: 100%; min-height: 170mm; padding: 14mm; box-sizing: border-box;
        border: 5px solid #183b72; background: #fff; color: #17243b;
        font-family: DejaVu Sans, Arial, sans-serif; text-align: center; position: relative;
    }
    .certificate-inner { border: 2px solid #c8a951; min-height: 138mm; padding: 12mm 10mm; box-sizing: border-box; }
    .certificate-kicker { color: #b28a2e; letter-spacing: 4px; text-transform: uppercase; font-size: 11pt; font-weight: bold; }
    .certificate-heading { color: #183b72; font-size: 30pt; margin: 8mm 0 2mm; }
    .certificate-copy { color: #526078; font-size: 12pt; margin: 3mm 0; }
    .certificate-recipient { color: #183b72; font-family: DejaVu Serif, serif; font-size: 25pt; border-bottom: 1px solid #c8a951; display: inline-block; padding: 2mm 12mm; margin: 2mm 0; }
    .certificate-course { color: #17243b; font-size: 20pt; font-weight: bold; margin: 3mm 0; }
    .certificate-detail { color: #526078; font-size: 10pt; }
    .certificate-meta { width: 100%; margin-top: 12mm; border-collapse: collapse; }
    .certificate-meta td { width: 33.3%; vertical-align: bottom; padding: 2mm 4mm; font-size: 9pt; color: #526078; }
    .certificate-rule { border-top: 1px solid #8792a4; padding-top: 2mm; margin-top: 10mm; }
    .certificate-seal { display: inline-block; border: 3px double #c8a951; border-radius: 50%; width: 24mm; height: 24mm; line-height: 24mm; color: #183b72; font-size: 9pt; font-weight: bold; }
</style>
<article class="certificate-document">
    <div class="certificate-inner">
        <div class="certificate-kicker">SkillShare Hub</div>
        <h1 class="certificate-heading">Certificate of Completion</h1>
        <p class="certificate-copy">This certificate is proudly presented to</p>
        <div class="certificate-recipient"><?php echo htmlspecialchars($certificateRecipient, ENT_QUOTES, 'UTF-8'); ?></div>
        <p class="certificate-copy">for successfully completing the course</p>
        <div class="certificate-course"><?php echo htmlspecialchars($certificateCourse, ENT_QUOTES, 'UTF-8'); ?></div>
        <p class="certificate-detail">with instructor <?php echo htmlspecialchars($certificateMentor, ENT_QUOTES, 'UTF-8'); ?></p>
        <table class="certificate-meta" role="presentation">
            <tr>
                <td><div class="certificate-rule">Issued <?php echo htmlspecialchars($certificateDate, ENT_QUOTES, 'UTF-8'); ?></div></td>
                <td><span class="certificate-seal">ACHIEVED</span></td>
                <td><div class="certificate-rule">Certificate code<br><?php echo htmlspecialchars($certificateCode, ENT_QUOTES, 'UTF-8'); ?></div></td>
            </tr>
        </table>
    </div>
</article>
