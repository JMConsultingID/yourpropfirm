<?php
/**
 * Plugin functions and definitions for Global.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */

// Include admin functions
require dirname(__FILE__) . '/admin/yourpropfirm-admin-functions.php';
require dirname(__FILE__) . '/admin/yourpropfirm-admin-functions-fields.php';
require dirname(__FILE__) . '/admin/yourpropfirm-admin-functions-products.php';

// Include public functions
require dirname(__FILE__) . '/public/yourpropfirm-public-functions.php';
require dirname(__FILE__) . '/public/yourpropfirm-public-functions-api.php';
require dirname(__FILE__) . '/public/yourpropfirm-public-functions-checkout.php';
require dirname(__FILE__) . '/public/yourpropfirm-public-functions-order.php';

// Include helper functions
require dirname(__FILE__) . '/helper/yourpropfirm-helper.php';