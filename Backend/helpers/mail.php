<?php

/**
 * Mail Helper Function
 */

if (!function_exists('sendMail')) {
    function sendMail($to, $subject, $body, $altBody = '')
    {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: SkillShare Hub <noreply@skillsharehub.com>' . "\r\n";

        return @mail($to, $subject, $body, $headers);
    }
}
