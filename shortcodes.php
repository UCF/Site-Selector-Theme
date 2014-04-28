<?php
/**
 * Create a javascript slideshow of each top level element in the
 * shortcode.  All attributes are optional, but may default to less than ideal
 * values.  Available attributes:
 * 
 * height     => css height of the outputted slideshow, ex. height="100px"
 * width      => css width of the outputted slideshow, ex. width="100%"
 * transition => length of transition in milliseconds, ex. transition="1000"
 * cycle      => length of each cycle in milliseconds, ex cycle="5000"
 * animation  => The animation type, one of: 'slide' or 'fade'
 *
 * Example:
 * [slideshow height="500px" transition="500" cycle="2000"]
 * <img src="http://some.image.com" .../>
 * <div class="robots">Robots are coming!</div>
 * <p>I'm a slide!</p>
 * [/slideshow]
 **/
function sc_slideshow($attr, $content=null){
	$content = cleanup(str_replace('<br />', '', $content));
	$content = DOMDocument::loadHTML($content);
	$html    = $content->childNodes->item(1);
	$body    = $html->childNodes->item(0);
	$content = $body->childNodes;
	
	# Find top level elements and add appropriate class
	$items = array();
	foreach($content as $item){
		if ($item->nodeName != '#text'){
			$classes   = explode(' ', $item->getAttribute('class'));
			$classes[] = 'slide';
			$item->setAttribute('class', implode(' ', $classes));
			$items[] = $item->ownerDocument->saveXML($item);
		}
	}
	
	$animation = ($attr['animation']) ? $attr['animation'] : 'slide';
	$height    = ($attr['height']) ? $attr['height'] : '100px';
	$width     = ($attr['width']) ? $attr['width'] : '100%';
	$tran_len  = ($attr['transition']) ? $attr['transition'] : 1000;
	$cycle_len = ($attr['cycle']) ? $attr['cycle'] : 5000;
	
	ob_start();
	?>
	<div 
		class="slideshow <?=$animation?>"
		data-tranlen="<?=$tran_len?>"
		data-cyclelen="<?=$cycle_len?>"
		style="height: <?=$height?>; width: <?=$width?>;"
	>
		<?php foreach($items as $item):?>
		<?=$item?>
		<?php endforeach;?>
	</div>
	<?php
	$html = ob_get_clean();
	
	return $html;
}
add_shortcode('slideshow', 'sc_slideshow');


function sc_search_form() {
	ob_start();
	?>
	<div class="search">
		<?get_search_form()?>
	</div>
	<?
	return ob_get_clean();
}
add_shortcode('search_form', 'sc_search_form');


/**
 * Post search
 *
 * @return string
 * @author Chris Conover
 **/
