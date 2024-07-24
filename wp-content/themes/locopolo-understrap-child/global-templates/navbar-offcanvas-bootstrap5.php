<?php
/**
 * Header Navbar (bootstrap5)
 *
 * @package Understrap
 * @since 1.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = 'container-fluid';
?>

<nav id="main-nav" class="navbar navbar-expand-lg navbar-light" aria-labelledby="main-nav-label">

	<h2 id="main-nav-label" class="screen-reader-text">
		<?php esc_html_e( 'Main Navigation', 'understrap' ); ?>
	</h2>


	<div class="<?php echo esc_attr( $container ); ?>">

		<!-- Your site branding in the menu -->
		<?php get_template_part( 'global-templates/navbar-branding' ); ?>

		<button
			class="navbar-toggler"
			type="button"
			data-bs-toggle="offcanvas"
			data-bs-target="#navbarNavOffcanvas"
			aria-controls="navbarNavOffcanvas"
			aria-expanded="false"
			aria-label="<?php esc_attr_e( 'Open menu', 'understrap' ); ?>"
		>
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="navbarNavOffcanvas">

			<div class="offcanvas-header justify-content-end">
				<button
					class="btn-close text-reset"
					type="button"
					data-bs-dismiss="offcanvas"
					aria-label="<?php esc_attr_e( 'Close menu', 'understrap' ); ?>"
				></button>
			</div><!-- .offcancas-header -->

			<!-- The WordPress Menu goes here -->
			<?php
			wp_nav_menu(
				array(
					'theme_location'  => 'primary',
					'container_class' => 'offcanvas-body',
					'container_id'    => 'primary-menu',
					'menu_class'      => 'navbar-nav justify-content-end align-items-lg-center flex-grow-1 flex-wrap pe-3',
					'fallback_cb'     => '',
					'menu_id'         => 'main-menu',
					'depth'           => 2,
					'walker'          => new Understrap_WP_Bootstrap_Navwalker(),
				)
			);
			?>

			<?php
			wp_nav_menu(
				array(
					'theme_location'  => 'botones',
					'container_class' => 'offcanvas-footer',
					'container_id'    => '',
					'menu_class'      => 'navbar-nav justify-content-end align-items-center flex-grow-1 flex-wrap',
					'fallback_cb'     => '',
					'menu_id'         => 'buttons-menu',
					'depth'           => 2,
					'walker'          => new Understrap_WP_Bootstrap_Navwalker(),
				)
			);
			?>

		</div><!-- .offcanvas -->

	</div><!-- .container(-fluid) -->

</nav><!-- #main-nav -->
