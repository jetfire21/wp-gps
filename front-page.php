<?php
/**
 * The template for displaying all pages
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package Canyon Themes
 * @subpackage Quality Construction
 */
get_header();
$quality_construction_hide_front_page_content = quality_construction_get_option('quality_construction_front_page_hide_option');

/*show widget in front page, now user are not force to use front page*/
if (!is_home()) {
    do_action('quality_construction_home_page_section');
    dynamic_sidebar('quality-construction-home-page');
}

if ('posts' == get_option('show_on_front')) {

    include(get_home_template());
} else {
    if (1 != $quality_construction_hide_front_page_content) {
        include(get_page_template());
    }
}

/* **** as21  **** */
?>

<section id="section9" class="partner-wrapper section-margine no-m-top section-9-background">
                    <div class="container">
                        <div class="row">
                            <div id="partner" class="owl-carousel owl-loaded owl-drag">
  
                                            <div class="owl-stage-outer">
                                            <div class="owl-stage" style="transform: translate3d(-1170px, 0px, 0px); transition: 0.25s; width: 3510px;">

                                            <div class="owl-item cloned" style="width: 195px;">
                                            <div class="col-sm-12 col-md-12">
                                                <div class="owl-wrap">
                                                    <div class="feature-image">
                                                            <img src="<?php echo site_url(); ?>/wp-content/uploads/2017/09/logo_promtr.png" class="img-responsive wow tada" alt="" style="visibility: visible; animation-name: tada;">
                                                    </div>
                                                </div><!-- /.owl-wrap -->
                                            </div>
                                            </div>

                                            <div class="owl-item cloned" style="width: 195px;"><div class="col-sm-12 col-md-12">
                                                <div class="owl-wrap">
                                                    <div class="feature-image">
                                                            <img src="<?php echo site_url();?>/wp-content/uploads/2017/09/logo_cat.png" class="img-responsive wow tada" alt="" style="visibility: visible; animation-name: tada;">
                                                    </div>
                                                </div><!-- /.owl-wrap -->
                                            </div></div><div class="owl-item cloned" style="width: 195px;"><div class="col-sm-12 col-md-12">
                                                <div class="owl-wrap">
                                                    <div class="feature-image">
                                                            <img src="<?php echo site_url(); ?>/wp-content/uploads/2017/09/logo_tmz.png" class="img-responsive wow tada" alt="" style="visibility: visible; animation-name: tada;">
                                                    </div>
                                                </div><!-- /.owl-wrap -->
                                            </div></div><div class="owl-item cloned" style="width: 195px;"><div class="col-sm-12 col-md-12">
                                                <div class="owl-wrap">
                                                    <div class="feature-image">
                                                            <img src="<?php echo site_url(); ?>/wp-content/uploads/2017/09/logo_hunday.png" class="img-responsive wow tada" alt="" style="visibility: visible; animation-name: tada;">
                                                    </div>
                                                </div><!-- /.owl-wrap -->
                                            </div></div><div class="owl-item cloned" style="width: 195px;"><div class="col-sm-12 col-md-12">
                                                <div class="owl-wrap">
                                                    <div class="feature-image">
                                                            <img src="<?php echo site_url(); ?>/wp-content/uploads/2017/09/logo_cummin.png" class="img-responsive wow tada" alt="" style="visibility: visible; animation-name: tada;">
                                                    </div>
                                                </div><!-- /.owl-wrap -->
                                            </div></div><div class="owl-item cloned" style="width: 195px;"><div class="col-sm-12 col-md-12">
                                                <div class="owl-wrap">
                                                    <div class="feature-image">
                                                            <img src="<?php echo site_url(); ?>/wp-content/uploads/2017/09/logo_ymz.png" class="img-responsive wow tada" alt="" style="visibility: visible; animation-name: tada;">
                                                    </div>
                                                </div><!-- /.owl-wrap -->
                                            </div></div>


                      

                                            </div>


                                            </div><div class="owl-nav disabled"><div class="owl-prev">prev</div><div class="owl-next">next</div></div><div class="owl-dots disabled"></div>
                                </div><!-- /.owl-carousel -->
                        </div>
                    </div>
                </section>

<?php
get_footer();
