<?php get_header();?>
<div class="row">
			<div class="container content">
           <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<div class="post">
  <h2><a href="<?php the_permalink(); ?>">
    <?php the_title(); ?>
  </a></h2>
  <small>
  <?php the_time('F jS, Y'); ?>
  </small>
  <div class="entry">
    <?php the_content(); ?>
  </div>
  <p class="postmetadata">Posted in
    <?php the_category(', '); ?>
  </p>
</div>
<?php endwhile; else: ?>
<p>Sorry, no posts matched your criteria.</p>
<?php endif; ?>
     
</div>
</div>

<?php get_footer(); ?>