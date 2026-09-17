<?php
/**
 * Archive card.
 *
 * @package Design4Web
 */
?>
<article <?php post_class( 'd4w-post-card h-100' ); ?>>
	<a class="d4w-post-card__media" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); else : ?><span><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span><?php endif; ?>
		<i class="bi bi-arrow-up-right"></i>
	</a>
	<p class="d4w-post-meta"><?php echo esc_html( get_the_date( 'd M, Y' ) ); ?> <span>•</span> <?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></p>
	<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
	<p><?php echo esc_html( get_the_excerpt() ); ?></p>
</article>
