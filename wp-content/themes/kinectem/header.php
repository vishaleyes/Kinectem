<!DOCTYPE html>
<html lang="en">
<head>
<title>
<?php bloginfo('name'); ?>
<?php wp_title(''); ?>
</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
<meta name="description" content="<?php bloginfo('description'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('stylesheet_url'); ?>" />
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="icon" href="<?php /*?><!-- add link to favicon --><?php */?>" />
<link rel="shortcut icon" href="<?php /*?><!-- add link to favicon --><?php */?>" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/css/bootstrap.css">
	<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.js"></script>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <?php wp_head(); ?>
</head>
<body>
	<div class="wrapper">
		<div class="row">
			<header class="site_header col-xs-12">
				<div class="header_top">
					<div class="container">
						<div class="logo_div col-md-6">
							<a href="<?php echo get_site_url(); ?>" title="Kinectem">
								<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png">
							</a>
						</div>
						<div class="hdr_rit col-md-6">

							<div class="col-md-6">
				                
                                <form action="/" method="get">
                                <div class="input-group stylish-input-group srch_hdr">
				                    <input type="text" class="form-control"  placeholder="Search" >
				                    <span class="input-group-addon">
				                        <button type="submit">
				                            <i class="fa fa-search" aria-hidden="true"></i>
				                            <!-- <span class="glyphicon glyphicon-search"></span> -->
				                        </button>
                                        
				                    </span>
				                </div>
                                </form> 
				            </div>    
							<div class="col-md-6">
								<a href="<?php echo site_url(); ?>/teams"><i class="fa fa-users" aria-hidden="true"></i></a>
								<a href="#"><i class="fa fa-calendar-o" aria-hidden="true"></i></a>
								<a href="<?php echo bp_loggedin_user_domain(); ?>messages"><i class="fa fa-envelope" aria-hidden="true"></i></a>
								<a href="#"><i class="fa fa-bell" aria-hidden="true"></i></a>
							</div>

						</div>
					</div>
				</div>
				<!-- End Header Top -->
                <div class="banner_area">
					<div class="container">
						<div class="author_info">
							<span class="bnr_img">
                            <a href="<?php bp_activity_user_link(); ?>">
			<?php bp_loggedin_user_avatar(); ?>
		</a></span>
							<h1><?php echo bp_get_loggedin_user_fullname(); ?></h1>
						</div>									
					</div>
				</div>
			</header>
			<!-- End Header -->
		</div>
		<!-- End Header Row -->

