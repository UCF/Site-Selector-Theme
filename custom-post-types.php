<?php

/**
 * Abstract class for defining custom post types.
 *
 **/
abstract class CustomPostType{
	public
		$name           = 'custom_post_type',
		$plural_name    = 'Custom Posts',
		$singular_name  = 'Custom Post',
		$add_new_item   = 'Add New Custom Post',
		$edit_item      = 'Edit Custom Post',
		$new_item       = 'New Custom Post',
		$public         = True,  # I dunno...leave it true
		$use_title      = True,  # Title field
		$use_editor     = True,  # WYSIWYG editor, post content field
		$use_revisions  = True,  # Revisions on post content and titles
		$use_thumbnails = False, # Featured images
		$use_order      = False, # Wordpress built-in order meta data
		$use_metabox    = False, # Enable if you have custom fields to display in admin
		$use_shortcode  = False, # Auto generate a shortcode for the post type
		                         # (see also objectsToHTML and toHTML methods)
		$taxonomies     = array('post_tag'),
		$built_in       = False,

		# Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null;


	/**
	 * Wrapper for get_posts function, that predefines post_type for this
	 * custom post type.  Any options valid in get_posts can be passed as an
	 * option array.  Returns an array of objects.
	 **/
	public function get_objects($options=array()){

		$defaults = array(
			'numberposts'   => -1,
			'orderby'       => 'title',
			'order'         => 'ASC',
			'post_type'     => $this->options('name'),
		);
		$options = array_merge($defaults, $options);
		$objects = get_posts($options);
		return $objects;
	}


	/**
	 * Similar to get_objects, but returns array of key values mapping post
	 * title to id if available, otherwise it defaults to id=>id.
	 **/
	public function get_objects_as_options($options=array()){
		$objects = $this->get_objects($options);
		$opt     = array();
		foreach($objects as $o){
			switch(True){
				case $this->options('use_title'):
					$opt[$o->post_title] = $o->ID;
					break;
				default:
					$opt[$o->ID] = $o->ID;
					break;
			}
		}
		return $opt;
	}


	/**
	 * Return the instances values defined by $key.
	 **/
	public function options($key){
		$vars = get_object_vars($this);
		return $vars[$key];
	}


	/**
	 * Additional fields on a custom post type may be defined by overriding this
	 * method on an descendant object.
	 **/
	public function fields(){
		return array();
	}


	/**
	 * Using instance variables defined, returns an array defining what this
	 * custom post type supports.
	 **/
	public function supports(){
		#Default support array
		$supports = array();
		if ($this->options('use_title')){
			$supports[] = 'title';
		}
		if ($this->options('use_order')){
			$supports[] = 'page-attributes';
		}
		if ($this->options('use_thumbnails')){
			$supports[] = 'thumbnail';
		}
		if ($this->options('use_editor')){
			$supports[] = 'editor';
		}
		if ($this->options('use_revisions')){
			$supports[] = 'revisions';
		}
		return $supports;
	}


	/**
	 * Creates labels array, defining names for admin panel.
	 **/
	public function labels(){
		return array(
			'name'          => __($this->options('plural_name')),
			'singular_name' => __($this->options('singular_name')),
			'add_new_item'  => __($this->options('add_new_item')),
			'edit_item'     => __($this->options('edit_item')),
			'new_item'      => __($this->options('new_item')),
		);
	}


	/**
	 * Creates metabox array for custom post type. Override method in
	 * descendants to add or modify metaboxes.
	 **/
	public function metabox(){
		if ($this->options('use_metabox')){
			return array(
				'id'       => $this->options('name').'_metabox',
				'title'    => __($this->options('singular_name').' Fields'),
				'page'     => $this->options('name'),
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => $this->fields(),
			);
		}
		return null;
	}


	/**
	 * Registers metaboxes defined for custom post type.
	 **/
	public function register_metaboxes(){
		if ($this->options('use_metabox')){
			$metabox = $this->metabox();
			add_meta_box(
				$metabox['id'],
				$metabox['title'],
				'show_meta_boxes',
				$metabox['page'],
				$metabox['context'],
				$metabox['priority']
			);
		}
	}


	/**
	 * Registers the custom post type and any other ancillary actions that are
	 * required for the post to function properly.
	 **/
	public function register(){
		$registration = array(
			'labels'     => $this->labels(),
			'supports'   => $this->supports(),
			'public'     => $this->options('public'),
			'taxonomies' => $this->options('taxonomies'),
			'_builtin'   => $this->options('built_in')
		);

		if ($this->options('use_order')){
			$registration = array_merge($registration, array('hierarchical' => True,));
		}

		register_post_type($this->options('name'), $registration);

		if ($this->options('use_shortcode')){
			add_shortcode($this->options('name').'-list', array($this, 'shortcode'));
		}
	}


	/**
	 * Shortcode for this custom post type.  Can be overridden for descendants.
	 * Defaults to just outputting a list of objects outputted as defined by
	 * toHTML method.
	 **/
	public function shortcode($attr){
		$default = array(
			'type' => $this->options('name'),
		);
		if (is_array($attr)){
			$attr = array_merge($default, $attr);
		}else{
			$attr = $default;
		}
		return sc_object_list($attr);
	}


