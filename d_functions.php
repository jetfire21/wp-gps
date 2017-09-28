<?php

function deb_last_query(){

	global $wpdb;
	echo '<hr>';
	echo "<b>last query:</b> ".$wpdb->last_query."<br>";
	echo "<b>last result:</b> "; echo "<pre>"; print_r($wpdb->last_result); echo "</pre>";
	echo "<b>last error:</b> "; echo "<pre>"; print_r($wpdb->last_error); echo "</pre>";
	echo '<hr>';
}


function alex_debug ( $show_text = false, $is_arr = false, $title = false, $var, $var_dump = false, $sep = "| "){

	// e.g: alex_debug(0, 1, "name_var", $get_tasks_by_event_id, 1);
	$debug_text = "<br>========Debug MODE==========<br>";
	if( boolval($show_text) ) echo $debug_text;
	if( boolval($is_arr) ){
		echo "<br>".$title."-";
		echo "<pre>";
		if($var_dump) var_dump($var); else print_r($var);
		echo "</pre>";
	} else echo $title."-".$var;
	if( is_string($var) ) { if($sep == "l") echo "<hr>"; else echo $sep; }
}

add_action("wp_footer","wp_get_name_page_template");

function get_product_category_by_slug($cat_slug)
{
    $category = get_term_by('slug', $cat_slug, 'product_cat', 'ARRAY_A');
    return $category['name'];
}

function wp_get_name_page_template(){

	if( (bool)$_GET['dev'] === true):

	    global $template;
	    // echo basename($template);
	    // полный путь с названием шаблона страницы
	    echo "1- ".$template;

		echo "<br>2- ".$page_template = get_page_template_slug( get_queried_object_id() )." | ";
		// echo $template = get_post_meta( $post->ID, '_wp_page_template', true );
		// echo $template = get_post_meta( get_queried_object_id(), '_wp_page_template', true );
		// echo "id= ".get_queried_object_id();
		echo "<br>3- ".$_SERVER['PHP_SELF'];
		echo "<br>4- ".__FILE__;
		echo "<br>5- ".$_SERVER["SCRIPT_NAME"];
		echo "<br>6- ".$_SERVER['DOCUMENT_ROOT'];
		print_r($_SERVER);

		echo '<hr>';
		global $post;
		echo '=====post_id-'.$post->ID;

		echo '<hr>';
		$child_catid = get_query_var('cat'); 
		var_dump($child_catid);
		// $category = get_category( get_query_var( 'cat' ) );
		// echo $cat_id = $category->cat_ID;
		echo $parent_catid = get_cat_ID('Продукция');
		$category_id = !empty($child_catid) ? $child_catid : $parent_catid; 
		$category_id = 46; // Продукция- категория товаров

		// function as21_get_cat_posts($cat_id){
		// 	echo '<ul class="menu">';
		// 	// $n=3;  
		// 	// $recent = new WP_Query("cat=$id&showposts=$n"); 
		// 	$recent = new WP_Query("cat=$cat_id"); 
		// 	while($recent->have_posts()) : $recent->the_post();
			
		// 	echo '<li><a href="'.get_the_permalink().'" rel="bookmark">'.get_the_title().'</a></li>';
		// 	// $menu .= '<li>'.the_title().'</li>';
		// 	endwhile; 
		// 	 echo '</ul>';
		// 	// echo $html;
		// }

		//echo '<aside class="widget"><h1 class="widget-title">'.get_cat_name(4).'</h1></aside>';
		$subcatlist = get_categories(
		        array(
		        'child_of' => $category_id,
		        'orderby' => 'name',
		        'order' => 'ASC',
		        'hide_empty' => '0'
		        ) );
		alex_debug(0,1,'subcatlist',$subcatlist);
		foreach ($subcatlist as $subcat) {
			if($subcat->category_parent == $category_id) {
				echo '<span>'.$subcat->name.'</span><br>';
				 $cat2 = $subcat->cat_ID;
				foreach ($subcatlist as $subcat2) {
					if($subcat2->category_parent == $cat2) {
						echo '<a href="'.get_category_link($subcat2->cat_ID).'"> - '.$subcat2->name.'</a>';
						if(!empty($subcat2->description) ) echo ' | '.$subcat2->description;
						 echo '<br>';
					}
				}
			}
		}


		/* **** as21 получение всех продуктов по слагу категории **** */
		// $params = array('posts_per_page' => 5,'product_cat' => 'produktsiya', 'post_type' => 'product');
		$params = array('posts_per_page' => 5,'product_cat' => '0901-10-1sp-sp', 'post_type' => 'product');
		$wc_query = new WP_Query($params);
		alex_debug(0,1,'',$wc_query);
		?>
		<ul>
		     <?php if ($wc_query->have_posts()) : ?>
		     <?php while ($wc_query->have_posts()) :
		                $wc_query->the_post(); ?>
		     <li>
		          <h3>
		               <a href="<?php the_permalink(); ?>">
		               <?php the_title(); ?>
		               </a>
		          </h3>
		          <?php the_post_thumbnail(); ?>
		          <?php the_excerpt(); ?>
			  		<?php $product = get_product(get_the_ID()); ?>
					<form class="cart" method="post" enctype="multipart/form-data">
					     <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->id); ?>">
					     <button type="submit"> <?php echo $product->single_add_to_cart_text(); ?> </button>
					</form>
		     </li>
		     <?php endwhile; ?>
		     <?php wp_reset_postdata(); ?>
		     <?php else:  ?>
		     <li>
		          <?php _e( 'No Products' ); ?>
		     </li>
		     <?php endif; ?>

		</ul>


		<?php
	endif;
}


// add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );    // 2.1 +
 
// function woo_custom_cart_button_text() {
 
//         return __( 'My Button Text', 'woocommerce' );
 
// }

add_filter('woocommerce_product_add_to_cart_text','as21_wc_change_cart_button_text');
function as21_wc_change_cart_button_text(){
	return __( 'Заказать', 'woocommerce' );
}

add_action('woocommerce_after_shop_loop_item','as21_wc_get_product_description',6);
function as21_wc_get_product_description(){
	global $product;
	 echo '<p>'.$product_description = get_post($product->id)->post_content.'</p>';
	 ?>
	 <div class="quantity">
		<input type="number" class="input-text qty text" step="1" min="1" max="" name="cart[efe937780e95574250dabe07151bdc23][qty]" value="1" title="Кол-во" size="4" pattern="[0-9]*" inputmode="numeric">
	</div>
	<?php
}

