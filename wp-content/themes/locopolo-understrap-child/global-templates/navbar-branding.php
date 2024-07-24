<?php
/**
 * Navbar branding
 *
 * @package Understrap
 * @since 1.2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<div class="navbar-brand d-none d-md-block">

	<?php smn_brand_logos_dropdown(); ?>

</div>

<div class="d-md-none">

	<?php the_custom_logo(); ?>

</div>
