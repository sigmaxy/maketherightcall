<?php

$databases = [];

$settings['hash_salt'] = 'aDkLtX_JKMwIQcuKfc6snafLJ9QpzOm1c7LzltzW-qizUlAixsdJ1yrq-VuYrG0Eao0VxMgSWA';

$settings['update_free_access'] = FALSE;

$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';

$settings['file_scan_ignore_directories'] = [
  'node_modules',
  'bower_components',
];

$settings['entity_update_batch_size'] = 50;

$settings['entity_update_backup'] = TRUE;

$settings['migrate_node_migrate_type_classic'] = FALSE;

$settings['trusted_host_patterns'] = [ 
  '^localhost$',
  '^.+\.sigmaxu\.com$',
  '^.+\.f4fonline\.com$',
  '^192\.168.\100.\94$',
  '.*'
];
$settings['block_interest_cohort'] = FALSE;
$config['system.logging']['error_level'] = 'verbose';
$settings['pickup_call_url'] = 'https://maketherightcall.com/';
$settings['config_sync_directory'] = $site_path.'/files/sync';
$settings['file_temporary_path'] = $site_path.'/files/temp';
$settings['file_temporary_path'] = $site_path.'/files/temp';
$databases['default']['default']['database'] = 'mtrc';
$databases['default']['default']['prefix'] = '';
$databases['default']['default']['namespace'] = 'Drupal\\Core\\Database\\Driver\\mysql';
$databases['default']['default']['driver'] = 'mysql';
$databases['default']['default']['autoload'] = 'core/modules/mysql/src/Driver/Database/mysql/';

//local
$databases['default']['default']['username'] = 'root';
$databases['default']['default']['password'] = 'P@$$w0rd';
$databases['default']['default']['host'] = 'localhost';
$databases['default']['default']['port'] = '3306';

$settings['sftp']['url'] = 'sftpuat.apac.chubb.com';
$settings['sftp']['username'] = 'UCHBHKLAUS';
$settings['sftp']['password'] = 'UHKLaus@0108';

// $settings['sftp']['url'] = 'sftp.apac.chubb.com';
// $settings['sftp']['username'] = 'PCHBHKLAUS';
// $settings['sftp']['password'] = 'HKLaus@2806';