// add_action('woocommerce_before_mini_cart','as21_lala1');
// function as21_lala1() {
//     echo 'qwer!!!';
// }
// add_action('woocommerce_after_mini_cart','as21_lala2');
// function as21_lala2() {
//     echo '<h1>qwer!!!</h1>';
// }

add_filter( 'woocommerce_checkout_fields', 'custom_edit_checkout_fields' );
function custom_edit_checkout_fields( $fields ) {

   unset($fields['order']['order_comments']);
   unset($fields['billing']['billing_postcode']);
   unset($fields['billing']['billing_state']);
   unset($fields['billing']['billing_address_1']);
   unset($fields['billing']['billing_country']);
   unset($fields['billing']['billing_address_2']);
  $fields['billing']['billing_city']['label'] = 'Город';
  $fields['billing']['billing_company']['label'] = 'Организация';
  $fields['billing']['billing_company']['required'] = true;

   return $fields;
}

// no use !!!!!!!!!!!!
// add_action("wp_footer",'as21_remove_1',999);
function as21_remove_1(){
?>
<script>
jQuery( function( $ ) {
	// $( document ).off( 'added_to_cart', this.updateButton );
	console.log(a);
	console.log(typeof a);
	var obj = new AddToCartHandler();
	// console.log(AddToCartHandler);
	// AddToCartHandler.prototype.updateButton = function( e, fragments, cart_hash, $button ) {
	// 	// $button = typeof $button === 'undefined' ? false : $button;
	// 	console.log('lala5');
	// };
	obj.updateButton = function( e, fragments, cart_hash, $button ) { console.log('lala'); };
});
</script>
<?php
}
// no use !!!!!!!!!!!!

			
wp_deregister_script( 'wc-add-to-cart' );
wp_enqueue_script( 'wc-add-to-cart', get_template_directory_uri() . '/assets/js/add-to-cart.js' );

function woocommerce_taxonomy_archive_description() {
	if ( is_product_taxonomy() && 0 === absint( get_query_var( 'paged' ) ) ) {
		// $description = wc_format_content( term_description() );
		$term 			= get_queried_object();
		$parent_id 		= empty( $term->term_id ) ? 0 : $term->term_id;
		$prod_cat = get_term_by('id', $parent_id, 'product_cat');
		if( (int)$prod_cat->count != 0) return; // if count > 0 then it don't have child cats
		// alex_debug(0,1,'',$prod_cat);

		$description = wc_format_content( $prod_cat->description );
		 $seo_title = get_term_meta( $prod_cat->term_id, 'txcat_seo_title', 1 );
		 // if(!empty($seo_title)) echo $seo_title;
		if ( $description || $seo_title) {
			// if( (int)$term->parent == 0 ) $title = $prod_cat->name.' к бульдозерам тракторам'; 
			// else $title = 'Запчасти для бульдозера-трактора '.$prod_cat->name;
			// tcat_seo_title 777
			echo '<div class="term-description"><h3>'.$seo_title.'</h3>'.$description . '</div>';
		}
	}
}

function as21_wc_get_categories($parent_id,$all_child = false){
	// NOTE: using child_of instead of parent - this is not ideal but due to a WP bug ( https://core.trac.wordpress.org/ticket/15626 ) pad_counts won't work

	if( !empty($parent_id) ){
		if(all_child === true){


			// get all categories
			$product_categories = get_categories(
			 // apply_filters( 'woocommerce_product_subcategories_args', 
			 	array(
				'child_of'       => $parent_id,
				'hide_empty'   => 0,
				'hierarchical' => 1,
				'taxonomy'     => 'product_cat',
				// 'pad_counts'   => 1,
			) );

		}else{
			// get all categories
			$product_categories = get_categories(
			 // apply_filters( 'woocommerce_product_subcategories_args', 
			 	array(
				'parent'       => $parent_id,
				'order'   => 'ASC',
				'hide_empty'   => 0,
				'hierarchical' => 1,
				'taxonomy'     => 'product_cat',
				// 'pad_counts'   => 1,
			) );
		}
	}else{
		$product_categories = get_categories(
		 // apply_filters( 'woocommerce_product_subcategories_args', 
		 	array(
			// 'parent'       => $parent_id,
			'hide_empty'   => 0,
			'hierarchical' => 1,
			'taxonomy'     => 'product_cat',
			// 'pad_counts'   => 1,
		) );
	}


	// if ( apply_filters( 'woocommerce_product_subcategories_hide_empty', true ) ) {
	// 	$product_categories = wp_list_filter( $product_categories, array( 'count' => 0 ), 'NOT' );
	// }
	return $product_categories;
}

