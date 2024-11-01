<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              feedbackautomatic.com
 * @since             1.0.1
 * @package           Starratings
 *
 * @wordpress-plugin
 * Plugin Name:       Star Ratings
 * Plugin URI:        https://feedbackautomatic.com/
 * Description:       This plugin allows you to pull reviews from locations such as Facebook and Yelp and present them on selected pages in JSON-LD standard format.
 * Version:           1.4
 * Author:            Feedback Automatic
 * Author URI:        https://feedbackautomatic.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       starratings
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'STARREVIEWS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-starreviews-activator.php
 */
function activate_starreviews() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-starreviews-activator.php';
    Starreviews_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-starreviews-deactivator.php
 */
function deactivate_starreviews() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-starreviews-deactivator.php';
    Starreviews_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_starreviews' );
register_deactivation_hook( __FILE__, 'deactivate_starreviews' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-starreviews.php';


/**  Build menu and Admin */
if ( is_admin() ){ // admin actions
    add_action( 'admin_menu', 'starreview_plugin_setup_menu' );
    add_action( 'admin_init', 'register_starreview_url' );
    // JS
    wp_register_script('prefix_bootstrap', plugins_url( 'includes/js/bootstrap.min.js', __FILE__ ));
    wp_enqueue_script('prefix_bootstrap');

    wp_enqueue_script('jquery');

    wp_register_script('prefix_popper', plugins_url( 'includes/js/popper.min.js', __FILE__ ));
    wp_enqueue_script('prefix_popper');

    // CSS
    wp_register_style('prefix_bootstrap_css', plugins_url( 'includes/css/bootstrap.min.css', __FILE__ ));
    wp_enqueue_style('prefix_bootstrap_css');

    wp_register_style('prefix_font-awesome_css', plugins_url( 'includes/css/font-awesome.min.css', __FILE__ ));
    wp_enqueue_style('prefix_font-awesome_css');

    //image upload logic
    add_action('admin_footer', function() {

        /*
        if possible try not to queue this all over the admin by adding your settings GET page val into next
        if( empty( $_GET['page'] ) || "my-settings-page" !== $_GET['page'] ) { return; }
        */

        ?>

        <script xmlns="http://www.w3.org/1999/html">
            jQuery(document).ready(function($){

                var custom_uploader
                    , click_elem = jQuery('.wpse-228085-upload')
                    , target = jQuery('.wrap input[name="logo"]')

                click_elem.click(function(e) {
                    e.preventDefault();
                    //If the uploader object has already been created, reopen the dialog
                    if (custom_uploader) {
                        custom_uploader.open();
                        return;
                    }
                    //Extend the wp.media object
                    custom_uploader = wp.media.frames.file_frame = wp.media({
                        title: 'Choose Image',
                        button: {
                            text: 'Choose Image'
                        },
                        multiple: false
                    });
                    //When a file is selected, grab the URL and set it as the text field's value
                    custom_uploader.on('select', function() {
                        attachment = custom_uploader.state().get('selection').first().toJSON();
                        target.val(attachment.url);
                    });
                    //Open the uploader dialog
                    custom_uploader.open();
                });
            });
        </script>

        <?php
    });
    add_action('admin_enqueue_scripts', function(){
        /*
        if possible try not to queue this all over the admin by adding your settings GET page val into next
        if( empty( $_GET['page'] ) || "my-settings-page" !== $_GET['page'] ) { return; }
        */
        wp_enqueue_media();
    });

} else {
    // non-admin enqueues, actions, and filters
    wp_register_style('prefix_font-awesome_css', plugins_url( 'includes/css/font-awesome.min.css', __FILE__ ));
    wp_enqueue_style('prefix_font-awesome_css');
}

function register_starreview_url() { // whitelist options
    #register_setting( 'starreview-url-settings', 'reviews_url' );
    #register_setting( 'review-pages', 'review_page');
    #register_setting( 'review-page-names', 'review_page_name');

    register_setting('starreview-options','yelp-url');
    register_setting('starreview-options','facebook-url');
    register_setting('starreview-options','display-page');
    register_setting('starreview-options','display-post');
    register_setting('starreview-settings','yelp-key');
    register_setting('starreview-options','site_img');
    register_setting('starreview-options','site_title');
    register_setting('starreview-options','site_phone');
    register_setting('starreview-options','site_streetaddr');
    register_setting('starreview-options','site_cityaddr');
    register_setting('starreview-options','site_stateaddr');
    register_setting('starreview-options','site_postaladdr');

}

function starreview_plugin_setup_menu(){
    // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    add_menu_page( 'starreview Plugin Page', 'Star Reviews', 'manage_options', 'starreview-plugin', 'starreview_content', 'dashicons-star-filled');
}


function starreview_content(){
    ?>
    <div class="wrap" style="margin: 5px 35px 10px 10px;">
        <div class="row" style="padding-bottom: 10px;">
            <div class="col-md-12">
                <h1>Star Ratings - Settings</h1>
            </div>
        </div>

        <div class="row">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Reviews</a>
                    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-yelpsettings" role="tab" aria-controls="nav-profile" aria-selected="false">Yelp Integration</a>
                    <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-about" role="tab" aria-controls="nav-contact" aria-selected="false">About</a>
                </div>
            </nav>
        </div>

        <div class="row" style="border-style: solid;border-width: 1px;padding: 20px;border-color: rgba(0, 0, 0, 0.20);">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <form method="post" action="options.php" enctype="multipart/form-data">
                        <?php settings_fields( 'starreview-options' ); ?>
                        <?php do_settings_sections( 'starreview-options' ); ?>
                        <?php

                        $site_title = get_option('site_title');
                        $site_phone = get_option('site_phone');
                        $site_streetaddr = get_option('site_streetaddr');
                        $site_cityaddr = get_option('site_cityaddr');
                        $site_stateaddr = get_option('site_stateaddr');
                        $site_postaladdr = get_option('site_postaladdr');
                        $site_img = get_option('site_img');

                        $yelpPlaceholder = "https://www.yelp.com/biz/the-freezer-tiki-bar-homosassa";
                        $fbPlaceholder = "https://www.facebook.com/titletap";

                        $yelpUrl = esc_attr(get_option('yelp-url'));
                        $fbUrl = esc_attr(get_option('facebook-url'));

                        $facebookRating = get_option( 'fb-rating');
                        $facebookReviewCount = get_option( 'fb-count');
                        $yelpRating = get_option( 'yelp-rating');
                        $yelpReviewCount = get_option( 'yelp-count');

                        if(empty($facebookReviewCount) && empty($yelpReviewCount)){
                            #echo "no facebook counts";
                            $facebookReviewCount = srfa_getReviewCount($fbUrl);
                            $facebookRating = srfa_getRating($fbUrl);
                            $yelpInfo = srfa_yelpInfo($yelpUrl);

                            $yelpRating = $yelpInfo['rating'];
                            $yelpReviewCount = $yelpInfo['reviews'];

                        }elseif(empty($yelpReviewCount)){
                            #echo "no yelp counts";
                        }

                        ?>
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label for="inputBusinessName">Business Name</label>
                                <input type="text" name="site_title" class="form-control" id="inputBusinessName" value="<?php echo $site_title; ?>" placeholder="<?php if(!empty($site_title)){echo $site_title;}else{echo "Good Review Co";}?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputBusinessPhone">Phone number</label>
                                <input type="text" name="site_phone" class="form-control" id="inputBusinessPhone" value="<?php echo $site_phone; ?>" placeholder="<?php if(!empty($site_phone)){echo $site_phone;}else{echo "800-232-4343";}?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputBusinessImage">Business Logo</label>
                                <button class="button wpse-228085-upload" type="file" name="site_img" /> Upload Logo</button>
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="inputBusinessStreet">Address</label>
                                <input type="text" name="site_streetaddr" class="form-control" id="inputBusinessStreet" value="<?php echo $site_streetaddr; ?>" placeholder="<?php if(!empty($site_streetaddr)){echo $site_streetaddr;}else{echo "1234 Main St.";}?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputBusinessCity">City</label>
                                <input type="text" name="site_cityaddr" class="form-control" id="inputBusinessCity" value="<?php echo $site_cityaddr; ?>" placeholder="<?php if(!empty($site_cityaddr)){echo $site_cityaddr;}else{echo "San Francisco";}?>">
                            </div>

                            <div class="form-group col-md-2">
                                <label for="inputBusinessState">State</label>
                                <input type="text" name="site_stateaddr" class="form-control" id="inputBusinessState" value="<?php echo $site_stateaddr; ?>" placeholder="<?php if(!empty($site_stateaddr)){echo $site_stateaddr;}else{echo "CA";}?>">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="inputBusinessPostal">Zip Code</label>
                                <input type="text" name="site_postaladdr" class="form-control" id="inputBusinessPostal" value="<?php echo $site_postaladdr; ?>" placeholder="<?php if(!empty($site_postaladdr)){echo $site_postaladdr;}else{echo "90001";}?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputYelpURL">Yelp URL</label>
                                <input type="text" name="yelp-url" class="form-control" id="inputYelpURL" value="<?php echo $yelpUrl; ?>" placeholder="<?php if(!empty($yelpURL)){echo $yelpUrl;}else{echo $yelpPlaceholder;}?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputFacebookURL">Facebook URL</label>
                                <input type="text" name="facebook-url" class="form-control" id="inputFacebookURL" value="<?php echo $fbUrl; ?>" placeholder="<?php if(!empty($fbURL)){echo $fbUrl;}else{echo $fbPlaceholder;}?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="YelpStars">Yelp Reviews:</label>
                                <p>
                                    <?php
                                    if(!empty($yelpRating)){
                                        echo "$yelpRating star rating! ($yelpReviewCount reviews) ";
                                    }else{
                                            echo "Enter a Yelp url to see reviews";
                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="FacebookReviews">Facebook Reviews:</label>
                                <p><?php if(!empty($facebookRating)){ echo "$facebookRating star rating! ($facebookReviewCount reviews) ";}?></p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="pageselect">Choose pages to display reviews:</label>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="postselect">Choose posts to display reviews:</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <select class="custom-select" id="pageselect" name="display-page" multiple>
                                    <?php
                                    $pages = get_pages();
                                    $displayPageID = get_option('display-page');
                                    $displayPageName = "";

                                    foreach($pages as $key => $page){
                                        $pageID = $page->ID;
                                        $pageName = $page->post_title;

                                        if($displayPageID == $pageID){
                                            #current page name
                                            $displayPageName = $page->post_title;
                                            echo '<option value="'.$pageID.'" selected>'.$pageName.'</option>';
                                        }else{
                                            echo '<option value="'.$pageID.'">'.$pageName.'</option>';
                                        }
                                    }
                                    ?>
                                </select><br>
                                <span class="text-muted">(CTRL/CMD + Click to select multiple )</span>
                            </div>

                            <div class="form-group col-md-6">
                                <select class="custom-select" id="postselect" name="display-post" multiple>
                                    <?php
                                    $posts = get_posts();
                                    $displayPostID = get_option('display-post');
                                    $displayPostName = "";

                                    foreach($posts as $key => $post){
                                        $postID = $post->ID;
                                        $postName = $post->post_title;

                                        if($displayPostID == $postID){
                                            #current post name
                                            $displayPostName = $post->post_title;
                                            echo '<option value="'.$postID.'" selected>'.$postName.'</option>';
                                        }else{
                                            echo '<option value="'.$postID.'">'.$postName.'</option>';
                                        }
                                    }
                                    ?>
                                </select><br>
                                <span class="text-muted">(CTRL/CMD + Click to select multiple )</span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="googlepreview">Google Result Preview:</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12" style="border-style: solid;border-width: thin; padding-top: 1rem; padding-bottom: 1rem;">
                        <span style="min-width: 40em; height: 10em; background-color: white;">
                            <span style="width: 400px;">
                                <div>
                                    <a href="" style="color: #1a0dab; font-size: 20px;"><?php echo $displayPageName;?></a><br>
                                    <span style="color: #006621; margin-top: -4px; margin-left: 3px; left: 0;"><?php echo get_permalink($displayPageID); ?></span><br>
                                    <span>This is a preview of what your reviews will look like in your Google search results.</span><br>

                                        <?php if(!empty($yelpRating)&!empty($facebookRating)) {
                                            $aggRating = ($yelpRating + $facebookRating)/2;


                                            # Rating Stars Logic

                                            if($aggRating == 5 || $aggRating >= 4.6){
                                                # 5.0 Stars
                                        ?>

                                            <span><p>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>

                                        <?php
                                            }elseif($aggRating == 4.5 || $aggRating >= 4.1){
                                                # 4.5 Stars
                                        ?>

                                            <span><p>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-full"></i>

                                        <?php

                                            }elseif($aggRating == 4.0 || $aggRating >= 3.6){
                                                # 4.0 Stars
                                        ?>

                                            <span><p>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-o"></i>

                                        <?php

                                            }elseif($aggRating == 3.5 || $aggRating >= 3.1){
                                                # 3.5 Stars
                                        ?>

                                            <span><p>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-full"></i>
                                            <i class="fa fa-star-o"></i>

                                        <?php

                                            }elseif($aggRating == 3.0 || $aggRating >= 2.6){
                                                # 3.0 Stars
                                        ?>

                                            <span><p>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>

                                        <?php

                                            }elseif($aggRating == 2.5 || $aggRating >= 2.1){
                                                # 2.5 Stars
                                        ?>

                                            <span><p>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-full"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>

                                        <?php

                                            }elseif($aggRating == 2.0 || $aggRating >= 1.6){
                                                # 2.0 Stars
                                            ?>

                                            <span><p>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>

                                        <?php

                                            }elseif($aggRating == 1.5 || $aggRating >= 1.1){
                                                # 1.5 Stars
                                        ?>

                                            <span><p>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-full"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>

                                        <?php

                                            }elseif($aggRating == 1.0 || $aggRating <= 1.0){
                                        # 1.0 Stars
                                        ?>

                                            <span><p>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>

                                        <?php
                                        }else{
                                                #not needed displaying 1 star as minimum
                                        }
                                            $aggCount = $facebookReviewCount + $yelpReviewCount;
                                            echo "Rating: $aggRating - $aggCount reviews";
                                        }
                                        ?>
                                            </p>
                            </span>
                        </span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <?php submit_button(); ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-yelpsettings" role="tabpanel" aria-labelledby="nav-profile-tab">
                <form method="post" action="options.php">
                    <?php settings_fields( 'starreview-settings' ); ?>
                    <?php do_settings_sections( 'starreview-settings' ); ?>
                    <?php
                    $yelpkey = get_option('yelp-key');
                    ?>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputYelpKey">Yelp Api Key</label>
                            <input type="text" name="yelp-key" class="form-control" id="inputYelpKey" value="<?php echo $yelpkey; ?>" placeholder="<?php if(!empty($yelpkey)){echo $yelpkey;}else{echo "";}?>">
                            <br>
                            <span class="text-muted"> For more information on how to acquire yelp api key visit <a href="https://www.yelp.com/developers/faq">here.</a></span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <?php submit_button(); ?>
                        </div>
                    </div>
                </form>
            </div>

            <div class="tab-pane fade" id="nav-about" role="tabpanel" aria-labelledby="nav-contact-tab">
                <h7>About</h7>
                <p>Star Reviews plugin was created to provide an easy way to display reviews on your website as well as to search engines (in a way they will understand!).</p>
            </div>
        </div>
    </div>
    </div>
    </div>
<?php }


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function srfa_getRating($url){
    $request = wp_safe_remote_get($url);
    $html = wp_remote_retrieve_body($request);
    $dom  = new DOMDocument();
    libxml_use_internal_errors( 1 );
    $dom->loadHTML( $html );
    $xpath = new DOMXpath( $dom );

    $jsonScripts = $xpath->query( '//script[@type="application/ld+json"]' );
    $json = trim( $jsonScripts->item(0)->nodeValue );
    $data = json_decode($json, true);

    $ratingValue = $data['aggregateRating']['ratingValue'];
    return $ratingValue;
}

function srfa_getReviewCount($url){
    $request = wp_safe_remote_get($url);
    $html = wp_remote_retrieve_body($request);

    $dom  = new DOMDocument();
    libxml_use_internal_errors( 1 );
    $dom->loadHTML( $html );
    $xpath = new DOMXpath( $dom );
    $jsonScripts = $xpath->query( '//script[@type="application/ld+json"]' );
    $json = trim( $jsonScripts->item(0)->nodeValue );
    $data = json_decode($json, true);

    $reviewCount = $data['aggregateRating']['ratingCount'];
    return $reviewCount;
}

function srfa_yelpInfo($url){
    #echo "running yelp info function!! <br>";

    $yelpkey = get_option('yelp-key');

    if(!empty($yelpkey)){
        // Convert Yelp Biz URI to API endpoint
        if (strpos($url, 'yelp.com/biz/') !== false) {
            // your code goes here
            $arr = explode('yelp.com/biz/', $url);
            $yelpID = $arr[1];
            $yelpAPI = "https://api.yelp.com/v3/businesses/".$yelpID;
        }

        #$request = wp_remote_request( $yelpAPI,
        #   array( 'headers' => array( 'Authorization' => 'Bearer -zrUWOjK8NaiTYzbag_-9gloPoLraiN-HV0DTXs06YpFzWH_hxqgPE09zbC-GN0VOfWLtrfa6tx4uIUU6ILC6cvQ5ituSth0L6pUO4RqCkIIXnSZvFksryfRSj6cXXYx')
        #  ));
        #var_dump($request);
        #$html = wp_remote_retrieve_body($request);

        // Leveraging cUrl instead of wp http api due to yelp restrictions: https://github.com/Yelp/yelp-fusion/issues/400
        $curl = curl_init();

        $header = array();
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json';
        $header[] = "Authorization: Bearer $yelpkey";

        // Set some options
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $yelpAPI,
            CURLOPT_HTTPHEADER => $header
        ]);
        // Send the request & save response to $html
        $response = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        $html = json_decode($response, true);

        $yelpData['rating'] = $html['rating'];
        $yelpData['reviews'] = $html['review_count'];

        return $yelpData;
    }else{
        return "Error: No valid Yelp Api Key Found";
    }
}
function run_starreviewcore() {
    $site_title = get_option('site_title');
    $site_phone = get_option('site_phone');
    $site_streetaddr = get_option('site_streetaddr');
    $site_cityaddr = get_option('site_cityaddr');
    $site_stateaddr = get_option('site_stateaddr');
    $site_postaladdr = get_option('site_postaladdr');
    $site_img = get_option('site_img');
    $facebookUrl = get_option( 'facebook-url' );
    $yelpUrl = get_option( 'yelp-url' );

    if(!$site_img){
        $site_img = "https://www.titletap.com/wp-content/uploads/2020/01/TitleTapLogo-SM-Black.png";
    }


    srfa_yelpInfo($yelpUrl);

    if(!empty($facebookUrl) && !empty($yelpUrl)){
        //if both facebook and yelp url's have been set
        #echo "yelp and facebook are not empty!!";
        $facebookRating = srfa_getRating($facebookUrl);
        $facebookReviewCount = srfa_getReviewCount($facebookUrl);

        $yelpData = srfa_yelpInfo($yelpUrl);
        #var_dump($yelpData);
        $yelpRating = $yelpData['rating'];
        $yelpReviewCount = $yelpData['reviews'];

        //verify option doesn't exist before adding it
        if(FALSE === get_option('fb-rating') && FALSE === update_option('fb-rating',FALSE)){
            add_option('fb-rating',$facebookRating);
        }else{
            update_option('fb-rating',$facebookRating);
        }

        if(FALSE === get_option('fb-count') && FALSE === update_option('fb-count',FALSE)){
            add_option('fb-count',$facebookReviewCount);
        }else{
            update_option('fb-count',$facebookReviewCount);
        }

        if(FALSE === get_option('yelp-rating') && FALSE === update_option('yelp-rating',FALSE)){
            add_option('yelp-rating',$yelpRating);
        }else{
            update_option('yelp-rating',$yelpRating);
        }

        if(FALSE === get_option('yelp-count') && FALSE === update_option('yelp-count',FALSE)){
            add_option('yelp-count',$yelpReviewCount);
        }else{
            update_option('yelp-count',$yelpReviewCount);
        }

        //aggregate both ratings (via average) and review count

        $arrRatings = [$facebookRating, $yelpRating];
        $ratingAvg = array_sum($arrRatings)/count($arrRatings);

        $reviewSum = ($facebookReviewCount + $yelpReviewCount);

        echo '<script type="application/ld+json">
          {
            "@context": "https://schema.org/",
            "@type": "AggregateRating",
            "itemReviewed": {
                "@type": "LocalBusiness",
                "image": "'.$site_img.'",
                "name": "'.$site_title.'",
                "telephone": "'.$site_phone.'",
                "address" : {
                    "@type": "PostalAddress",
                    "streetAddress": "'.$site_streetaddr.'",
                    "addressLocality": "'.$site_cityaddr.'",
                    "addressRegion": "'.$site_stateaddr.'",
                    "postalCode": "'.$site_postaladdr.'",
                    "addressCountry": "US"
                    }
            },
            "ratingValue": "'.$ratingAvg.'",
            "bestRating": "100",
            "ratingCount": "'.$reviewSum.'"
          }
         </script>';
    }elseif(!empty($facebookUrl) && empty($yelpUrl)){
        //if facebook url is set but not yelp
        $facebookRating = srfa_getRating($facebookUrl);
        $facebookReviewCount = srfa_getReviewCount($facebookUrl);

        //verify option doesn't exist before adding it
        if(FALSE === get_option('fb-rating') && FALSE === update_option('fb-rating',FALSE)){
            add_option('fb-rating',$facebookRating);
        }else{
            update_option('fb-rating',$facebookRating);
        }

        if(FALSE === get_option('fb-count') && FALSE === update_option('fb-count',FALSE)){
            add_option('yelp-count',$facebookReviewCount);
        }else{
            update_option('yelp-count',$facebookReviewCount);
        }

        echo '<script type="application/ld+json">
          {
            "@context": "https://schema.org/",
            "@type": "AggregateRating",
            "itemReviewed": {
                "@type": "LocalBusiness",
                "image": "'.$site_img.'",
                "name": "'.$site_title.'",
                "telephone": "'.$site_phone.'",
                "address" : {
                    "@type": "PostalAddress",
                    "streetAddress": "'.$site_streetaddr.'",
                    "addressLocality": "'.$site_cityaddr.'",
                    "addressRegion": "'.$site_stateaddr.'",
                    "postalCode": "'.$site_postaladdr.'",
                    "addressCountry": "US"
                    }
                },
            "ratingValue": "'.$facebookRating.'",
            "bestRating": "100",
            "ratingCount": "'.$facebookReviewCount.'"
          }
         </script>';
    }elseif(!empty($yelpUrl) && empty($facebookUrl)){
        //if yelp url is set but not facebook
        $yelpRating = srfa_getRating($yelpUrl);
        $yelpReviewCount = srfa_getReviewCount($yelpUrl);

        //verify option doesn't exist before adding it
        if(FALSE === get_option('yelp-rating') && FALSE === update_option('yelp-rating',FALSE)){
            add_option('yelp-rating',$yelpRating);
        }else{
            update_option('yelp-rating',$yelpRating);
        }

        if(FALSE === get_option('yelp-count') && FALSE === update_option('yelp-count',FALSE)){
            add_option('yelp-count',$yelpReviewCount);
        }else{
            update_option('yelp-count',$yelpReviewCount);
        }

        echo '<script type="application/ld+json">
        {
            "@context": "https://schema.org/",
            "@type": "AggregateRating",
            "itemReviewed": {
                "@type": "LocalBusiness",
                "image": "'.$site_img.'",
                "name": "'.$site_title.'",
                "telephone": "'.$site_phone.'",
                "address" : {
                    "@type": "PostalAddress",
                    "streetAddress": "'.$site_streetaddr.'",
                    "addressLocality": "'.$site_cityaddr.'",
                    "addressRegion": "'.$site_stateaddr.'",
                    "postalCode": "'.$site_postaladdr.'",
                    "addressCountry": "US"
                    }
                },
            "ratingValue": "'.$yelpRating.'",
            "bestRating": "100",
            "ratingCount": "'.$yelpReviewCount.'"
          }
         </script>';
    }elseif(empty($facebookUrl) && empty($yelpUrl)){
        //No input
    }
}

