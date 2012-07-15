<?php 

add_action( 'wp_dashboard_setup', 'thefrosty_dashboard_widgets' );

if ( !function_exists( 'thefrosty_dashboard_widgets' ) ) {
	function thefrosty_dashboard_widgets() {
		global $wp_meta_boxes;
	
		wp_add_dashboard_widget( 'thefrosty_dashboard', __( 'The Frosty Network <em>feeds</em>' ), 'thefrosty_dashboard_widget_rss' );
	}
}

// Function: Print Dashboard Widget
if ( !function_exists( 'thefrosty_dashboard_widget_rss' ) ) {
	function thefrosty_dashboard_widget_rss( $sidebar_args ) {
		global $wpdb;
		extract( array($sidebar_args, EXTR_SKIP));
		
			//echo '<a href="http://frosty.me/cl"><img style="float:right; margin: 0 0 5px 5px;" src="' . plugin_dir_url( __FILE__ ) . '/Austin_Passy.jpg" alt="frosty" /></a>';
			$style  = '<style type="text/css">';
			$style .= '.frosty .frosty-image { display:inline-block; height:25px; float:left; width:25px; overflow:hidden }' . "\n";
			$style .= '.frosty .frosty-image span { background:url("' . esc_url( plugin_dir_url( __FILE__ ) ) . 'Sprite.jpg") 0 0 no-repeat; display: inline-block; height: 25px; width: 25px }' . "\n";
			$style .= '.frosty li { padding-left:30px }' . "\n";
			$style .= 'span.austinpassy { background-position: -31px 0 !important }' . "\n";
			$style .= 'span.jeanaarter { background-position: -60px 0 !important }' . "\n";
			$style .= 'span.wordcamp { background-position: -92px 0 !important }' . "\n";
			$style .= 'span.floatoholics { background-position: -124px 0 !important }' . "\n";
			$style .= 'span.thefrosty { background-position: -156px 0 !important }' . "\n";
			$style .= 'span.greatescapecabofishing { background-position: -193px 0 !important }' . "\n";
			$style .= 'span.wpworkshop { background-position: -221px 0 !important }' . "\n";
			$style .= 'span.infieldbox { background-position: -251px 0 !important }' . "\n";
			$style .= '</style>' . "\n";
			
			$domain = preg_replace( '|https?://([^/]+)|', '$1', get_option( 'siteurl' ) );
			
			include_once( ABSPATH . WPINC . '/class-simplepie.php' );
			$feed = new SimplePie();
			
			$feed->set_feed_url( 'http://pipes.yahoo.com/pipes/pipe.run?_id=52c339c010550750e3e64d478b1c96ea&_render=rss' );
			
			$feed->enable_cache( true );
			$cache_folder = plugin_dir_path( __FILE__ ) . 'cache';
			if ( !is_writable( $cache_folder ) ) chmod( $cache_folder, 0666 );
			$feed->set_cache_location( $cache_folder );
				
			$feed->init();
			$feed->handle_content_type();
	
			$items = $feed->get_item();	
			echo '<ul class="frosty">';		
			if ( empty( $items ) ) { 
				echo '<li>No items</li>';		
			} else {
				echo $style;
				foreach( $feed->get_items( 0, 6 ) as $item ) : 
				
					$title = esc_attr( strtolower( sanitize_title_with_dashes( htmlentities( $item->get_title() ) ) ) );
					
					$class = str_replace( 'http://', '', $item->get_permalink() ); 
					$class = str_replace( array( '2010.', '2011.', '2012.', '2014.' ), '', $class );
					$class = str_replace( array( '.com/', '.net/', '.org/', '.la/', 'la.' ), ' ', $class );
					$class = str_replace( array( '2011/', '2012/', '2013/', '2014/' ), '', $class );
					$class = str_replace( array( '01/', '02/', '03/', '04/', '05/', '06/', '07/', '08/', '09/', '10/', '11/', '12/' ), '', $class );
					$class = str_replace( $title, '', $class );
					$class = str_replace( '/', '', $class );
					$class = str_replace( 'feedproxy.google', '', $class );
					$class = str_replace( '~r', '', $class );
					$class = str_replace( '~', ' ', $class );
					// Redundant, I know. Can you make a preg_replace for this? ?>
                    
                    <div class="frosty-image">
                    	<span class="<?php echo strtolower( esc_attr( $class ) ); ?>">&nbsp;</span>
                    </div>
					<li>
						<a class="rsswidget" href="<?php echo esc_url( $item->get_permalink() ); ?>" title="<?php esc_attr_e( $item->get_description() ); ?>"><?php esc_attr_e( $item->get_title() ); ?></a>		
						<span style="font-size:10px; color:#aaa;"><?php echo esc_html( $item->get_date('F, jS Y') ); ?></span>			
					</li>		
				<?php endforeach;
			}
			echo '</ul>';
			
	}
}

if ( !function_exists( 'thefrosty_dashboard_callback' ) ) {
	function thefrosty_dashboard_callback( $sidebar_args ) {
		array(
			'all_link' => 'http://thefrosty.net/',
			'feed_link' => 'http://pipes.yahoo.com/pipes/pipe.run?_id=52c339c010550750e3e64d478b1c96ea&_render=rss',
			'width' => 'half', // OR 'fourth', 'third', 'half', 'full' (Default: 'half')
			'height' => 'double', // OR 'single', 'double' (Default: 'single')
		);
	}
}

?>