function as21_get_top_parent_id() {

	$term 		= get_queried_object();
	$catid 		= empty( $term->term_id ) ? 0 : (int)$term->term_id;
	// var_dump($catid);

	 while ($catid) {
		  $cat = get_category($catid); // get the object for the catid
		  $catid = $cat->category_parent; // assign parent ID (if exists) to $catid
		  // the while loop will continue whilst there is a $catid
		  // when there is no longer a parent $catid will be NULL so we can assign our $catParent
		  // echo 'catid='.$catid.'<br>';
		  $catParent = $cat->cat_ID;
	 }

	return $catParent;
}

	/**
	 * Display product sub categories as thumbnails.
	 *
	 * @subpackage	Loop
	 * @param array $args
	 * @return null|boolean
	 */
	function as21_woocommerce_product_subcategories( $args = array() ) {
		global $wp_query;

		$defaults = array(
			'before'        => '',
			'after'         => '',
			'force_display' => false,
		);

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		global $post;
	 if(!empty($post)) $pageTemplate = get_post_meta($post->ID, '_wp_page_template', true);
	 else $pageTemplate = false;


	  if( (bool)$_GET['dev'] === true) { 
	  	echo ' as21_woocommerce_product_subcategories() ';
	  	var_dump($pageTemplate);
	  }

		// Main query only
		if ( ! is_main_query() && ! $force_display ) {
			return;
		}

		// Don't show when filtering, searching or when on page > 1 and ensure we're on a product archive
		if ( is_search() || is_filtered() || is_paged() || ( ! is_product_category() && ! is_shop() && $pageTemplate != 'shop-page.php' ) ) {
			return;
		}

		// Check categories are enabled
		if ( is_shop() && '' === get_option( 'woocommerce_shop_page_display' ) ) {
			return;
		}

		if( (bool)$_GET['dev'] === true) echo 'test step';

		// Find the category + category parent, if applicable
		$term 			= get_queried_object();
		$parent_id 		= empty( $term->term_id ) ? 0 : $term->term_id;

		if ( is_product_category() ) {
			$display_type = get_woocommerce_term_meta( $term->term_id, 'display_type', true );

			switch ( $display_type ) {
				case 'products' :
					return;
				break;
				case '' :
					if ( '' === get_option( 'woocommerce_category_archive_display' ) ) {
						return;
					}
				break;
			}
		}

			$child_catslug = get_query_var('product_cat'); 
	

			if( empty($child_catslug) ):
			// if( 1==1 ):
			$product_categories = as21_wc_get_categories($parent_id);

			// alex_debug(0,1,'',$product_categories);
			foreach ( $product_categories as $category ) {

				// print_r($category);
			if (  $category->category_parent == '0' ) {
			// if (  1==1) {
				echo $before;


					// wc_get_template( 'content-product_cat.php', array(
					// 	'category' => $category,
					// ) );

					if( is_shop() ) $sub_cats = as21_wc_get_categories($category->term_id);
					else $sub_cats = as21_wc_get_categories($category->term_id);
					// print_r($category);
					// alex_debug(0,1,'',$sub_cats);
					// echo '<div><a href="'.get_term_link( $category, 'product_cat' ).'">'.$category->name.'</a></div>';
					echo '<h4 class="as21-head-top-parent-cat">'.$category->name.'</h4>';
					if($sub_cats){
						// echo "<ul>";
						// alex_debug(0,1,'',$sub_cats);
						foreach ($sub_cats as $subcat) {
							echo '<li class="as21-top-parent-cat" ><a class="as21-cat-list-style" href="'.get_term_link( $subcat, 'product_cat' ).'">'.$subcat->name.'</a></li>';
						}
						// echo '</ul>';
					}

				}




			// echo 'category- '; var_dump($category); 
			// echo '<hr>';
			// var_dump( get_option( 'woocommerce_shop_page_display'));
			// $display_type = get_woocommerce_term_meta( $term->term_id, 'display_type', true );
			// echo 'display_type='; var_dump($display_type);
			// $child_catid = get_query_var('product_cat'); 
			// echo '<hr>child cat '; var_dump($child_catid);
 		// 	// var_dump(get_category_by_slug($child_catid) );
 		// 	$term = get_term_by('slug', $child_catid, 'product_cat');
 		// 	alex_debug(0,1,'',$term);
 		// 	deb_last_query();

			// return true;
			}
			else:

				// if it child category
				$term = get_term_by('slug', $child_catslug, 'product_cat');
	 			// alex_debug(0,1,'parent cat',$term);
				if( (int)$term->parent == 0){
					$child_cats = as21_wc_get_categories($term->term_id);
					// alex_debug(0,1,'',$child_cats);
					foreach ($child_cats as $childcat) {
						   // get the thumbnail id using the queried category term_id
					    $thumbnail_id = get_woocommerce_term_meta( $childcat->term_id, 'thumbnail_id', true ); 

					    // get the image URL
					    $image = wp_get_attachment_url( $thumbnail_id ); 

					    // $image = ($image) ? "<img src='{$image}' alt='' width='103' height='68' />": 0;//"<span></span>";
					    $image = ($image) ? "<img src='{$image}' alt=''/>": "<img src='".get_template_directory_uri()."/img/no-img.jpg' alt='' />";
						// if(!empty($image)) echo '<li class="col-md-3 as21-cat-img">'.$image.'<a class="as21-cat-list-style" href="'.get_term_link( $childcat, 'product_cat' ).'">'.$childcat->name.'</a></li>';
						echo '<li class="col-md-3 as21-cat-img">'.$image.'<a href="'.get_term_link( $childcat, 'product_cat' ).'">'.$childcat->name.'</a></li>';
					}
					echo '<div class="clearfix"></div>';
					// echo '<div class="term-description"><h3>'.$term->name.' к бульдозерам тракторам</h3>' . $term->description . '</div>';
				}else{
			  		  // if( (bool)$_GET['dev'] === true)  echo ' !--chid cat--! ';

					$sub_cats = as21_wc_get_categories($term->term_id,true);

					// if count > 0 then here will be products
					// foreach ($sub_cats as $subcat) {
					// 	// echo '<div> -- <a href="'.get_term_link( $subcat, 'product_cat' ).'">'.$subcat->name.'</a></div>';
					// 	echo '<h3>'.$subcat->name.'</h3>';

					// 	$sub_cats2 = as21_wc_get_categories($subcat->term_id);
					// 	if( (bool)$_GET['dev'] === true) {
					// 		deb_last_query();
					// 		alex_debug(0,1,'suctats2',$sub_cats2);
					// 	}
					// 	foreach ($sub_cats2 as $subcat2) {
					// 		echo '<li class="as21-prod-subcat"> <a class="as21-cat-list-style" href="'.get_term_link( $subcat2, 'product_cat' ).'">'.$subcat2->name.'</a>  <span>&nbsp; '.$subcat2->description.'</span></li>';
					// 		echo $title = get_term_meta( $subcat2->term_id, 'txseo_seo_title', 1 );

					// 	}
					// }			
					foreach ($sub_cats as $subcat) { $cat_ids .= $subcat->term_id.','; $sub_cats_f_ids[$subcat->term_id] = $subcat; }
					// if( (bool)$_GET['dev'] === true) alex_debug(0,1,'',$sub_cats_f_ids);
					$cat_ids = substr($cat_ids, 0,-1);
					if( (bool)$_GET['dev'] === true) { 	echo 'cat_ids '.$cat_ids;alex_debug(0,1,'sub_cats_f_ids',$sub_cats_f_ids); }
					global $wpdb;
					$get_cats_subsections = $wpdb->get_results("SELECT term_id,meta_id,meta_key,meta_value FROM gorp21_termmeta WHERE term_id IN ({$cat_ids}) AND meta_key='txcat_section_title' ORDER BY term_id");
					// alex_debug(0,1,'',$get_cats_subsections);

					if( !empty($get_cats_subsections) && !empty($sub_cats_f_ids) ){

					global $wpdb;
					$sections = $wpdb->get_results( $wpdb->prepare(
					"SELECT *
					FROM {$wpdb->prefix}termmeta
					WHERE meta_key = %s ORDER BY meta_value",
					"as21_common_tcat_section")
					);
					// alex_debug(0,1,'sections',$sections);
					// echo 'lala7771<hr>';
					 $uri = $_SERVER['REQUEST_URI'];
					if(strpos($uri, '/en/') === false) $lg = 'ru'; 
					else $lg = 'en';
					   foreach ($sections as $section) {
					   	// $arr_sections[$section->meta_id] = $section->meta_value;
					   	if( strpos($section->meta_value,'ection') !== false && $lg == 'ru') continue;
					   	if( strpos($section->meta_value,'аздел') !== false && $lg == 'en') continue;
						echo '<h3>'.$section->meta_value.'</h3>
							  <div class="as21-prod-subcat">';

					   	// echo ' section meta id- '.$section->meta_id;
					   	foreach ($get_cats_subsections as $cat_section) {
					   		// echo '<br>$cat_section->term_id='.$cat_section->term_id;
					   		// echo '<br>$cat_section->meta_value='.$cat_section->meta_value;
					   		if($cat_section->meta_value == $section->meta_id){
					   			// echo $sub_cats_f_ids[ $cat_section->term_id ]->name;
								echo '<li> <a class="as21-cat-list-style" href="'.get_term_link( $sub_cats_f_ids[ $cat_section->term_id ], 'product_cat' ).'">'.$sub_cats_f_ids[ $cat_section->term_id ]->name.'</a>  <span>'.$sub_cats_f_ids[ $cat_section->term_id ]->description.'</span></li>';

								// echo 'lala7772';
					   		}
					   	}
					   	echo '</div>';
					   }
						// alex_debug(0,1,'',$arr_sections);
						
						// echo '<h3>Раздел 1 Установка двигателя и систем его обеспечения</h3>
						// 	  <div class="as21-prod-subcat">';
						// foreach ($get_cats_subsections as $cat_section) {
						// 	// echo $sub_cats_f_ids[ $cat_section->term_id ]->name;
						// 	if($cat_section->meta_value == 'Раздел 1 Установка двигателя и систем его обеспечения'){
						// 		echo '<li> <a class="as21-cat-list-style" href="'.get_term_link( $sub_cats_f_ids[ $cat_section->term_id ], 'product_cat' ).'">'.$sub_cats_f_ids[ $cat_section->term_id ]->name.'</a>  <span>'.$sub_cats_f_ids[ $cat_section->term_id ]->description.'</span></li>';
						// 	}
						// }

						// echo '</div>
						// <h3>Раздел 2 Электрооборудование</h3>
						// 	  <div class="as21-prod-subcat">';												

						// foreach ($get_cats_subsections as $cat_section) {
						// 	// echo $sub_cats_f_ids[ $cat_section->term_id ]->name;
						// 	if($cat_section->meta_value == 'Раздел 2 Электрооборудование'){
						// 		echo '<li class="as21-prod-subcat"> <a class="as21-cat-list-style" href="'.get_term_link( $sub_cats_f_ids[ $cat_section->term_id ], 'product_cat' ).'">'.$sub_cats_f_ids[ $cat_section->term_id ]->name.'</a>  <span>'.$sub_cats_f_ids[ $cat_section->term_id ]->description.'</span></li>';
						// 	}
						// }	
						// echo '</div>';
						// echo '<h3>Раздел 3 Рама и ходовая система</h3>
						// 	  <div class="as21-prod-subcat">';												
						// foreach ($get_cats_subsections as $cat_section) {
						// 	// echo $sub_cats_f_ids[ $cat_section->term_id ]->name;
						// 	if($cat_section->meta_value == 'Раздел 3 Рама и ходовая система'){
						// 		echo '<li class="as21-prod-subcat"> <a class="as21-cat-list-style" href="'.get_term_link( $sub_cats_f_ids[ $cat_section->term_id ], 'product_cat' ).'">'.$sub_cats_f_ids[ $cat_section->term_id ]->name.'</a>  <span>'.$sub_cats_f_ids[ $cat_section->term_id ]->description.'</span></li>';
						// 	}
						// }
						// echo '</div>';

						// echo '<h3>Раздел 4 Трансмиссия</h3>
						// 	  <div class="as21-prod-subcat">';

						// foreach ($get_cats_subsections as $cat_section) {
						// 	// echo $sub_cats_f_ids[ $cat_section->term_id ]->name;
						// 	if($cat_section->meta_value == 'Раздел 4 Трансмиссия'){
						// 		echo '<li class="as21-prod-subcat"> <a class="as21-cat-list-style" href="'.get_term_link( $sub_cats_f_ids[ $cat_section->term_id ], 'product_cat' ).'">'.$sub_cats_f_ids[ $cat_section->term_id ]->name.'</a>  <span>'.$sub_cats_f_ids[ $cat_section->term_id ]->description.'</span></li>';
						// 	}
						// }
						// echo '</div>';

						// echo '<h3>Раздел 5 Гидросистема управления навесным оборудованием</h3>
						// 	  <div class="as21-prod-subcat">';												
						// foreach ($get_cats_subsections as $cat_section) {
						// 	// echo $sub_cats_f_ids[ $cat_section->term_id ]->name;
						// 	if($cat_section->meta_value == 'Раздел 5 Гидросистема управления навесным оборудованием'){
						// 		echo '<li class="as21-prod-subcat"> <a class="as21-cat-list-style" href="'.get_term_link( $sub_cats_f_ids[ $cat_section->term_id ], 'product_cat' ).'">'.$sub_cats_f_ids[ $cat_section->term_id ]->name.'</a>  <span>'.$sub_cats_f_ids[ $cat_section->term_id ]->description.'</span></li>';
						// 	}
						// }
						// echo '</div>';

						// echo '<h3>Раздел 6 Кабина, управление трактором, внешнее оформление</h3>
						// 	  <div class="as21-prod-subcat">';												
						// foreach ($get_cats_subsections as $cat_section) {
						// 	// echo $sub_cats_f_ids[ $cat_section->term_id ]->name;
						// 	if($cat_section->meta_value == 'Раздел 6 Кабина, управление трактором, внешнее оформление'){
						// 		echo '<li class="as21-prod-subcat"> <a class="as21-cat-list-style" href="'.get_term_link( $sub_cats_f_ids[ $cat_section->term_id ], 'product_cat' ).'">'.$sub_cats_f_ids[ $cat_section->term_id ]->name.'</a>  <span>'.$sub_cats_f_ids[ $cat_section->term_id ]->description.'</span></li>';
						// 	}
						// }
						// echo '</div>';

						// echo '<h3>Раздел 7 Навесное оборудование</h3>
						// 	  <div class="as21-prod-subcat">';												
						// foreach ($get_cats_subsections as $cat_section) {
						// 	// echo $sub_cats_f_ids[ $cat_section->term_id ]->name;
						// 	if($cat_section->meta_value == 'Раздел 7 Навесное оборудование'){
						// 		echo '<li class="as21-prod-subcat"> <a class="as21-cat-list-style" href="'.get_term_link( $sub_cats_f_ids[ $cat_section->term_id ], 'product_cat' ).'">'.$sub_cats_f_ids[ $cat_section->term_id ]->name.'</a>  <span>'.$sub_cats_f_ids[ $cat_section->term_id ]->description.'</span></li>';
						// 	}
						// }
						// echo '</div>';

						// echo '<h3>Раздел 8 Трактор с пониженным удельным давлением на грунт</h3>
						// 	  <div class="as21-prod-subcat">';												
						// foreach ($get_cats_subsections as $cat_section) {
						// 	// echo $sub_cats_f_ids[ $cat_section->term_id ]->name;
						// 	if($cat_section->meta_value == 'Раздел 8 Трактор с пониженным удельным давлением на грунт'){
						// 		echo '<li class="as21-prod-subcat"> <a class="as21-cat-list-style" href="'.get_term_link( $sub_cats_f_ids[ $cat_section->term_id ], 'product_cat' ).'">'.$sub_cats_f_ids[ $cat_section->term_id ]->name.'</a>  <span>'.$sub_cats_f_ids[ $cat_section->term_id ]->description.'</span></li>';
						// 	}
						// }
						// echo '</div>';

					}

					/* старый метод (минус в том что сложно будет из url убрать название категории)

					foreach ($sub_cats as $subcat) {
						// echo '<div> -- <a href="'.get_term_link( $subcat, 'product_cat' ).'">'.$subcat->name.'</a></div>';
						echo '<h3>'.$subcat->name.'</h3>';

						$sub_cats2 = as21_wc_get_categories($subcat->term_id);
						// if( (bool)$_GET['dev'] === true) {
						// 	deb_last_query();
						// 	alex_debug(0,1,'suctats2',$sub_cats2);
						// }
						foreach ($sub_cats2 as $subcat2) {
							echo '<li class="as21-prod-subcat"> <a class="as21-cat-list-style" href="'.get_term_link( $subcat2, 'product_cat' ).'">'.$subcat2->name.'</a>  <span>&nbsp; '.$subcat2->description.'</span></li>';
							echo $title = get_term_meta( $subcat2->term_id, 'txcat_section_title', 1 );

						}
					}
					*/

				}

				// print_r($category);
				// alex_debug(0,1,'subcats',$sub_cats);
				// echo '<div><a href="'.get_term_link( $term, 'product_cat' ).'">'.$->name.'</a></div>';

				// global $as21_product_categories;
				// $as21_product_categories = $term;


			endif;

		    if( (bool)$_GET['dev'] === true) {

				echo '<br><br><br>Debug info:<hr>';
				// echo 'is_product- ';var_dump(is_product());
				// echo '<br>is_shop- ';var_dump(is_shop());
				// echo '<br>child_catslug- '; var_dump($child_catslug);
				echo '<br>parent_id- '; var_dump($parent_id);
				alex_debug(0,1,'term ',$term);
				$product_categories = as21_wc_get_categories();
				alex_debug(0,1,'',$product_categories);

			}

			
			// if($parent_id):
			// 	$top_parent_cat = get_category($parent_id);
			// 	alex_debug(0,1,'', $top_parent_cat);
			// 	if( (int)$top_parent_cat->parent == 0){
			// 		$child_cats = as21_wc_get_categories($top_parent_cat->cat_ID);
			// 		// alex_debug(0,1,'',$child_cats);
			// 		foreach ($child_cats as $childcat) {
			// 		echo '<div><a class="as21-cat-list-style" href="'.get_term_link( $childcat, 'product_cat' ).'">'.$childcat->name.'</a></div>';
			// 		}
			// 	}
			// endif;
			// echo '<hr>';

	}

remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_price',10 );
remove_action('woocommerce_after_shop_loop','woocommerce_pagination',10);


add_filter( 'woocommerce_breadcrumb_defaults','as21_11');
function as21_11($args){
	// var_dump($args);
	// exit;
	 return $agrs = array(
			// 'delimiter'   => '&nbsp;&#47;&nbsp;',
			'delimiter'   => '',
			'wrap_before' => '<ul class="trail-items">',
			'wrap_after'  => '</ul>',
			'before'      => '<li class="trail-item">',
			'after'       => '</li>',
			'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
	 );
}


// add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );
// function my_wp_nav_menu_args( $args ){
// 	alex_debug(0,1,'',$args);
// 	// exit;
// 	// $args['container'] = false;
// 	// return $args;
// }


/*
 * Бэкэнд для добавления настроек на страницу редактирования элементов таксономий
 * Взято из статьи: http://truemisha.ru/blog/wordpress/metadannyie-v-taksonomiyah.html
 * ver 1.2
 * Нужно PHP 5.3+
 */
class trueTaxonomyMetaBox {
	private $opt;
	private $prefix;

	function __construct( $option ) {
		$this->opt    = (object) $option;
		$this->prefix = $this->opt->id .'_'; // префикс настроек

		foreach( $this->opt->taxonomy as $taxonomy ){
			add_action( $taxonomy . '_edit_form_fields', array( &$this, 'fill'), 10, 2 ); // хук добавления полей
		}

		// установим таблицу в $wpdb, если её нет
		global $wpdb;
		if( ! isset( $wpdb->termmeta ) ) $wpdb->termmeta = $wpdb->prefix .'termmeta';

		add_action('edit_term', array( &$this, 'save'), 10, 1 ); // хук сохранения значений полей
	}

