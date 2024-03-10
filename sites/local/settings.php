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
  '^.+\.mtrc\.com$',
  '^192\.168.\100.\94$',
];
$settings['block_interest_cohort'] = FALSE;
$settings['pickup_call_url'] = 'http://192.168.11.175:8080/UCCS/ws/api/outcall?username=mtrc&password=mtrc&';
$settings['config_sync_directory'] = $site_path.'/files/sync';
$databases['default']['default']['database'] = 'mtrc';
$databases['default']['default']['prefix'] = '';
$databases['default']['default']['namespace'] = 'Drupal\\Core\\Database\\Driver\\mysql';
$databases['default']['default']['driver'] = 'mysql';
$databases['default']['default']['autoload'] = 'core/modules/mysql/src/Driver/Database/mysql/';
// $config['system.logging']['error_level'] = 'verbose';
$databases['default']['default']['username'] = 'root';
$databases['default']['default']['password'] = 'password';
// $databases['default']['default']['host'] = 'db-stag-mtrc.sigmaxu.com';
$databases['default']['default']['host'] = '172.20.0.2';
$databases['default']['default']['port'] = '3306';

$settings['sftp']['url'] = 'sftpuat.apac.chubb.com';
$settings['sftp']['username'] = 'UCHBHKLAUS';
$settings['sftp']['password'] = 'UHKLaus@0108';

