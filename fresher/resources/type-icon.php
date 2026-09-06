<?php
// Helper file for resource type icons
function getResourceIcon($file_type) {
    $icons = [
        'pdf' => 'file-pdf',
        'doc' => 'file-word',
        'docx' => 'file-word',
        'ppt' => 'file-powerpoint',
        'pptx' => 'file-powerpoint',
        'xls' => 'file-excel',
        'xlsx' => 'file-excel',
        'jpg' => 'file-image',
        'jpeg' => 'file-image',
        'png' => 'file-image',
        'gif' => 'file-image',
        'webp' => 'file-image',
        'mp4' => 'file-video',
        'webm' => 'file-video',
        'ogg' => 'file-video',
        'mp3' => 'file-audio',
        'wav' => 'file-audio',
        'zip' => 'file-archive',
        'rar' => 'file-archive',
        '7z' => 'file-archive',
        'txt' => 'file-alt',
        'md' => 'file-alt',
        'json' => 'file-code',
        'xml' => 'file-code',
        'html' => 'file-code',
        'css' => 'file-code',
        'js' => 'file-code',
        'php' => 'file-code',
    ];
    
    return $icons[$file_type] ?? 'file';
}

function getFileCategory($file_type) {
    $categories = [
        'pdf' => 'Document',
        'doc' => 'Document',
        'docx' => 'Document',
        'ppt' => 'Presentation',
        'pptx' => 'Presentation',
        'jpg' => 'Image',
        'jpeg' => 'Image',
        'png' => 'Image',
        'gif' => 'Image',
        'webp' => 'Image',
        'mp4' => 'Video',
        'webm' => 'Video',
        'ogg' => 'Video',
        'mp3' => 'Audio',
        'wav' => 'Audio',
        'zip' => 'Archive',
        'rar' => 'Archive',
        '7z' => 'Archive',  
    ];
    
    return $categories[$file_type] ?? 'Other';
}
?>