function srfa_getYelpWidget(){
    $yelpUrl = get_option( 'yelp-url' );
    $yelpRating = get_option('yelp-rating');
    $yelpCount = get_option('yelp-count');

    if(($yelpRating <= 5) && ($yelpRating >= 4.5)) {
        $yelpStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>';
    } elseif (($yelpRating <= 4.4) && ($yelpRating >= 4)) {
        $yelpStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star-half"></i>';
    } elseif (($yelpRating <= 3.9) && ($yelpRating > 3.5)) {
        $yelpStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star-half"></i>';
    } elseif (($yelpRating <= 3.4) && ($yelpRating > 2)) {
        $yelpStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star-half"></i>';
    } elseif (($yelpRating <= 1.9) && ($yelpRating > 1.1)) {
        $yelpStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>';
    } elseif (($yelpRating <= 1.0) && ($yelpRating > 0.1)) {
        $yelpStars = '<i class="fa fa-star"></i>';
    } else {
        $yelpStars = 'No Rating';
    }

    $yelpImage =  plugin_dir_url( __FILE__ ) . 'includes/images/yelp.png';
    $widget = '
    <div class="row" style="display: inline-flex;width: 28rem;border-top: #bf241a;border-top-width: .5rem;border-left-width: 0.06rem;border-right-width: 0.06rem;border-bottom-width: 0.06rem;border-style: solid;border-radius: 5px;font-size: .85rem;font-family: Helvetica Neue;font-weight: bold;color: grey;box-shadow: 0 2px 5px 0 rgba(0,0,0,.25)!important;height: 110px;margin-bottom: 0%; background-color: white;">
    <a href="'.$yelpUrl.'" style="text-decoration: none;min-height: 110px;min-width: 330px;position: absolute;z-index: 1;"></a>

    <div class="col" style="width: 20%;padding-left: .5rem;padding-top: .95rem;padding-bottom: .5rem;">
        <img src="'.$yelpImage.'" style="border-radius: 25%;height: 45px; width: 45px;">
    </div>

    <div class="col" style="padding-top: 25px;padding-left: 15px;margin-top: -2%;width: 70%;line-height: .95rem;">

        <span style="margin-top: 6%;padding-bottom: -26px;">Yelp rating</span><br>
            
        <div class="col" style="max-width: 20%;white-space: nowrap;overflow: hidden;float: left;padding-right: 3px;font-weight: bold;color: #000000;">
             '.$yelpStars.'
        </div>
<br>
        <div class="col" style="max-width: 200px; overflow: hidden; font-size: .75rem; color: #bf241a;">
           Rating: '.$yelpRating.'
           
           <span style="font-size: 0.65rem;color: #6d6d6d8f;">(14 reviews) </span> 
        </div>
        
        
    </div>
</div>
    ';
    return $widget;
}

