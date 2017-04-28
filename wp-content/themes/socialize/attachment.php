<?php get_header(); ?>

<div id="gp-content-wrapper" class="gp-container">

	<div id="gp-content">

		<header class="gp-entry-header">
			<h1 class="gp-entry-title"><?php the_title(); ?></h1>
		</header>

		<?php the_attachment_link( get_the_ID(), true ) ?>

		<div class="gp-entry-content">
			<?php the_content(); ?>
		</div>

	</div>

<div class="gp-clear"></div></div>

<?php get_footer(); ?>