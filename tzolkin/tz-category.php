<?php
add_action( 'init', 'create_tz_taxonomy', 0 );

////////////////////////////////////////////////////////////////////////////////
// REGISTER TAXONOMY  /////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

function create_tz_taxonomy() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Category' ),
		'all_items'         => __( 'All Categories' ),
		'parent_item'       => __( 'Parent Category' ),
		'parent_item_colon' => __( 'Parent Category:' ),
		'edit_item'         => __( 'Edit Category' ),
		'update_item'       => __( 'Update Category' ),
		'add_new_item'      => __( 'Add New Category' ),
		'new_item_name'     => __( 'New Category Name' ),
		'menu_name'         => __( 'Categories' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => '/events/category' ),
	);

	register_taxonomy( 'tz_category', array( 'tz_event' ), $args );
}

////////////////////////////////////////////////////////////////////////////////
// ADD COLOR TO TAX  //////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

// A callback function to add a custom field to our "tz_category" taxonomy
function tz_category_taxonomy_custom_fields($tag) {
   // Check for existing taxonomy meta for the term you're editing
	$t_id = $tag->term_id; // Get the ID of the term you're editing
	$term_meta = get_option( "taxonomy_term_$t_id" ); // Do the check

	$colors = array(
		'gray'
		,'red'
		,'yellow'
		,'blue'
		,'green'
		,'orange'
		,'purple'
	);
?>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="tz-color"><?php _e('Category Color'); ?></label>
	</th>
	<td>
		<select id="tz-color" name="term_meta[color]">
			<?php
			foreach($colors as $color) {
				// Figure out who's on first.
				$selected = '';
				if ($term_meta['color'] == $color)
					$selected = 'selected';

				// Echo options based on $colors array.
				echo '<option value="'. $color .'" '. $selected .'>'. ucwords($color) .'</option>';
			}
			?>
		 </select>
		<span class="description"><?php _e('The category\'s color.'); ?></span>
	</td>
</tr>

<?php
}

// A callback function to save our extra taxonomy field(s)
function save_taxonomy_custom_fields( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_term_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ){
			if ( isset( $_POST['term_meta'][$key] ) ){
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		//save the option array
		update_option( "taxonomy_term_$t_id", $term_meta );
	}
}

// Add the fields to the "tz_category" taxonomy, using our callback function
add_action( 'tz_category_edit_form_fields', 'tz_category_taxonomy_custom_fields', 10, 2 );

// Save the changes made on the "tz_category" taxonomy, using our callback function
add_action( 'edited_tz_category', 'save_taxonomy_custom_fields', 10, 2 );

