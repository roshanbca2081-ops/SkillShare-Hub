<?php
require_once '../../config/session.php';
require_once '../../config/auth.php';
requireFresher();
header('Location: setting.php?section=security#security');
exit;
