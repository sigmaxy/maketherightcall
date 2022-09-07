/**
* @file
*/

(function ($, Drupal) {
Drupal.AjaxCommands.prototype.hello = function (ajax, response, status) {
  console.log(response.message);
}

})(jQuery, Drupal);
