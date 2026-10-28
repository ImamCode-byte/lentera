<?php
/**
 * constantss.php
 * file konstanta konfigurasi sistem lenteras
 * semua variabel dan konstanta konsisten dengan aturan lowercase dan akhiran s
 */

// peran penggunas (roles)
define('role_authors', 'author');
define('role_curators', 'curator');
define('role_admins', 'admin');

// status karyas (work status)
define('status_drafts', 'draft');
define('status_submitteds', 'submitted');
define('status_in_reviews', 'in_review');
define('status_revisions', 'revision');
define('status_waiting_payments', 'waiting_payment');
define('status_publisheds', 'published');
define('status_rejecteds', 'rejected');

// aksi kurasis (curation actions)
define('action_approves', 'approve');
define('action_revisions', 'revision');
define('action_rejects', 'reject');
define('action_in_reviews', 'in_review');
define('action_acc_payments', 'acc_payment');

// batasan berkass (upload limits)
define('max_upload_bytes_limits', 20971520); // 20 megabytes
define('allowed_mime_types_pdfs', 'application/pdf');
define('allowed_extensions_pdfs', 'pdf');