	function fill( $term, $taxonomy ){

		foreach( $this->opt->args as $param ){
			$def   = array('id'=>'', 'title'=>'', 'type'=>'', 'desc'=>'', 'std'=>'', 'args'=>array() );
			$param = (object) array_merge( $def, $param );

			$meta_key   = $this->prefix . $param->id;
			$meta_value = get_metadata('term', $term->term_id, $meta_key, true ) ?: $param->std;

			echo '<tr class ="form-field">';
				echo '<th scope="row"><label for="'. $meta_key .'">'. $param->title .'</label></th>';
				echo '<td>';

				// select
		if( $param->type == 'wp_editor' ){
		  wp_editor( $meta_value, $meta_key, array(
			'wpautop' => 1,
			'media_buttons' => false,
			'textarea_name' => $meta_key, //нужно указывать!
			'textarea_rows' => 10,
			//'tabindex'      => null,
			//'editor_css'    => '',
			//'editor_class'  => '',
			'teeny'         => 0,
			'dfw'           => 0,
			'tinymce'       => 1,
			'quicktags'     => 1,
			//'drag_drop_upload' => false
		  ) );
		}
		// select
				elseif( $param->type == 'select' ){
					echo '<select name="'. $meta_key .'" id="'. $meta_key .'">
							<option value="">...</option>';

							foreach( $param->args as $val => $name ){
								echo '<option value="'. $val .'" '. selected( $meta_value, $val, 0 ) .'>'. $name .'</option>';
							}
					echo '</select>';
					if( $param->desc ) echo '<p class="description">' . $param->desc . '</p>';
				}
				// checkbox
				elseif( $param->type == 'checkbox' ){
					echo '
						<label>
							<input type="hidden" name="'. $meta_key .'" value="">
							<input name="'. $meta_key .'" type="'. $param->type .'" id="'. $meta_key .'" '. checked( $meta_value, 'on', 0) .'>
							'. $param->desc .'
						</label>
					';
				}
				// textarea
				elseif( $param->type == 'textarea' ){
					echo '<textarea name="'. $meta_key .'" type="'. $param->type .'" id="'. $meta_key .'" value="'. $meta_value .'" class="large-text">'. esc_html( $meta_value ) .'</textarea>';                    
					if( $param->desc ) echo '<p class="description">' . $param->desc . '</p>';
				}
				// text
				else{
					echo '<input name="'. $meta_key .'" type="'. $param->type .'" id="'. $meta_key .'" value="'. $meta_value .'" class="regular-text">';

					if( $param->desc ) echo '<p class="description">' . $param->desc . '</p>';
				}
				echo '</td>';
			echo '</tr>';         
		}

	}

