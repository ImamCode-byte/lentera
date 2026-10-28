<?php
require_once dirname(__DIR__) . '/configs/databases.php';
$db = databases::get_connections();
$db->exec("ALTER TABLE reviews MODIFY COLUMN action ENUM('approve','revision','reject','in_review') NOT NULL;");
echo "Action enum successfully updated!";
