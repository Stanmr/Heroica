<?php

	function line_shortcode($atts, $content = null) {

	    $output = '<div class="line">';
	    	$output .= do_shortcode($content);
	    $output .= '</div>';

	    return $output;
	}
	add_shortcode('line', 'line_shortcode');


	function shortcode_recent_posts($atts, $content = null) {
		extract(shortcode_atts(array(
				'type'             => 'post',
				'category'         => '',
				'custom_category'  => '',
				'post_format'      => 'standard',
				'num'              => '5',
				'meta'             => 'true',
				'thumb'            => 'true',
				'thumb_width'      => '120',
				'thumb_height'     => '120',
				'more_text_single' => '',
				'excerpt_count'    => '0',
				'custom_class'     => ''
		), $atts));

		$output = '<ul class="recent-posts '.$custom_class.' unstyled">';

		global $post;
		global $my_string_limit_words;

		// WPML filter
		$suppress_filters = get_option('suppress_filters');
		
		if($post_format == 'standard') {

			$args = array(
						'post_type'         => $type,
						'category_name'     => $category,
						$type . '_category' => $custom_category,
						'numberposts'       => $num,
						'orderby'           => 'post_date',
						'order'             => 'DESC',
						'tax_query'         => array(
						'relation'          => 'AND',
							array(
								'taxonomy' => 'post_format',
								'field'    => 'slug',
								'terms'    => array('post-format-aside', 'post-format-gallery', 'post-format-link', 'post-format-image', 'post-format-quote', 'post-format-audio', 'post-format-video'),
								'operator' => 'NOT IN'
							)
						),
						'suppress_filters' => $suppress_filters
					);
		
		} else {
		
			$args = array(
				'post_type'         => $type,
				'category_name'     => $category,
				$type . '_category' => $custom_category,
				'numberposts'       => $num,
				'orderby'           => 'post_date',
				'order'             => 'DESC',
				'tax_query'         => array(
				'relation'          => 'AND',
					array(
						'taxonomy' => 'post_format',
						'field'    => 'slug',
						'terms'    => array('post-format-' . $post_format)
					)
				),
				'suppress_filters' => $suppress_filters
			);
		}

		$latest = get_posts($args);
		
		foreach($latest as $k => $post) {
				// Unset not translated posts
				if ( function_exists( 'wpml_get_language_information' ) ) {
					global $sitepress;

					$check              = wpml_get_language_information( $post->ID );
					$language_code      = substr( $check['locale'], 0, 2 );
					if ( $language_code != $sitepress->get_current_language() ) unset( $latest[$k] );

					// Post ID is different in a second language Solution
					if ( function_exists( 'icl_object_id' ) ) $post = get_post( icl_object_id( $post->ID, $type, true ) );
				}
				setup_postdata($post);
				$excerpt        = get_the_excerpt();
				$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
				$url            = $attachment_url['0'];
				$image          = aq_resize($url, $thumb_width, $thumb_height, true);
				
				$post_classes = get_post_class();
				foreach ($post_classes as $key => $value) {
					$pos = strripos($value, 'tag-');
					if ($pos !== false) {
						unset($post_classes[$key]);
					}
				}
				$post_classes = implode(' ', $post_classes);

				$output .= '<li class="recent-posts_li ' . $post_classes . '">';
				
				//Aside
				if($post_format == "aside") {
					
					$output .= the_content($post->ID);
				
				} elseif ($post_format == "link") {
				
					$url =  get_post_meta(get_the_ID(), 'tz_link_url', true);
				
					$output .= '<a target="_blank" href="'. $url . '">';
					$output .= get_the_title($post->ID);
					$output .= '</a>';
				
				//Quote
				} elseif ($post_format == "quote") {
				
					$quote =  get_post_meta(get_the_ID(), 'tz_quote', true);
					
					$output .= '<div class="quote-wrap clearfix">';
							
							$output .= '<blockquote>';
								$output .= $quote;
							$output .= '</blockquote>';
							
					$output .= '</div>';
				
				//Image
				} elseif ($post_format == "image") {
				
				if (has_post_thumbnail() ) :
				
					// $lightbox = get_post_meta(get_the_ID(), 'tz_image_lightbox', TRUE);
					
					$src      = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), array( '9999','9999' ), false, '' );
					
					$thumb    = get_post_thumbnail_id();
					$img_url  = wp_get_attachment_url( $thumb,'full'); //get img URL
					$image    = aq_resize( $img_url, 200, 120, true ); //resize & crop img
					
					
					$output .= '<figure class="thumbnail featured-thumbnail large">';
						$output .= '<a class="image-wrap" rel="prettyPhoto" title="' . get_the_title($post->ID) . '" href="' . $src[0] . '">';
						$output .= '<img src="' . $image . '" alt="' . get_the_title($post->ID) .'" />';
						$output .= '<span class="zoom-icon"></span></a>';
					$output .= '</figure>';
				
				endif;
				
				
				//Audio
				} elseif ($post_format == "audio") {
				
					$template_url = get_template_directory_uri();
					$id           = $post->ID;
					
					// get audio attribute
					$audio_title  = get_post_meta(get_the_ID(), 'tz_audio_title', true);
					$audio_artist = get_post_meta(get_the_ID(), 'tz_audio_artist', true);
					$audio_format = get_post_meta(get_the_ID(), 'tz_audio_format', true);
					$audio_url    = get_post_meta(get_the_ID(), 'tz_audio_url', true);

					$content_url = content_url();
					$content_str = 'wp-content';
					
					$pos    = strpos($audio_url, $content_str);
					if ($pos === false) {
						$file = $audio_url;
					} else {
						$audio_new = substr($audio_url, $pos+strlen($content_str), strlen($audio_url) - $pos);
						$file      = $content_url.$audio_new;
					}
						
					$output .= '<script type="text/javascript">
						jQuery(document).ready(function(){
							var myPlaylist_'. $id.'  = new jPlayerPlaylist({
							jPlayer: "#jquery_jplayer_'. $id .'",
							cssSelectorAncestor: "#jp_container_'. $id .'"
							}, [
							{
								title:"'. $audio_title .'",
								artist:"'. $audio_artist .'",
								'. $audio_format .' : "'. stripslashes(htmlspecialchars_decode($file)) .'"}
							], { 
								playlistOptions: {enableRemoveControls: false},
								ready: function () {jQuery(this).jPlayer("setMedia", {'. $audio_format .' : "'. stripslashes(htmlspecialchars_decode($file)) .'", poster: "'. $image .'"});
							},
							swfPath: "'. $template_url .'/flash",
							supplied: "'. $audio_format .', all",
							wmode:"window"
							});
						});
						</script>';
						
					$output .= '<div id="jquery_jplayer_'.$id.'" class="jp-jplayer"></div>
								<div id="jp_container_'.$id.'" class="jp-audio">
									<div class="jp-type-single">
										<div class="jp-gui">
											<div class="jp-interface">
												<div class="jp-progress">
													<div class="jp-seek-bar">
														<div class="jp-play-bar"></div>
													</div>
												</div>
												<div class="jp-duration"></div>
												<div class="jp-time-sep"></div>
												<div class="jp-current-time"></div>
												<div class="jp-controls-holder">
													<ul class="jp-controls">
														<li><a href="javascript:;" class="jp-previous" tabindex="1" title="'.theme_locals("prev").'"><span>'.theme_locals("prev").'</span></a></li>
														<li><a href="javascript:;" class="jp-play" tabindex="1" title="'.theme_locals("play").'"><span>'.theme_locals("play").'</span></a></li>
														<li><a href="javascript:;" class="jp-pause" tabindex="1" title="'.theme_locals("pause").'"><span>'.theme_locals("pause").'</span></a></li>
														<li><a href="javascript:;" class="jp-next" tabindex="1" title="'.theme_locals("next").'"><span>'.theme_locals("next").'</span></a></li>
														<li><a href="javascript:;" class="jp-stop" tabindex="1" title="'.theme_locals("stop").'"><span>'.theme_locals("stop").'</span></a></li>
													</ul>
													<div class="jp-volume-bar">
														<div class="jp-volume-bar-value"></div>
													</div>
													<ul class="jp-toggles">
														<li><a href="javascript:;" class="jp-mute" tabindex="1" title="'.theme_locals("mute").'"><span>'.theme_locals("mute").'</span></a></li>
														<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="'.theme_locals("unmute").'"><span>'.theme_locals("unmute").'</span></a></li>
													</ul>
												</div>
											</div>
											<div class="jp-no-solution">
												'.theme_locals("update_required").'
											</div>
										</div>
									</div>
									<div class="jp-playlist">
										<ul>
											<li></li>
										</ul>
									</div>
								</div>';
				
				
				$output .= '<div class="entry-content">';
					$output .= get_the_content($post->ID);
				$output .= '</div>';
				
				//Video
				} elseif ($post_format == "video") {
					
					$template_url = get_template_directory_uri();
					$id           = $post->ID;
				
					// get video attribute
					$video_title  = get_post_meta(get_the_ID(), 'tz_video_title', true);
					$video_artist = get_post_meta(get_the_ID(), 'tz_video_artist', true);
					$embed        = get_post_meta(get_the_ID(), 'tz_video_embed', true);
					$m4v_url      = get_post_meta(get_the_ID(), 'tz_m4v_url', true);
					$ogv_url      = get_post_meta(get_the_ID(), 'tz_ogv_url', true);

					$content_url = content_url();
					$content_str = 'wp-content';
					
					$pos1 = strpos($m4v_url, $content_str);
					if ($pos1 === false) {
						$file1 = $m4v_url;
					} else {
						$m4v_new  = substr($m4v_url, $pos1+strlen($content_str), strlen($m4v_url) - $pos1);
						$file1    = $content_url.$m4v_new;
					}

					$pos2 = strpos($ogv_url, $content_str);
					if ($pos2 === false) {
						$file2 = $ogv_url;
					} else {
						$ogv_new  = substr($ogv_url, $pos2+strlen($content_str), strlen($ogv_url) - $pos2);
						$file2    = $content_url.$ogv_new;
					}
					
					// get thumb
					if(has_post_thumbnail()) {
						$thumb   = get_post_thumbnail_id();
						$img_url = wp_get_attachment_url( $thumb,'full'); //get img URL
						$image   = aq_resize( $img_url, 770, 380, true ); //resize & crop img
					}

					if ($embed == '') {
						$output .= '<script type="text/javascript">
							jQuery(document).ready(function(){
								jQuery("#jquery_jplayer_'. $id.'").jPlayer({
									ready: function () {
										jQuery(this).jPlayer("setMedia", {
											m4v: "'. stripslashes(htmlspecialchars_decode($file1)) .'",
											ogv: "'. stripslashes(htmlspecialchars_decode($file2)) .'",
											poster: "'. $image .'"
										});
									},
									swfPath: "'. $template_url .'/flash",
									solution: "flash, html",
									supplied: "ogv, m4v, all",
									cssSelectorAncestor: "#jp_container_'. $id.'",
									size: {
										width: "100%",
										height: "100%"
									}
								});
							});
							</script>';
							$output .= '<div id="jp_container_'. $id .'" class="jp-video fullwidth">';
							$output .= '<div class="jp-type-list-parent">';
							$output .= '<div class="jp-type-single">';
							$output .= '<div id="jquery_jplayer_'. $id .'" class="jp-jplayer"></div>';
							$output .= '<div class="jp-gui">';
							$output .= '<div class="jp-video-play">';
							$output .= '<a href="javascript:;" class="jp-video-play-icon" tabindex="1" title="'.theme_locals("play").'">'.theme_locals("play").'</a></div>';
							$output .= '<div class="jp-interface">';
							$output .= '<div class="jp-progress">';
							$output .= '<div class="jp-seek-bar">';
							$output .= '<div class="jp-play-bar">';
							$output .= '</div></div></div>';
							$output .= '<div class="jp-duration"></div>';
							$output .= '<div class="jp-time-sep">/</div>';
							$output .= '<div class="jp-current-time"></div>';
							$output .= '<div class="jp-controls-holder">';
							$output .= '<ul class="jp-controls">';
							$output .= '<li><a href="javascript:;" class="jp-play" tabindex="1" title="'.theme_locals("play").'"><span>'.theme_locals("play").'</span></a></li>';
							$output .= '<li><a href="javascript:;" class="jp-pause" tabindex="1" title="'.theme_locals("pause").'"><span>'.theme_locals("pause").'</span></a></li>';
							$output .= '<li class="li-jp-stop"><a href="javascript:;" class="jp-stop" tabindex="1" title="'.theme_locals("stop").'"><span>'.theme_locals("stop").'</span></a></li>';
							$output .= '</ul>';
							$output .= '<div class="jp-volume-bar">';
							$output .= '<div class="jp-volume-bar-value">';
							$output .= '</div></div>';
							$output .= '<ul class="jp-toggles">';
							$output .= '<li><a href="javascript:;" class="jp-mute" tabindex="1" title="'.theme_locals("mute").'"><span>'.theme_locals("mute").'</span></a></li>';
							$output .= '<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="'.theme_locals("unmute").'"><span>'.theme_locals("unmute").'</span></a></li>';
							$output .= '</ul>';
							$output .= '</div></div>';
							$output .= '<div class="jp-no-solution">';
							$output .= theme_locals("update_required");
							$output .= '</div></div></div></div>';
							$output .= '</div>';
					} else {
						$output .= '<div class="video-wrap">' . stripslashes(htmlspecialchars_decode($embed)) . '</div>';
					}
					
					if($excerpt_count >= 1){
						$output .= '<div class="excerpt">';
							$output .= my_string_limit_words($excerpt,$excerpt_count);
						$output .= '</div>';
				}
				
				//Standard
				} else {

					if($custom_class=='block-output-with-aniamtion'){
						$output .= '<a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
					}
				
					if ($thumb == 'true') {
						if ( has_post_thumbnail($post->ID) ){
							$output .= '<figure class="thumbnail featured-thumbnail">';
							if($custom_class!=='block-output-with-aniamtion'){
								$output .= '<a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
							}
							$output .= '<img src="'.$image.'" alt="' . get_the_title($post->ID) .'"/>';
							if($custom_class!=='block-output-with-aniamtion'){
								$output .= '</a>';
							}
							$output .= '</figure>';
						}
					}

					$output .= '<div class="content-block">';

						if ($meta == 'true') {
								$output .= '<span class="meta">';
										$output .= '<span class="post-date">';
											$output .= '<div class="day">'.get_the_date('j').'</div>';
											$output .= '<div class="month">'.get_the_date('M').'</div>';
										$output .= '</span>';
								$output .= '</span>';
						}

						$output .= '<h5>';
						if($custom_class!=='block-output-with-aniamtion'){
							$output .='<a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
						}
								$output .= get_the_title($post->ID);
						if($custom_class!=='block-output-with-aniamtion'){
							$output .= '</a>';
						}
						$output .= '</h5>';
						
						$output .= cherry_get_post_networks(array('post_id' => $post->ID, 'display_title' => false, 'output_type' => 'return'));
						if ($excerpt_count >= 1) {
							$output .= '<div class="excerpt">';
								$output .= my_string_limit_words($excerpt,$excerpt_count);
							$output .= '</div>';
						}
						if ($more_text_single!="") {
							$output .= '<a href="'.get_permalink($post->ID).'" class="btn btn-primary" title="'.get_the_title($post->ID).'">';
							$output .= $more_text_single;
							$output .= '</a>';
						}
					$output .= '</div>';
					if($custom_class=='block-output-with-aniamtion'){
						$output .= '</a>';
					}
				}
			$output .= '<div class="clear"></div>';
			$output .= '</li><!-- .entry (end) -->';
		}
		$output .= '</ul><!-- .recent-posts (end) -->';
		return $output;
	}
	add_shortcode('recent_posts', 'shortcode_recent_posts');


	function breadcrumbs() {

	$showOnHome  = 1; // 1 - show "breadcrumbs" on home page, 0 - hide
	$delimiter   = '<li class="divider">&thinsp;|&thinsp;</li>'; // divider
	$home        = get_the_title( get_option('page_on_front', true) ); // text for link "Home"
	$showCurrent = 1; // 1 - show title current post/page, 0 - hide
	$before      = '<li class="active">'; // open tag for active breadcrumb
	$after       = '</li>'; // close tag for active breadcrumb

	global $post;
	$homeLink = home_url();

	if (is_front_page()) {
		if ($showOnHome == 1) 
			echo '<ul class="breadcrumb breadcrumb__t"><li><a href="' . $homeLink . '">' . $home . '</a><li></ul>';
		} else {
			echo '<ul class="breadcrumb breadcrumb__t"><li><a href="' . $homeLink . '">' . $home . '</a></li>' . $delimiter;

			if ( is_home() ) {
				$blog_text = of_get_option('blog_text');
				if ($blog_text == '' || empty($blog_text)) {
					echo theme_locals("blog");
				}
				echo $before . $blog_text . $after;
			} 
			elseif ( is_category() ) {
				$thisCat = get_category(get_query_var('cat'), false);
				if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter . ' ');
				echo $before . theme_locals("category_archives").': "' . single_cat_title('', false) . '"' . $after;
			} 
			elseif ( is_search() ) {
				echo $before . theme_locals("fearch_for") . ': "' . get_search_query() . '"' . $after;
			} 
			elseif ( is_day() ) {
				echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li> ' . $delimiter . ' ';
				echo '<li><a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a></li> ' . $delimiter . ' ';
				echo $before . get_the_time('d') . $after;
			} 
			elseif ( is_month() ) {
				echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li> ' . $delimiter . ' ';
				echo $before . get_the_time('F') . $after;
			} 
			elseif ( is_year() ) {
				echo $before . get_the_time('Y') . $after;
			}
			elseif ( is_tax(get_post_type().'_category') ) {
				$post_name = get_post_type();
				echo $before . ucfirst($post_name) . ' ' . theme_locals('category') . ': ' . single_cat_title( '', false ) . $after;
			}
			elseif ( is_single() && !is_attachment() ) {
				if ( get_post_type() != 'post' ) {
					$post_id = get_the_ID();
					$post_name = get_post_type();
					$post_type = get_post_type_object(get_post_type());
					// echo '<li><a href="' . $homeLink . '/' . $post_type->labels->name . '/">' . $post_type->labels->name . '</a></li>';

					$terms = get_the_terms( $post_id, $post_name.'_category');
					if ( $terms && ! is_wp_error( $terms ) ) {
						echo '<li><a href="' .get_term_link(current($terms)->slug, $post_name.'_category') .'">'.current($terms)->name.'</a></li>';
						echo ' ' . $delimiter . ' ';
					} else {
						// echo '<li><a href="' . $homeLink . '/' . $post_type->labels->name . '/">' . $post_type->labels->name . '</a></li>';
					}

					if ($showCurrent == 1)
						echo $before . get_the_title() . $after;
				} else {
					$cat = get_the_category();
					if (!empty($cat)) {
						$cat  = $cat[0];
						$cats = get_category_parents($cat, TRUE, '</li>' . $delimiter . '<li>');
						if ($showCurrent == 0) 
							$cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
						echo '<li>' . substr($cats, 0, strlen($cats)-4);
					}
					if ($showCurrent == 1) 
						echo $before . get_the_title() . $after;
				}
			}
			elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
				$post_type = get_post_type_object(get_post_type());
				if ( isset($post_type) ) {
					echo $before . $post_type->labels->singular_name . $after;
				}
			} 
			elseif ( is_attachment() ) {
				$parent = get_post($post->post_parent);
				$cat    = get_the_category($parent->ID);
				if ( isset($cat) && !empty($cat)) {
					$cat    = $cat[0];
					echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
					echo '<li><a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a></li>';
				}
				if ($showCurrent == 1) 
					echo $before . get_the_title() . $after;
			} 
			elseif ( is_page() && !$post->post_parent ) {
				if ($showCurrent == 1) 
					echo $before . get_the_title() . $after;
			} 
			elseif ( is_page() && $post->post_parent ) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page          = get_page($parent_id);
					$breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
					$parent_id     = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					echo $breadcrumbs[$i];
					if ($i != count($breadcrumbs)-1) echo ' ' . $delimiter . ' ';
				}
				if ($showCurrent == 1) 
					echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
			} 
			elseif ( is_tag() ) {
				echo $before . theme_locals("tag_archives") . ': "' . single_tag_title('', false) . '"' . $after;
			} 
			elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata($author);
				echo $before . theme_locals("by") . ' ' . $userdata->display_name . $after;
			} 
			elseif ( is_404() ) {
				echo $before . '404' . $after;
			}
			echo '</ul>';
		}
	} // end breadcrumbs()

?>