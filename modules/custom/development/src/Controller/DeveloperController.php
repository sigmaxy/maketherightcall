<?php

namespace Drupal\development\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

// use Drupal\api\Controller\APIController;
// use PhpOffice\PhpSpreadsheet\IOFactory;
// use PhpOffice\PhpSpreadsheet\Reader\Xls;
// use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
// use PhpOffice\PhpSpreadsheet\Reader\Csv;

/**
 * Class DeveloperController.
 */
class DeveloperController extends ControllerBase {

  /**
   * Test.
   *
   * @return string
   *   Return Hello string.
   */
  public function action($actionname = NULL) {
    switch ($actionname) {
      case 'test':
        self::test();
      break;
      case 'phpinfo':
        self::php_info();exit;
      break;
      default:
        echo "no action"; exit;
      break;
    }
    exit;
  }
  public static function test(){
    echo 'sigma';exit;
    // echo 'English Charactor: China Hong Kong F4F<br>';
    // echo 'Chinese Charactor: 中国香港F4F<br>';
    // // echo 'Translate Chinese into Pinyin: '.php_transpinyin('中国香港F4F').'<br>';
    // echo 'Convert Chinese into UT8 '.json_encode('中国香港F4F').'<br>';
    phpinfo();
    
    exit;
  }
  public static function php_info(){
    phpinfo();
  }
}
