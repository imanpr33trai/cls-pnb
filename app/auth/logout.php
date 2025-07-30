<?php
echo '<div style="background: #ffc; padding: 10px; border: 1px solid #dda; margin: 10px;"><strong>DEBUGGER:</strong> app/auth/logout.php loaded.</div>';
require_once(__DIR__ . '/../../config/config.php');

session_unset();
session_destroy();
header("Location: ./login.php");
exit;
