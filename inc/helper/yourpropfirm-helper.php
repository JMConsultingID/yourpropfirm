<?php
/**
 * Plugin functions and definitions for Helper.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */

function yourpropfirm_connection_response_logger() {
    $logger = wc_get_logger();
    $context = array('source' => 'yourpropfirm_connection_response_log');
    return array('logger' => $logger, 'context' => $context);
}