function sc_post_type_search($params=array(), $content='') {
	$defaults = array(
		'post_type_name'         => 'post',
		'taxonomy'               => 'category',
		'show_empty_sections'    => false,
		'non_alpha_section_name' => 'Other',
		'column_width'           => 'span4',
		'column_count'           => '3',
		'order_by'               => 'title',
		'order'                  => 'ASC',
		'show_sorting'           => True,
		'default_sorting'        => 'term',
		'show_sorting'           => True
	);

	$params = ($params === '') ? $defaults : array_merge($defaults, $params);

	$params['show_empty_sections'] = (bool)$params['show_empty_sections'];
	$params['column_count']        = is_numeric($params['column_count']) ? (int)$params['column_count'] : $defaults['column_count'];
	$params['show_sorting']        = (bool)$params['show_sorting'];

	if(!in_array($params['default_sorting'], array('term', 'alpha'))) {
		$params['default_sorting'] = $default['default_sorting'];
	}

	// Resolve the post type class
	if(is_null($post_type_class = get_custom_post_type($params['post_type_name']))) {
		return '<p>Invalid post type.</p>';
	}
	$post_type = new $post_type_class;

	// Set default search text if the user didn't
	if(!isset($params['default_search_text'])) {
		$params['default_search_text'] = 'Find a '.$post_type->singular_name;
	}

	// Register if the search data with the JS PostTypeSearchDataManager
	// Format is array(post->ID=>terms) where terms include the post title
	// as well as all associated tag names
	$search_data = array();
	foreach(get_posts(array('numberposts' => -1, 'post_type' => $params['post_type_name'])) as $post) {
		$search_data[$post->ID] = array($post->post_title);
		foreach(wp_get_object_terms($post->ID, 'post_tag') as $term) {
			$search_data[$post->ID][] = $term->name;
		}
	}
	?>
	<script type="text/javascript">
		if(typeof PostTypeSearchDataManager != 'undefined') {
			PostTypeSearchDataManager.register(new PostTypeSearchData(
				<?=json_encode($params['column_count'])?>,
				<?=json_encode($params['column_width'])?>,
				<?=json_encode($search_data)?>
			));
		}
	</script>
	<?

	// Split up this post type's posts by term
	$by_term = array();
	foreach(get_terms($params['taxonomy']) as $term) {
		$posts = get_posts(array(
			'numberposts' => -1,
			'post_type'   => $params['post_type_name'],
			'tax_query'   => array(
				array(
					'taxonomy' => $params['taxonomy'],
					'field'    => 'id',
					'terms'    => $term->term_id
				)
			),
			'orderby'     => $params['order_by'],
			'order'       => $params['order']
		));

		if(count($posts) == 0 && $params['show_empty_sections']) {
			$by_term[$term->name] = array();
		} else {
			$by_term[$term->name] = $posts;
		}
	}

	// Split up this post type's posts by the first alpha character
	$by_alpha = array();
	$by_alpha_posts = get_posts(array(
		'numberposts' => -1,
		'post_type'   => $params['post_type_name'],
		'orderby'     => 'title',
		'order'       => 'alpha'
	));
	foreach($by_alpha_posts as $post) {
		if(preg_match('/([a-zA-Z])/', $post->post_title, $matches) == 1) {
			$by_alpha[strtoupper($matches[1])][] = $post;
		} else {
			$by_alpha[$params['non_alpha_section_name']][] = $post;
		}
	}
	ksort($by_alpha);

	if($params['show_empty_sections']) {
		foreach(range('a', 'z') as $letter) {
			if(!isset($by_alpha[strtoupper($letter)])) {
				$by_alpha[strtoupper($letter)] = array();
			}
		}
	}

	$sections = array(
		'post-type-search-term'  => $by_term,
		'post-type-search-alpha' => $by_alpha,
	);

	ob_start();
	?>
	<div class="post-type-search">
		<div class="post-type-search-header">
			<form class="post-type-search-form" action="." method="get">
				<label style="display:none;">Search</label>
				<input type="text" class="span3" placeholder="<?=$params['default_search_text']?>" />
			</form>
		</div>
		<div class="post-type-search-results "></div>
		<? if($params['show_sorting']) { ?>
		<div class="btn-group post-type-search-sorting">
			<button class="btn<?if($params['default_sorting'] == 'term') echo ' active';?>"><i class="icon-list-alt"></i></button>
			<button class="btn<?if($params['default_sorting'] == 'alpha') echo ' active';?>"><i class="icon-font"></i></button>
		</div>
		<? } ?>
	<?

	foreach($sections as $id => $section) {
		$hide = false;
		switch($id) {
			case 'post-type-search-alpha':
				if($params['default_sorting'] == 'term') {
					$hide = True;
				}
				break;
			case 'post-type-search-term':
				if($params['default_sorting'] == 'alpha') {
					$hide = True;
				}
				break;
		}
		?>
		<div class="<?=$id?>"<? if($hide) echo ' style="display:none;"'; ?>>
			<? foreach($section as $section_title => $section_posts) { ?>
				<? if(count($section_posts) > 0 || $params['show_empty_sections']) { ?>
					<div>
						<h3><?=esc_html($section_title)?></h3>
						<div class="row">
							<? if(count($section_posts) > 0) { ?>
								<? $posts_per_column = ceil(count($section_posts) / $params['column_count']); ?>
								<? foreach(range(0, $params['column_count'] - 1) as $column_index) { ?>
									<? $start = $column_index * $posts_per_column; ?>
									<? $end   = $start + $posts_per_column; ?>
									<? if(count($section_posts) > $start) { ?>
									<div class="<?=$params['column_width']?>">
										<ul>
										<? foreach(array_slice($section_posts, $start, $end) as $post) { ?>
											<li data-post-id="<?=$post->ID?>"><?=$post_type->toHTML($post)?></li>
										<? } ?>
										</ul>
									</div>
									<? } ?>
								<? } ?>
							<? } ?>
						</div>
					</div>
				<? } ?>
			<? } ?>
		</div>
		<?
	}
	?> </div> <?
	return ob_get_clean();
}
add_shortcode('post-type-search', 'sc_post_type_search');