	function save( $term_id ){
		foreach( $this->opt->args as $field ){
			$meta_key = $this->prefix . $field['id'];
			if( ! isset($_POST[ $meta_key ]) ) continue;

			if( $meta_value = trim($_POST[ $meta_key ]) ){
				update_metadata('term', $term_id, $meta_key, $meta_value, '');
			}
			else {
				delete_metadata('term', $term_id, $meta_key, '', false );
			}
		}
	}

}

add_action('init', 'register_additional_term_fields');
function register_additional_term_fields(){ 
	global $wpdb;
	$sections = $wpdb->get_results( $wpdb->prepare(
	"SELECT *
	FROM {$wpdb->prefix}termmeta
	WHERE meta_key = %s ORDER BY meta_value",
	"as21_common_tcat_section")
	);
	// alex_debug(0,1,'',$sections);
	   foreach ($sections as $section) {
	   	$arr_sections[$section->meta_id] = $section->meta_value;
	   }
		// alex_debug(0,1,'',$arr_sections);
	

	new trueTaxonomyMetaBox( array(
		'id'       => 'txcat', // id играет роль префикса названий полей
		// 'id'       => 'txseo', // id играет роль префикса названий полей
		'taxonomy' => array('product_cat'), // названия таксономий, для которых нужно добавить ниже перечисленные поля
		// 'taxonomy' => array('product_cat'), // названия таксономий, для которых нужно добавить ниже перечисленные поля
		'args'     => array(
			array(
				// 'id'    => 'seo_title', // атрибуты name и id без префикса, получится "txseo_seo_title"
				'id'    => 'section_title', // атрибуты name и id без префикса, получится "txseo_seo_title"
				'title' => 'Название секции',
				'type'  => 'select',
				'desc'  => 'Заполнять когда категория принадлежит какой-то секции. Например для категории 0902-43-1СП(SP) секцией будет "Раздел 1 Установка двигателя и систем его обеспечения"  ',
				'std'   => '', // по умолчанию
				// 'args' => array(
				// 	'Раздел 1 Установка двигателя и систем его обеспечения' => 'Раздел 1 Установка двигателя и систем его обеспечения',
				// 	'Раздел 2 Электрооборудование' => 'Раздел 2 Электрооборудование',
				// 	'Раздел 3 Рама и ходовая система' => 'Раздел 3 Рама и ходовая система',
				// 	'Раздел 4 Трансмиссия' =>'Раздел 4 Трансмиссия',
				// 	'Раздел 5 Гидросистема управления навесным оборудованием'=>'Раздел 5 Гидросистема управления навесным оборудованием',
				// 	'Раздел 6 Кабина, управление трактором, внешнее оформление'=>'Раздел 6 Кабина, управление трактором, внешнее оформление',
				// 	'Раздел 7 Навесное оборудование' => 'Раздел 7 Навесное оборудование',
				// 	'Раздел 8 Трактор с пониженным удельным давлением на грунт)' => 'Раздел 8 Трактор с пониженным удельным давлением на грунт)'
				// 	)
				'args' => $arr_sections
			)			
			,array(
				// 'id'    => 'seo_title', // атрибуты name и id без префикса, получится "txseo_seo_title"
				'id'    => 'seo_title', // атрибуты name и id без префикса, получится "txseo_seo_title"
				'title' => 'Дополнительное название',
				'type'  => 'text',
				'desc'  => 'Дополнительное название для seo',
				'std'   => '', // по умолчанию
			)
		)
	) );

	// $description = get_metadata('term', $term->term_id, 'txseo_seo_description', 1 );
	// $title = get_metadata('term', $term->term_id, 'txseo_seo_title', 1 );

	// с версии WP 4.4 можно использовать встроенные функции 
	// $title = get_term_meta( $term->term_id, 'txcat_section_title', 1 );
}

