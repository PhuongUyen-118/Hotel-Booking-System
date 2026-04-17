<?php
session_start();
session_unset();
session_destroy();
header("Location: /hotel_booking/index.php");
exit;

