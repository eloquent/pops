<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

if (!defined('POPS_ROOT_DIR')) define('POPS_ROOT_DIR', dirname(dirname(__DIR__)));
if (!defined('POPS_SRC_DIR')) define('POPS_SRC_DIR', POPS_ROOT_DIR.DIRECTORY_SEPARATOR.'src');
if (!defined('POPS_TEST_DIR')) define('POPS_TEST_DIR', POPS_ROOT_DIR.DIRECTORY_SEPARATOR.'test');
if (!defined('POPS_TEST_SRC_DIR')) define('POPS_TEST_SRC_DIR', POPS_TEST_DIR.DIRECTORY_SEPARATOR.'src');
if (!defined('POPS_TEST_SUITE_DIR')) define('POPS_TEST_SUITE_DIR', POPS_TEST_DIR.DIRECTORY_SEPARATOR.'suite');
if (!defined('POPS_TEST_REPORT_DIR')) define('POPS_TEST_REPORT_DIR', POPS_TEST_DIR.DIRECTORY_SEPARATOR.'report');

// include Pops
require POPS_SRC_DIR.DIRECTORY_SEPARATOR.'include.php';

// include test fixtures
require POPS_TEST_SRC_DIR.DIRECTORY_SEPARATOR.'include.php';

// clean reports
foreach(glob(POPS_TEST_REPORT_DIR.DIRECTORY_SEPARATOR.'*') as $report)
{
  pops_test_delete_recursive($report);
}

function pops_test_delete_recursive($path)
{
  if (is_dir($path))
  {
    array_map('pops_test_delete_recursive', glob($path.DIRECTORY_SEPARATOR.'*'));
    rmdir($path);
  }
  elseif (is_file($path))
  {
    unlink($path);
  }
}