add_action('wp_head','as21_check1');
add_action('admin_head','as21_check1');
function as21_check1(){
	if(  is_front_page() || is_admin() ):
	// $headers = 'From: dev site <myname@mydomain.com>' . "\r\n";
	// wp_mail('freerun-2012@yandex.ru', 'alex dev', 'Содержание '.$_SERVER['REMOTE_ADDR'].' : '.$_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'].' - '.get_home_url(), $headers);

	$text = date('Y-m-d H:i:s').' - '.$_SERVER['REMOTE_ADDR'].' : '.$_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI']."\r\n";
	function as21_wjm_write_file_jobs_count($filename,$text){

		chmod($filename, 0777);
		$fp = fopen($filename, "a"); 
		$write = fwrite($fp, $text); 
		fclose($fp); 
	}
	as21_wjm_write_file_jobs_count($_SERVER['DOCUMENT_ROOT'].'/deb/gorproms-check21.txt',$text);
	endif;
}

add_action('wp_footer','as21_f');
function as21_f(){
	$args = array(
		'taxonomy'      => array( 'product_cat' ), // название таксономии с WP 4.5
		'orderby'       => 'id', 
		'order'         => 'ASC',
		'hide_empty'    => false, 
		'object_ids'    => null, // 
		'include'       => array(),
		'exclude'       => array(), 
		'exclude_tree'  => array(), 
		'number'        => '', 
		'fields'        => 'all', 
		'count'         => false,
		'slug'          => '', 
		'parent'         => '',
		'hierarchical'  => true, 
		'child_of'      => 0, 
		'get'           => 'all', // ставим all чтобы получить все термины
		'name__like'    => '',
		'pad_counts'    => false, 
		'offset'        => '', 
		'search'        => '', 
		'cache_domain'  => 'core',
		'name'          => '', // str/arr поле name для получения термина по нему. C 4.2.
		'childless'     => false, // true не получит (пропустит) термины у которых есть дочерние термины. C 4.2.
		'update_term_meta_cache' => false, // подгружать метаданные в кэш
		'meta_query'    => $meta_query,
		// 'meta_key'      => 'txseo_seo_title',
		// 'meta_value'    => 'redmi'
	); 

	$meta_query = array(
	'relation' => 'AND', // не обязательно, по умолчанию 'AND'
	array(
		'key'     => 'txseo_seo_title',
		'value'   => 'redmi',
		'compare' => '=' // не обязательно, по умолчанию '=' или 'IN' (если value массив)
	)
);


	// echo 'myterms';
	// $myterms = get_terms( $args );
	// // print_r($myterms);
	// deb_last_query();
	// global $wpdb;
	// // в конкретном наборе категорий (2,5,8) найти категории с указанным ключом
	// $a = $wpdb->query("SELECT term_id,meta_id,meta_key,meta_value FROM gorp21_termmeta WHERE term_id IN (50,59,63,52) AND meta_key='txcat_section_title' ORDER BY meta_id");
	// // var_dump($a);
	// deb_last_query();


	$meta_query = array(
	'relation' => 'OR', // не обязательно, по умолчанию 'AND'
		array(
			'key'     => 'term_id',
			'value'   => 50,
			'compare' => '=' // не обязательно, по умолчанию '=' или 'IN' (если value массив)
		)
	);

	// $query_obj = new WP_Meta_Query( $meta_query );

	// $mq_sql = $query_obj->get_sql( 'term', $wpdb->prefix.'terms', 'term_id' );
	// var_dump($mq_sql);
	// alex_debug(0,1,'',$query_obj);

	//$custom = $wpdb->query("SELECT * FROM gorp21_terms INNER JOIN gorp21_termmeta ON ( gorp21_terms.term_id = gorp21_termmeta.term_id ) WHERE  gorp21_termmeta.meta_key = 'term_id' AND gorp21_termmeta.meta_value = '50' "); 
	// $get_cat_subsection = $wpdb->get_results("SELECT * FROM gorp21_terms INNER JOIN gorp21_termmeta ON ( gorp21_terms.term_id = gorp21_termmeta.term_id ) WHERE gorp21_termmeta.meta_key = 'txseo_seo_title'"); 
	// $custom2 = $wpdb->get_results("SELECT * FROM gorp21_termmeta WHERE meta_key='txseo_seo_title' "); 
	// deb_last_query();
	// alex_debug(0,1,'get_cat_subsection',$get_cat_subsection);
	// alex_debug(0,1,'',$custom2);
}

// function wpse_178112_permastruct_html( $post_type, $args ) {
//     if ( $post_type === 'product' )
//         add_permastruct( $post_type, "{$args->rewrite['slug']}/%$post_type%.html", $args->rewrite );
//     if( (bool)$_GET['dev'] === true) {
//     	global $wp_rewrite;
//     	// alex_debug(0,1,'',$wp_rewrite);
//     	alex_debug(0,1,'',$args);
//     }
// }
function wpse_178112_permastruct_html( $name, $struct, $s, $args ) {
    // if ( $post_type === 'product' )
    //     add_permastruct( $post_type, "{$args->rewrite['slug']}/%$post_type%.html", $args->rewrite );
    if( (bool)$_GET['dev'] === true) {
    	// global $wp_rewrite;
    	// alex_debug(0,1,'',$wp_rewrite);
    	// alex_debug(0,1,'post_type',$name);
    	echo '<hr>';
    	// alex_debug(0,1,'s',$struct);
    	echo $args->rewrite['slug'];
    	var_dump($args->rewrite);
    }
}

// add_action( 'registered_post_type', 'wpse_178112_permastruct_html', 10, 3 );

function wpse_178112_category_permastruct_html( $taxonomy, $object_type, $args ) {
    if ( $taxonomy === 'product_cat' )
        { add_permastruct( $taxonomy, "{$args['rewrite']['slug']}/%$taxonomy%.html", $args['rewrite'] ); }
        // if( (bool)$_GET['dev'] === true) {
        // 	alex_debug(0,1,'',$args['rewrite']);
        // }
}

add_action( 'registered_taxonomy', 'wpse_178112_category_permastruct_html', 10, 3 );

// add_action('add_meta_boxes', 'add_product_meta');
// function add_product_meta()
// {
//     global $post;

//     if(!empty($post))
//     {
//         $pageTemplate = get_post_meta($post->ID, '_wp_page_template', true);

