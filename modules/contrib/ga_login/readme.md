All the plugins provided by this module are now part of the core tfa module.

However, some installs might be using drupal/ga_login as a composer
dependency, and not install drupal/tfa directly. If your project is one of
these, please make sure to run `composer remove drupal/ga_login`, followed
by `composer require drupal/tfa`. This change should only be performed after
running the database updates from the tfa module which will move all the
plugin configuration to the tfa module, and uninstall this module safely.