/**
* Wrap arbitrary text in <blockquote>
**/
function sc_blockquote($attr, $content='') {
	$source = $attr['source'] ? $attr['source'] : null;
	$cite = $attr['cite'] ? $attr['cite'] : null;
	$color = $attr['color'] ? $attr['color'] : null;

	$html = '<blockquote';
	if ($source) {
		$html .= ' class="quote"';
	}

	    if ($color) {
	        $html .= ' style="color: ' . $color . '"';
	    }

	$html .= '><p';
	    if ($color) {
	        $html .= ' style="color: ' . $color . '"';
	    }
	    $html .= '>'.$content.'</p>';

	if ($source || $cite) {
		$html .= '<small';
	        if ($color) {
	            $html .= ' style="color: ' . $color . '"';
	        }
	        $html .= '>';

	if ($source) {
		$html .= $source;
	}
	if ($cite) {
		$html .= '<cite title="'.$cite.'">'.$cite.'</cite>';
	}
		$html .= '</small>';
	}
	$html .= '</blockquote>';

	return $html;
}
add_shortcode('blockquote', 'sc_blockquote');


/**
 * Display a Homepage Feature.  Creates necessary markup for displaying a
 * full-screen background image with parallax effects.
 **/
function sc_homepage_feature($attrs, $content=null) {
	$title = $attrs['title'];
	$feature = !empty($title) ? get_page_by_title($title, 'OBJECT', 'homepage_feature') : null;
	if ($feature) {
		$offset = get_post_meta($feature->ID, 'homepage_feature_callout_position', true) == 'right' ? 'offset4' : '';
		$featured_img_id = get_post_thumbnail_id($feature->ID);

		$featured_img_f = wp_get_attachment_image_src($featured_img_id, 'homepage_feature-full');
		$featured_img_d = get_homepage_feature_img($feature->ID, 'homepage_feature-desktop', 'homepage_feature_image_d');
		$featured_img_t = get_homepage_feature_img($feature->ID, 'homepage_feature-tablet', 'homepage_feature_image_t');
		$featured_img_m = get_homepage_feature_img($feature->ID, 'homepage_feature-mobile', 'homepage_feature_image_m');
		if ($featured_img_f) { $featured_img_f = $featured_img_f[0]; }

		ob_start();
		?>
		<style type="text/css">
			<?php if ($featured_img_f) { ?>
			@media all and (min-width: 1200px) {
				#photo_<?=$feature->ID?> { background-image: url('<?=$featured_img_f?>'); }
			}
			<?php } ?>
			<?php if ($featured_img_d) { ?>
			@media all and (max-width: 1199px) and (min-width: 768px) {
				#photo_<?=$feature->ID?> { background-image: url('<?=$featured_img_d?>'); }
			}
			<?php } ?>
			<?php if ($featured_img_t) { ?>
			@media all and (max-width: 767px) and (min-width: 481px) {
				#photo_<?=$feature->ID?> { background-image: url('<?=$featured_img_t?>'); }
			}
			<?php } ?>
			<?php if ($featured_img_m) { ?>
			@media all and (max-width: 480px) {
				#photo_<?=$feature->ID?> { background-image: url('<?=$featured_img_m?>'); }
			}
			<?php } ?>
		</style>
		<section>
			<div class="photo" id="photo_<?=$feature->ID?>" data-stellar-background-ratio="0.5">
				<div class="container">
					<div class="row">
						<div class="span8 <?=$offset?>">
							<div class="callout">
								<?=apply_filters('the_content', $feature->post_content)?>
							</div>
						</div>
					</div>
				</div>
				<div class="cta">
					<a href="#">Partner with us.</a>
				</div>
			</div>
			<?php if ($content) {
				print apply_filters('the_content', $content);
			}
			?>
		</section>
		<?php
		return ob_get_clean();
	}
	else {
		return null;
	}
}
add_shortcode('homepage_feature', 'sc_homepage_feature');
?>