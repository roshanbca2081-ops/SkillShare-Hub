<?php
/**
 * Validate, store, and save a user's profile image.
 * Returns its project-relative public path after the database update succeeds.
 */
function saveUserProfileImage(PDO $pdo, $userId, array $upload)
{
    if (!isset($upload['error']) || (int)$upload['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'The image exceeds the server upload limit.',
            UPLOAD_ERR_FORM_SIZE => 'The image exceeds the upload limit.',
            UPLOAD_ERR_PARTIAL => 'The image upload was incomplete. Please try again.',
            UPLOAD_ERR_NO_FILE => 'Choose an image to upload.',
            UPLOAD_ERR_NO_TMP_DIR => 'The server upload directory is missing.',
            UPLOAD_ERR_CANT_WRITE => 'The server could not save the image.',
            UPLOAD_ERR_EXTENSION => 'The server rejected this image upload.',
        ];
        $errorCode = (int)($upload['error'] ?? UPLOAD_ERR_NO_FILE);
        throw new RuntimeException($uploadErrors[$errorCode] ?? 'The image could not be uploaded.');
    }

    $temporaryPath = (string)($upload['tmp_name'] ?? '');
    if ($temporaryPath === '' || !is_uploaded_file($temporaryPath)) {
        throw new RuntimeException('The uploaded image could not be verified. Please select it again.');
    }

    $maxBytes = 2 * 1024 * 1024;
    if ((int)($upload['size'] ?? 0) < 1 || (int)$upload['size'] > $maxBytes) {
        throw new RuntimeException('Profile images must be smaller than 2 MB.');
    }

    $imageInfo = @getimagesize($temporaryPath);
    if ($imageInfo === false || empty($imageInfo['mime'])) {
        throw new RuntimeException('The selected file is not a valid image.');
    }

    $extensionByMime = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    $mime = strtolower((string)$imageInfo['mime']);
    if (!isset($extensionByMime[$mime])) {
        throw new RuntimeException('Use a JPEG, PNG, GIF, or WEBP image.');
    }

    $projectRoot = dirname(__DIR__);
    $uploadDirectory = $projectRoot . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profiles';
    if (!is_dir($uploadDirectory) && !@mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
        throw new RuntimeException('The profile image folder could not be created. Check folder permissions.');
    }
    if (!is_writable($uploadDirectory)) {
        throw new RuntimeException('The profile image folder is not writable. Check folder permissions.');
    }

    $fileName = 'user_' . (int)$userId . '_' . bin2hex(random_bytes(12)) . '.' . $extensionByMime[$mime];
    $absolutePath = $uploadDirectory . DIRECTORY_SEPARATOR . $fileName;
    $relativePath = 'uploads/profiles/' . $fileName;
    if (!move_uploaded_file($temporaryPath, $absolutePath)) {
        throw new RuntimeException('The profile image could not be saved. Check folder permissions and try again.');
    }

    try {
        $stmt = $pdo->prepare('SELECT id, avatar FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([(int)$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            throw new RuntimeException('The account no longer exists.');
        }
        $oldAvatar = (string)($user['avatar'] ?? '');

        $update = $pdo->prepare('UPDATE users SET avatar = ? WHERE id = ?');
        $update->execute([$relativePath, (int)$userId]);
        if ($update->rowCount() < 1) {
            $verify = $pdo->prepare('SELECT avatar FROM users WHERE id = ? LIMIT 1');
            $verify->execute([(int)$userId]);
            if ($verify->fetchColumn() !== $relativePath) {
                throw new RuntimeException('The profile image was uploaded, but the account could not be updated.');
            }
        }
    } catch (Throwable $e) {
        @unlink($absolutePath);
        throw $e;
    }

    // Remove only the previous local profile image, after the new database path is saved.
    $oldAvatar = str_replace('\\', '/', ltrim($oldAvatar, '/'));
    if (strpos($oldAvatar, 'uploads/profiles/') === 0 && !preg_match('~(^|/)\.\.?(/|$)~', $oldAvatar)) {
        $oldAbsolutePath = realpath($projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $oldAvatar));
        $resolvedUploadDirectory = realpath($uploadDirectory);
        if ($oldAbsolutePath && $resolvedUploadDirectory && is_file($oldAbsolutePath)
            && strpos($oldAbsolutePath, $resolvedUploadDirectory . DIRECTORY_SEPARATOR) === 0
            && $oldAbsolutePath !== realpath($absolutePath)) {
            @unlink($oldAbsolutePath);
        }
    }

    return $relativePath;
}