function srfa_getFacebookWidget(){
    $facebookUrl = get_option( 'facebook-url' );
    $fbRating = get_option('fb-rating');
    $fbCount = get_option('fb-count');

    if(($fbRating <= 5) && ($fbRating >= 4.5)) {
        $fbStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>';
    } elseif (($fbRating <= 4.4) && ($fbRating > 4.0)) {
        $fbStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star-half"></i>';
    } elseif (($fbRating <= 3.9) && ($fbRating > 3.5)) {
        $fbStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star-half"></i>';
    } elseif (($fbRating <= 3.4) && ($fbRating > 2)) {
        $fbStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star-half"></i>';
    } elseif (($fbRating <= 1.9) && ($fbRating > 1.1)) {
        $fbStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>';
    } elseif (($fbRating <= 1.0) && ($fbRating > 0.1)) {
        $fbStars = '<i class="fa fa-star"></i>';
    } else {
        $fbStars = 'No Rating';
    }
    $fbImage = plugin_dir_url( __FILE__ ) . 'includes/images/fb.png';
    $widget = '<div class="row" style="width: 28rem;border-top: #1876f2;border-top-width: .5rem;border-left-width: 0.06rem;border-right-width: 0.06rem;border-bottom-width: 0.06rem;border-style: solid;border-radius: 5px;font-size: .85rem;font-family: Helvetica neue;font-weight: bold;color: grey;box-shadow: 0 2px 5px 0 rgba(0,0,0,.25)!important;/* height: 110px; */">
      <a href="'.$fbImage.'" style="text-decoration: none;min-height: 110px;min-width: 330px;position: absolute;z-index: 1;"></a>
      <div class="row" style="padding-left: 1.25rem;padding-top: 1.25rem;padding-bottom: .5rem;height: 100px;max-height: 100px;background-color: white;">
          <div class="col-md-2">
            <img src="'.$fbImage.'" style="border-radius: 25%;height: 50px;width: 50px;float: left;margin-top: 5px;margin-right: 20px;">

            <div class="col-md-8" style="max-width: 200px;overflow: hidden;font-size: .75rem;color: #1876f2;">
              <span style="padding-top: 40px;">Facebook rating</span><br>
            </div>
          </div>
     
          <div class="col" style="/* padding-top: 25px; *//* padding-left: 15px; *//* margin-top: -2%; *//* width: 70%; */line-height: .95rem;/* position: absolute; */">
            '.$fbStars.'<br>
            <div class="col" style="max-width: 20%; white-space: nowrap; overflow: hidden; float: left; padding-right: 3px; font-weight: bold; color: #000000;">
              Rating: '.$fbRating.'
            </div>
            <span style="font-size: 0.65rem;color: #6d6d6d8f;">('.$fbCount.' reviews) </span>
          </div>
    </div>   
</div>';
    return $widget;
}

