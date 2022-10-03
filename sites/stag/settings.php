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
  '^.+\.f4fonline\.com$'
];
$settings['block_interest_cohort'] = FALSE;
$settings['pickup_call_url'] = 'https://maketherightcall.com/';
$settings['config_sync_directory'] = $site_path.'/files/sync';
$databases['default']['default']['database'] = 'mtrc';
$databases['default']['default']['prefix'] = '';
$databases['default']['default']['namespace'] = 'Drupal\\Core\\Database\\Driver\\mysql';
$databases['default']['default']['driver'] = 'mysql';
$databases['default']['default']['autoload'] = 'core/modules/mysql/src/Driver/Database/mysql/';
// $config['system.logging']['error_level'] = 'verbose';
$settings['sftp']['url'] = 'sftpuat.apac.chubb.com';
$settings['sftp']['username'] = 'UCHBHKLAUS';
$settings['sftp']['password'] = 'UHKLaus@0108';