<?php
/**
 * Template Name: CP
 * Description: Homepage for the CP
 */

get_header(); ?>

	<div style="float:left;width:20%;margin-right:5px">	
		<?php dynamic_sidebar( 'cp-left' ); ?>
	</div>

	<div style="float:left;width:59%;">
		<?php dynamic_sidebar( 'cp-center' ); ?>
	</div>

	<div style="float:right;width:20%;margin-left:5px">
		<?php dynamic_sidebar( 'cp-right' ); ?>
	</div>

<?php include (TEMPLATEPATH . '/cp-footer.php'); ?>
