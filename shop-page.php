<?php
/**
 * The template for displaying all pages
 * Template Name: En-shop-page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Canyon Themes
 * @subpackage Quality Construction
 */
$quality_construction_breadcrump_option = quality_construction_get_option('quality_construction_breadcrumb_setting_option');
$quality_construction_designlayout = get_post_meta(get_the_ID(), 'quality_construction_sidebar_layout', true  );
$quality_construction_hide_breadcrump_option = quality_construction_get_option('quality_construction_hide_breadcrumb_front_page_option');
get_header(); 
if( ($quality_construction_hide_breadcrump_option== 1 && is_front_page()) || !is_front_page())
{
?>
    <section id="inner-title" class="inner-title" <?php echo $header_style; ?>>
        <div class="container">
            <div class="row">
                <div class="col-md-8"><h2><?php the_title(); ?></h2></div>
                <?php
                if ($quality_construction_breadcrump_option == "enable") {
                    ?>
                    <div class="col-md-4">
                        <div class="breadcrumbs">
                            <?php breadcrumb_trail(); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
 <?php } ?>   
    <section id="section16" class="section16">
        <div class="container">
            <div class="row">
                <div class="col-xs-<?php if ($quality_construction_designlayout == 'no-sidebar') {
                    echo "12";
                } else {
                    echo "12";
                } ?> col-sm-<?php if ($quality_construction_designlayout == 'no-sidebar') {
                    echo "12";
                } else {
                    echo "9";
                } ?> left-block">
                    <?php
                      as21_woocommerce_product_subcategories();
                          // global $post;
                        // if(!empty($post)) $pageTemplate = get_post_meta($post->ID, '_wp_page_template', true);
                   ?>

                </div><!-- div -->

                <?php  
                if( is_checkout()):

                    $product_categories = as21_wc_get_categories();
                    // print_r($product_categories);
                    if($product_categories):
                    ?>
                    <div class="col-xs-12 col-sm-3">
                            <?php
                            // alex_debug(0,1,'',$product_categories);
                            foreach ( $product_categories as $category ) {
                                // print_r($category);
                                 if (  $category->category_parent == '0' ) {
                                    // alex_debug(0,1,'',$sub_cats);
                                    echo '<div><a href="'.get_term_link( $category, 'product_cat' ).'">'.$category->name.'</a></div>';
                                }
                            }
                            ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($quality_construction_designlayout != 'no-sidebar') { ?>
                    <div class="col-xs-12 col-sm-3">
                        <?php get_sidebar(); ?>
                    </div>
                <?php } ?>
            </div><!-- div -->
        </div>
    </section>



<?php //echo '<h1>---777checkout----</h1>'; var_dump(is_checkout()); ?>

<?php get_footer();