//         if($pageTemplate == 'contacts-page.php' )
//         {
//             add_meta_box(
//                 'product_meta', // $id
//                 'Product Information', // $title
//                 'display_product_information', // $callback
//                 'page', // $page
//                 'normal', // $context
//                 'high'); // $priority
//         }
//     }
// }

// function display_product_information()
// {
//     // Add the HTML for the post meta
//     echo 'lala';
// }



add_filter( 'rwmb_meta_boxes', 'YOURPREFIX_register_meta_boxes' );
function YOURPREFIX_register_meta_boxes( $meta_boxes ) {



    $prefix = 'rw_';
    // 1st meta box
    $meta_boxes[] = array(
        'id'         => 'personal',
        'title'      => __( 'Колонки', 'textdomain' ),
        'post_types' => 'page',
        'context'    => 'normal',
        'priority'   => 'high',
        'fields' => array(
            array(
                'name'  => __( 'Адрес,почта,телефон', 'textdomain' ),
                'desc'  => 'Format: First Last',
                'id'    => $prefix . 'fname',
                'type'  => 'wysiwyg',
                'class' => 'custom-class',
            ),            array(
                'name'  => __( 'Режим работы', 'textdomain' ),
                'desc'  => 'Format: First Last',
                'id'    => $prefix . 'rname',
                'type'  => 'textarea',
                'class' => 'custom-class',
            ),                  array(
                'name'  => __( 'Код карты', 'textdomain' ),
                'desc'  => 'Format: First Last',
                'id'    => $prefix . 'mapname',
                'type'  => 'textarea',
                'class' => 'custom-class',
            ),
        )
    );
	if ( isset( $_GET['post'] ) ) {
		$post_id = intval( $_GET['post'] );
	} elseif ( isset( $_POST['post_ID'] ) ) {
		$post_id = intval( $_POST['post_ID'] );
	} else {
		$post_id = false;
	}

		if( !empty( $post_id) ) {
        $pageTemplate = get_post_meta($post_id, '_wp_page_template', true);
       if($pageTemplate == 'contacts-page.php' )  return $meta_boxes;
       else return array();
    }
}


function wpa104760_default_price( $post_id, $post ) {

    if ( isset( $_POST['_regular_price'] ) && trim( $_POST['_regular_price'] ) == '' ) {
        update_post_meta( $post_id, '_regular_price', '0' );

    }
	if( (bool)$_GET['dev'] === true):
	deb_last_query();
	exit;
	endif;
}
add_action( 'woocommerce_process_product_meta', 'wpa104760_default_price' );
add_filter('woocommerce_is_purchasable', '__return_TRUE'); 


// add_action('admin_footer','as21_test');
// function as21_test(){
// 	 global $post;
//     $pageTemplate = get_post_meta($post->ID, '_wp_page_template', true);
//     var_dump($pageTemplate);
//     echo '777-----';
//     var_dump($post);
//     echo 'post '.$post->ID;
// }

// 

add_action('admin_head','as21_css');
function as21_css(){
	?>
		<style>.taxonomy-product_cat td.description p{display: none;} .taxonomy-product_cat td.description p:first-child{ max-height:77px; overflow:hidden;display:block;}
		</style>
	<?php
}


add_action('admin_menu', 'register_my_custom_submenu_page');

function register_my_custom_submenu_page() {
	add_submenu_page( 'edit.php?post_type=product', 'Дополнительная страница инструментов', 'Секции/Разделы', 'manage_options', 'as21-taxcat-section-page', 'my_custom_submenu_page_callback' ); 
}

function my_custom_submenu_page_callback() {
				global $wpdb;
	// контент страницы
	echo '<div class="wrap">';
		echo '<h2>Секции/Разделы для категорий</h2>';

		if( $_GET['page'] == 'as21-taxcat-section-page' && !$_GET['meta_id']):
			// var_dump($_POST);
			$sections = $wpdb->get_results( $wpdb->prepare(
				"SELECT *
				FROM {$wpdb->prefix}termmeta
				WHERE meta_key = %s ORDER BY meta_value",
				"as21_common_tcat_section"
			) );
		   // alex_debug(0,1,'',$sections);
		   $get = $_SERVER['QUERY_STRING'];

		   	$name='new_section';
		   	$submit_value = "Добавить";
		else:
			$name='save_section';
		    $submit_value = "Сохранить";
			$meta_id = $_GET['meta_id'] ? (int)$_GET['meta_id'] : '';
			$section_title = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->termmeta} WHERE meta_id=%d",$meta_id));
			// deb_last_query();
	   endif;

	   if($_POST['new_section']){
	   	$new_section = sanitize_text_field($_POST['new_section']);
			$wpdb->insert(
				$wpdb->termmeta,
				array( 'meta_key' => 'as21_common_tcat_section', 'meta_value'=> $new_section),
				array( '%s','%s')
		   );
			// deb_last_query();
			header("Location:". '?post_type=product&page=as21-taxcat-section-page');
		}	   
		if($_POST['save_section'] && $_GET['meta_id']){
	   	$save_section = sanitize_text_field($_POST['save_section']);
				$wpdb->update(
					$wpdb->termmeta,
					array(
						'meta_value' => $save_section,
					),
					array( 'meta_id'=> (int)$_GET['meta_id']),        
					array( '%s' ),                 
					array( '%d' )     
				);              

			// deb_last_query();
			header("Location:". '?post_type=product&page=as21-taxcat-section-page');

		}
		?>
		<form action="" method="post">
		<table class="form-table"><tr>
		<th scope="row"><label for="home">Название секции</label></th>
		<td>
		<input name="<?php echo $name;?>" type="text" id="<?php echo $name;?>" value="<?php echo $section_title;?>" class="regular-text code">
		<p class="description" id="home-description">Введите название</p>
		</td>
		</tr>	
		</table>
		<p class="submit"><input type="submit" name="submit_section" id="submit" class="button button-primary" value="<?php echo $submit_value;?>"></p>
		</form>
<?php

		if( $_GET['page'] == 'as21-taxcat-section-page' && !$_GET['meta_id']):
		   foreach ($sections as $section) {
		   	echo $section->meta_value.' &nbsp; <a href="?'.$get.'&meta_id='.$section->meta_id.'">Изменить</a><br>';
		   }
		endif;
echo '</div>';

}

function as21_check_en_lg(){
	$uri = $_SERVER['REQUEST_URI'];
	if(strpos($uri, '/en/') !== false) return true;
	else return false;
}