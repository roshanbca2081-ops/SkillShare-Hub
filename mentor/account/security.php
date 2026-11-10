<?php
require_once '../../config/session.php';
require_once '../../config/auth.php';
requireMentor();
header('Location: setting.php?section=security#security');
exit;
