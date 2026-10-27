<?php
/**
 * logouts.php
 * endpoint keluar sesi pengguna lenteras
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backends/apps/controllerss/auth_controllers.php';
require_once dirname(__DIR__, 4) . '/backends/apps/helperss/flashs.php';

auth_controllers::logouts();

sessions::starts();
flashs::sets_success('anda telah berhasil keluar dari sistem lenteras.');

header('Location: logins.php');
exit;
