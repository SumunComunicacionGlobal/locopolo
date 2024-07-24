<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
global $post;
?>

<div class="related-product">

    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">

        <?php the_post_thumbnail( 'medium' ); ?>

    </a>

</div>
        

