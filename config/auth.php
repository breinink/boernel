<?php
// Willekeurig gegenereerd token — wijzig dit naar een eigen geheime waarde
define('API_TOKEN', 'boernel_' . hash('sha256', 'change_this_secret_value'));

function requireToken() {
    $token = $_POST['_token'] ?? '';
    if (!hash_equals(API_TOKEN, $token)) {
        http_response_code(403);
        die('Toegang geweigerd.');
    }
}
