<?php
/**
 * The template for displaying all pages
 * Template Name: Contacts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Canyon Themes
 * @subpackage Quality Construction
 */
get_header();
$quality_construction_breadcrump_option = quality_construction_get_option('quality_construction_breadcrumb_setting_option')?>
<section id="inner-title" class="inner-title"  <?php echo $header_style; ?>>
    <div class="container">
        <div class="row">
            <div class="col-md-8"><h2><?php the_title(); ?> </h2>
            </div>
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
<section id="section16" class="section16">
<div class="container">
        <div class="row">

               <div class="col-md-6 wow fadeInLeft" style="visibility: visible; animation-name: fadeInLeft;">
                 <?php if ( have_posts() ) : ?>
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php the_content();?>
                    <?php endwhile; // end of the loop. ?>
                <?php endif; // end of the loop. ?>
                </div>

            <div class="col-md-6 wow fadeInRight" style="visibility: visible; animation-name: fadeInRight;">
                <div class="row">
                      <div class="col-md-6">
                        <div class="address"> 
                        <h4>Адрес:</h4>
                         <?php echo rwmb_meta('rw_fname'); ?>
                     </div>
                     </div>
                    <div class="col-md-6">
                    <div class="address"> 
                    <h4>Рабочий режим:</h4>
                    <?php echo rwmb_meta('rw_rname'); ?>
                    </div>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="contacts-map"><?php echo rwmb_meta('rw_mapname'); ?></div>

    </div>
</div>
</section>
<?php
get_footer();