	/**
	 * Handles output for a list of objects, can be overridden for descendants.
	 * If you want to override how a list of objects are outputted, override
	 * this, if you just want to override how a single object is outputted, see
	 * the toHTML method.
	 **/
	public function objectsToHTML($objects, $css_classes){
		if (count($objects) < 1){ return '';}

		$class = get_custom_post_type($objects[0]->post_type);
		$class = new $class;

		ob_start();
		?>
		<ul class="<?php if($css_classes):?><?=$css_classes?><?php else:?><?=$class->options('name')?>-list<?php endif;?>">
			<?php foreach($objects as $o):?>
			<li>
				<?=$class->toHTML($o)?>
			</li>
			<?php endforeach;?>
		</ul>
		<?php
		$html = ob_get_clean();
		return $html;
	}


	/**
	 * Outputs this item in HTML.  Can be overridden for descendants.
	 **/
	public function toHTML($object){
		$html = '<a href="'.get_permalink($object->ID).'">'.$object->post_title.'</a>';
		return $html;
	}
}

class Page extends CustomPostType {
	public
		$name           = 'page',
		$plural_name    = 'Pages',
		$singular_name  = 'Page',
		$add_new_item   = 'Add New Page',
		$edit_item      = 'Edit Page',
		$new_item       = 'New Page',
		$public         = True,
		$use_editor     = True,
		$use_thumbnails = False,
		$use_order      = True,
		$use_title      = True,
		$use_metabox    = True,
		$built_in       = True;

	public function fields() {
		$prefix = $this->options('name').'_';
		return array(
			array(
				'name' => 'Featured image "Desktop" size override',
				'desc' => 'Overrides the automatically generated "Desktop" size of the featured image for this page. Recommended image size: 1199x750px.',
				'id' => $prefix.'image_d',
				'type' => 'file',
			),
			array(
				'name' => 'Featured image "Tablet" size override',
				'desc' => 'Overrides the automatically generated "Tablet" size of the featured image for this page. Recommended image size: 767x775px.',
				'id' => $prefix.'image_t',
				'type' => 'file',
			),
			array(
				'name' => 'Featured image "Mobile" size override',
				'desc' => 'Overrides the automatically generated "Mobile" size of the featured image for this page. Recommended image size: 480x475px.',
				'id' => $prefix.'image_m',
				'type' => 'file',
			),
		);
	}
}

class Post extends CustomPostType {
	public
		$name           = 'post',
		$plural_name    = 'Posts',
		$singular_name  = 'Post',
		$add_new_item   = 'Add New Post',
		$edit_item      = 'Edit Post',
		$new_item       = 'New Post',
		$public         = True,
		$use_editor     = True,
		$use_thumbnails = False,
		$use_order      = True,
		$use_title      = True,
		$use_metabox    = True,
		$taxonomies     = array('post_tag', 'category'),
		$built_in       = True;

	public function fields() {
		$prefix = $this->options('name').'_';
		return array();
	}
}

class ParallaxFeature extends CustomPostType {
	public
		$name           = 'parallax_feature',
		$plural_name    = 'Parallax Features',
		$singular_name  = 'Parallax Feature',
		$add_new_item   = 'Add New Parallax Feature',
		$edit_item      = 'Edit Parallax Feature',
		$new_item       = 'New Parallax Feature',
		$public         = True,
		$use_editor     = True,
		$use_thumbnails = True,
		$use_order      = True,
		$use_title      = True,
		$use_metabox    = True,
		$taxonomies     = array();

	/**
	 * Grab array of pages for CTA link options
	 **/
	public function get_page_options() {
		$pages = get_posts(array('posts_per_page' => -1, 'post_type' => 'page'));
		$pages_array = array();
		foreach ($pages as $page) {
			$pages_array[$page->post_title] = $page->ID;
		}

		return $pages_array;
	}

	public function fields() {
		$prefix = $this->options('name').'_';
		return array(
			array(
				'name' => 'Callout box position',
				'desc' => 'Position of the yellow box inside the parallax feature.',
				'id' => $prefix.'callout_position',
				'type' => 'radio',
				'options' => array(
					'Left' => 'left',
					'Right' => 'right'
				),
			),
			array(
				'name' => 'Featured image "Desktop" size override',
				'desc' => 'Overrides the automatically generated "Desktop" size of the featured image for this parallax feature. Recommended image size: 1199x925px.',
				'id' => $prefix.'image_d',
				'type' => 'file',
			),
			array(
				'name' => 'Featured image "Tablet" size override',
				'desc' => 'Overrides the automatically generated "Tablet" size of the featured image for this parallax feature. Recommended image size: 767x775px.',
				'id' => $prefix.'image_t',
				'type' => 'file',
			),
			array(
				'name' => 'Featured image "Mobile" size override',
				'desc' => 'Overrides the automatically generated "Mobile" size of the featured image for this parallax feature. Recommended image size: 480x475px.',
				'id' => $prefix.'image_m',
				'type' => 'file',
			),
			array(
				'name' => 'Display CTA',
				'desc' => 'Display a call to action button in the bottom-center edge of the feature.',
				'id' => $prefix.'display_cta',
				'type' => 'checkbox'
			),
			array(
				'name' => 'CTA Text',
				'desc' => 'Text within the call to action button.',
				'id' => $prefix.'cta_text',
				'type' => 'text'
			),
			array(
				'name' => 'CTA Link to Page',
				'desc' => 'Page that the call to action button links to.',
				'id' => $prefix.'cta_link',
				'type' => 'select',
				'options' => $this->get_page_options(),
			),
		);
	}
}
?>
