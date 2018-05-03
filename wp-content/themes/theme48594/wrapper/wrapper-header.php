<?php /* Wrapper Name: Header */ ?>
<div class="row">
	<div class="span12 hidden-phone" data-motopress-type="static" data-motopress-static-file="static/static-search.php">
		<?php get_template_part("static/static-search"); ?>
	</div>
</div>
<div class="row">
	<div class="span12" data-motopress-type="static" data-motopress-static-file="static/static-logo.php">
		<?php get_template_part("static/static-logo"); ?>
	</div>
</div>
<?php if ( is_page_template('page-home.php') ) { ?>
	<div class="row">
		<div class="span12" data-motopress-type="static" data-motopress-static-file="static/static-slider.php">
			<?php get_template_part("static/static-slider"); ?>
		</div>
	</div>
<?php } ?>
<div class="row">
	<div class="span12" data-motopress-type="static" data-motopress-static-file="static/static-nav.php">
		<?php get_template_part("static/static-nav"); ?>
	</div>
</div>