<?php
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', 0); // set to 1 if HTTPS

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
