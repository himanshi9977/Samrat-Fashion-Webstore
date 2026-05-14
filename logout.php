<?php
session_start();
session_destroy();
header("Location: Web.html");
exit;
?>