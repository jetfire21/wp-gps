<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Canyon Themes
 * @subpackage Quality Construction
 */

$sidebar_design_layout = quality_construction_get_option( 'quality_construction_sidebar_layout_option' );

if( is_singular()){
    $single_design_layout = get_post_meta(get_the_ID(), 'quality_construction_sidebar_layout', true  );

    $sidebar_design_layout = $single_design_layout;
}
if ( ! is_active_sidebar( 'sidebar-1' ) || 'no-sidebar' == $sidebar_design_layout ) {
	return;
}
?>
<aside id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside><!-- #secondary -->

<?php

$top_catid = as21_get_top_parent_id();
if($top_catid > 0){
	$sub_cats = as21_wc_get_categories($top_catid);
	if($sub_cats):
		// alex_debug( 0,1,'',$sub_cats);
		echo '<aside class="widget-area widget sidebar-list-cats"><h2 class="widget-title">Каталог запчастей</h2>';
		$i = 1;
		foreach ($sub_cats as $subcat) {
			echo '<a href="'.get_term_link( $subcat, 'product_cat' ).'">'.$subcat->name.'</a>';
			// if($i%2==0) echo "<br>";
			$i++;
		}
		echo '</aside>';
	endif;
}
?>