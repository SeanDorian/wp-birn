<?php
/* -----------------------------------------------------------*/
function amr_users_give_credit () {  // check if the web owner is okay to give credit on  a public list
	global $amain;
//		'no_credit' => true,
//	'givecreditmessage' => 
	
	if (empty($amain['no_credit'])) {
		$message = amr_users_random_message();
		return ('<a class="credits" style="font-weight: lighter;
			font-style: italic; font-size:0.7em; line-height:0.8em; float:right;" '
			.'href="http://wpusersplugin.com" title="'
			.$message
			.' - amr-users from wpusersplugin.com'
			.'">'.__('credits').'</a>');
	}
	else return '';

}
/* -----------------------------------------------------------*/
function amr_users_random_message () { // offer a number of ways to meaningfully give thanks for the plugin - an seo experiment
	$messages = array(
		__('wordpress user directory plugin','amr-users'),
		__('wordpress people list plugin','amr-users'),
		__('wordpress member directory plugin','amr-users'),
		__('wordpress user management plugin','amr-users'),
		__('wordpress members plugin','amr-users'),
		__('wordpress membership plugin','amr-users'),
		__('wordpress community plugin','amr-users'),
		__('wordpress users statistics plugin','amr-users'),
		__('wordpress member statistics plugin','amr-users'),
		__('wordpress club member plugin','amr-users'),
		__('wordpress subscription management plugin','amr-users'),
		__('wordpress team plugin','amr-users')
	);
	$randkey = array_rand($messages);
	return $messages[$randkey];
}
/* -----------------------------------------------------------*/
function amr_users_say_thanks_opportunity_form () {
global $amain;

	echo '<label for="no_credit">';
	_e('Do not give credit', 'amr-users'); 
	echo '</label>
	<input type="checkbox" size="2" id="no_credit" 
				name="no_credit" ';
	echo (empty($amain['no_credit'])) ? '' :' checked="checked" '; 
	echo '/><br />';
	_e('Express thanks in other ways:', 'amr-users' );
	echo ' <a href="http://wpusersplugin.com/downloads/buy-it/" title="Support development by purchasing membership and gaining access to add on functionality.">';
	_e('Buy it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a href="'.admin_url('post-new.php?post_type=post').'" title="Write a post about it.">';
	_e('Press it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a href="https://www.paypal.com" title="Send via paypal to anmari@anmari.com.">';
	_e('Send it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a href="http://wordpress.org/extend/plugins/amr-users/" title="Tell others this version works!">';
	_e('Work it','amr-users');	
	echo '</a>,&nbsp; ';	
	echo '<a href="http://wppluginmarket.com/24736/plugins-that-give-credit-to-plugins/" title="Plug all the plugins you use.">';
	_e('Plug it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a href="http://wordpress.org/extend/plugins/amr-users/" title="Rate it at wordpress">';
	_e('Rate it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a href="http://wpusersplugin.com/rss" title="Stay in touch at least - monitor the rss feed">';
	_e('Watch it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a href="http://twitter.com/?status='.esc_attr('amr-users plugin from http://wpusersplugin.com').'" title="Share something positive.">';
	_e('Tweet it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a href="http://http://wpusersplugin.com/" title="Like it from the plugin website.">';
	_e('Like it','amr-users');
	echo '</a>,&nbsp; ';

	echo '<g:plusone size="small" annotation="inline" width="120" href="http://wpusersplugin.com"></g:plusone>';
	
echo '<!-- Place this render call where appropriate -->
<script type="text/javascript">
  (function() {
    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
    po.src = \'https://apis.google.com/js/plusone.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>';
// links policy
// http://wordpress.org/extend/plugins/about/
//http://codex.wordpress.org/Theme_Review#Credit_Links

}

