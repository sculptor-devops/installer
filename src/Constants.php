<?php

define('APP_PATH', '.installer');
define('APP_COMPATIBLE', ['18.04', '20.04']);
define('APP_PANEL_USER', 'panel_user');
define('APP_STAGES', [
    'Credentials',
    'SuUser',
    'Motd',
    'Ntp',
    'Php',
    'Packages',
    'Sshd',
    'NodeJs',
    'Nginx',
    'MySql',
    'Redis',
    'LetsSEncrypt',
    'Composer',
    'Firewall',
    'Crontab',
    'CheckServices'
]);
