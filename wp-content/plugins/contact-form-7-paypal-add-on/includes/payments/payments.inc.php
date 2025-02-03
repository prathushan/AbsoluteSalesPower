<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Register custom post type 'cf7pp_payments'.
 */
require_once 'cpt.php';

/**
 * Require functions to work with payments.
 */
require_once 'functions.php';

/**
 * Require cronjob for update pending payments.
 */
require_once 'cronjob.php';

/**
 * Require Stripe payments listener.
 */
require_once 'stripe_handler.php';

/**
 * Require PayPal payments listener.
 */
require_once 'paypal_handler.php';