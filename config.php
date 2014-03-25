<?php
/**
 * Configuration settings
 *
 * @package    Formstack Mapabout
 * @author     Soon Van - randomecho.com
 * @copyright  2014 Soon Van
 * @license    http://opensource.org/licenses/BSD-3-Clause
 */

// Formstack API token - https://www.formstack.com/developers/api
$formstack_token = '';

// Enter the address you will start and finish your route at
$starting_address = '';

date_default_timezone_set('America/New_York');
setlocale(LC_ALL, 'en_US.utf-8');
session_start();