function srfa_getavgWidget(){
    $yelpRating = get_option('yelp-rating');
    $yelpCount = get_option('yelp-count');
    $fbRating = get_option('fb-rating');
    $fbCount = get_option('fb-count');


    //aggregate both ratings (via average) and review count
    $arrRatings = [$fbRating, $yelpRating];
    $ratingAvg = array_sum($arrRatings)/count($arrRatings);
    $reviewSum = ($fbCount + $yelpCount);


    if(($ratingAvg <= 5) && ($ratingAvg >= 4.5)) {
        $avgStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>';
    } elseif (($ratingAvg <= 4.4) && ($ratingAvg >= 4)) {
        $avgStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star-half"></i>';
    } elseif (($ratingAvg <= 3.9) && ($ratingAvg > 3.5)) {
        $avgStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star-half"></i>';
    } elseif (($ratingAvg <= 3.4) && ($ratingAvg > 2)) {
        $avgStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star-half"></i>';
    } elseif (($ratingAvg <= 1.9) && ($ratingAvg > 1.1)) {
        $avgStars = '<i class="fa fa-star"></i>
        <i class="fa fa-star"></i>';
    } elseif (($ratingAvg <= 1.0) && ($ratingAvg > 0.1)) {
        $avgStars = '<i class="fa fa-star"></i>';
    } else {
        $avgStars = 'No Rating';
    }

    $fbImage = plugin_dir_url( __FILE__ ) . 'includes/images/fb.png';
    $widget = '
    <div class="row" style="display: inline-flex;width: 28rem;border-top: rgba(218, 183, 0, 0.65);border-top-width: .5rem;border-left-width: 0.06rem;border-right-width: 0.06rem;border-bottom-width: 0.06rem;border-style: solid;border-radius: 5px;font-size: .85rem;font-family: Helvetica neue;font-weight: bold;color: grey;box-shadow: 0 2px 5px 0 rgba(0,0,0,.25)!important;height: 110px; background-color: white;">
    <div class="col" style="width: 20%;padding-left: .5rem;padding-top: .95rem;padding-bottom: .5rem;">
  <i class="fa fa-bullhorn" style="
    font-size: 5em;
    padding-left: 10px;
    padding-top: 5px;
    color: rgba(218, 183, 0, 0.65);
"></i>
</div>

    <div class="col" style="padding-top: 25px;padding-left: 15px;margin-top: -2%;width: 70%;line-height: .95rem;">

        <span style="margin-top: 6%;padding-bottom: -26px;">Average ratings</span><br>
            
        <div class="col" style="max-width: 20%;white-space: nowrap;overflow: hidden;float: left;padding-right: 3px;font-weight: bold;color: #000000;">
            '.$avgStars.'
        </div>
<br>
        <div class="col" style="max-width: 200px;overflow: hidden;font-size: .75rem;color: rgba(218, 183, 0, 0.65);">
            Rating: '.$ratingAvg.'
        </div>
        
        <span style="font-size: 0.65rem;color: #6d6d6d8f;">('.$reviewSum.' reviews) </span> 
    </div>
</div>
    ';
    return $widget;
}

add_shortcode('yelpstars','srfa_getYelpWidget');
add_shortcode('fbstars','srfa_getFacebookWidget');
add_shortcode('avgstars','srfa_getavgWidget');
add_shortcode('starreview','run_starreviewcore');

function starReviews() {
    $pageID = get_queried_object_id();
    $displayID = get_option('display-page');
    if($pageID == $displayID){
        do_shortcode('[starreview]');
    }else{
        //do nothing
    }
}

add_action('wp_head', 'starReviews');
