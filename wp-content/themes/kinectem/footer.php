<footer class="site_footer">
			<div class="container">
				<p>Copyright &copy 2016 Kinectem.com. All rights reserved.</p>
			</div>
		</footer>

	</div>
    
	<script type="text/javascript">
		$( document ).ready(function() {
			$('.butn_post a').click(function(event){
				$('.post_form').toggle();
				event.preventDefault();
			});

			$('.post_cmnt_btn').click(function(event){
				$('.cmnt_form').toggle();
				event.preventDefault();
			});

		});

$(document).ready(function() {
	        var divHeight = $('.content').height(); 
	        $('.left_side').css('height', divHeight+'px');
	    });
		$(document).ready(function() {
	        var divHeight = $('.content').height(); 
	        $('.right_side').css('height', divHeight+'px');
	    });
	</script>
<?php wp_footer(); ?>
</body>
</html>