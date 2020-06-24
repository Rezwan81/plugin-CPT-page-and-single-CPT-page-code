<?php


// start code for cpt page and single cpt page from plugin directory


function jobapplication_single_page( $file ) {
    global $post;

    if("jobapplication" == $post->post_type){
        $file_path = plugin_dir_path(__FILE__)."cpt-templates/single-jobapplication.php";
        $file = $file_path;
    }
    return $file;
}

add_filter('single_template', 'jobapplication_single_page');


/**
 * Add "Custom" template to page attirbute template section.
 
 theme_page_templates hook is available for page post type. If you want to add custom template to other
 post type you must replace page with your custom post type name e.g. event post type hook will have a name theme_event_templates.
 */
function wpse_288589_add_template_to_select( $post_templates, $wp_theme, $post, $post_type ) {

    // Add custom template named template-custom.php to select dropdown 
    $post_templates['jobapplication-cpt-page.php'] = __('rezwan plugin cpt page');

    return $post_templates;
}

add_filter( 'theme_page_templates', 'wpse_288589_add_template_to_select', 10, 4 );


/**
 * Check if current page has our custom template. Try to load
 * template from theme directory and if not exist.... load it 
 * from root plugin directory.
 */
function wpse_288589_load_plugin_template( $template ) {

    if(  get_page_template_slug() === 'jobapplication-cpt-page.php' ) {

        if ( $theme_file = locate_template( array( 'jobapplication-cpt-page.php' ) ) ) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path( __FILE__ ) . 'jobapplication-cpt-page.php';
        }
    }

    if($template == '') {
        throw new \Exception('No template found');
    }

    return $template;
}

add_filter( 'template_include', 'wpse_288589_load_plugin_template' );

// end code for cpt page and single cpt page from plugin directory











/**
* this system for cpt page and single cpt page within plugin directory
*
* I just used wordpress conditionals to check what what template was being requested  and then created a 
"theme_files" folder in my plugin directory and placed the appropriately named wordpress template files in there.
This was to create a single and archive template for a custom post type.
*/
function include_template_files() {

    $plugindir = dirname( __FILE__ );

    if ( is_post_type_archive( 'post-type-name' ) ) {
    	
        $templatefilename = 'archive-post_type_name.php';
        $template = $plugindir . '/theme_files/' . $templatefilename;
        return $template;
    } 

    if ( 'post-type-name' == get_post_type() ){

        $templatefilename = 'single-post-type-name.php';
        $template = $plugindir . '/theme_files/' . $templatefilename;
        return $template;
    }
}

add_filter( 'template_include', 'include_template_files' );

/**
* This example includes a new template on a page called ‘portfolio’ if the new template file was found.
*/
add_filter( 'template_include', 'portfolio_page_template', 99 );

function portfolio_page_template( $template ) {

    if ( is_page( 'portfolio' )  ) {
        $new_template = locate_template( array( 'portfolio-page-template.php' ) );
    if ( '' != $new_template ) {
        return $new_template ;
    }
    }
    return $template;
}

/**
* template include with locate template function
*
* This example includes a new template on a page called ‘portfolio’ if the new template file was found.
*/
add_filter( 'template_include', 'portfolio_page_template', 99 );

function portfolio_page_template( $template ) {

    if ( is_page( 'portfolio' )  ) {
        $new_template = locate_template( array( 'portfolio-page-template.php' ) );
    if ( '' != $new_template ) {    	
        return $new_template ;
    }
    }
    return $template;
}

/**
* Creating the routing rules
*
* We can use add_rewrite_rule function to register new custom routes with WordPress. 
This function can be implemented within many actions in WordPress. However, 
we usually use init action to handle the rewrite rules. Let's
add a custom rewrite rule to handle the scenario of user login and registration:
*/

  add_action( 'init','wqraf_manage_user_routes' );

  function wqraf_manage_user_routes() {

  add_rewrite_rule( '^user/([^/]+)/?', 'index.php?wpquick_action=$matches[1]', 'top' );}

/**
* In most vanilla WordPress installations, this is Hello World and the URL is usually 
http://domain.com/hello-world or http://domain.com/?p=1 depending on your permalink settings (that is, your current set of rewrite rules).

But let's define a rule such that http://domain.com/first will also load the first post in the database:
*/
function example_add_rewrite_rules() {
 
    add_rewrite_rule( 'first', 'index.php?p=1', 'top' );
    flush_rewrite_rules(); 
}
add_action( 'init', 'example_add_rewrite_rules' );

/**
* Let's add one more rule that will follow suit and allow us to load the second post in the database. Namely, http://domain.com/?p=2.
Assuming that you've read the documentation for add_rewrite rule, this is easy enough to understand,
*/
function example_add_rewrite_rules() {
 
    add_rewrite_rule( 'first', 'index.php?p=1', 'top' );
    add_rewrite_rule( 'second', 'index.php?p=2', 'top' );
 
    flush_rewrite_rules();
 
}
add_action( 'init', 'example_add_rewrite_rules' );







// another system from wplms theme

if(isset($_GET['submissions']) || $action == 'submissions'){

locate_template( array( 'course/single/submissions.php'  ), true );

}else if(isset($_GET['stats'])  || $action == 'stats'){

    locate_template( array( 'course/single/stats.php'  ), true );
}else{

    $tab_content = apply_filters('wplms_course_admin_tab_content',0,$action);
    global $post;
    if(function_exists('bp_course_get_students_count')){
        $students=bp_course_get_students_count(get_the_ID());
    }else{
        $students=get_post_meta(get_the_ID(),'vibe_students',true); 
    }

$loop_number=vibe_get_option('loop_number');
if(!isset($loop_number)) $loop_number = 5;
    
?>  
    <h4 class="total_students"><?php _e('Total number of Students in course','vibe'); ?><span><?php echo vibe_sanitizer($students,'text'); ?></span></h4>
    <?php

    if(function_exists('bp_course_get_course_students')){
        $course_students=apply_filters('bp_course_admin_before_course_students_list',bp_course_get_course_students(),get_the_ID());
        $students_undertaking = $course_students['students'];
        $max_page = ceil($course_students['max']/$loop_number);
    }else{
        $students_undertaking=apply_filters('bp_course_admin_before_course_students_list',bp_course_get_students_undertaking(),get_the_ID());   
        $max_page = ceil(count($students_undertaking)/$loop_number);
    }


?>

<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">

<?php    



// another system

$current_action = bp_current_action();
    
    if(!empty($_GET['action'])){
        $current_action = $_GET['action'];
    }

    global $bp;
    if(!empty($current_action)):

        switch($current_action){
            case 'curriculum':
                locate_template( array( 'course/single/curriculum.php'  ), true );
            break;
            case 'members':
                locate_template( array( 'course/single/members.php'  ), true );
            break;
            case 'activity':
                locate_template( array( 'course/single/activity.php'  ), true );
            break;
            case 'submissions':
            case 'stats':
            case 'admin':
                $uid = bp_loggedin_user_id();
                $authors=array($post->post_author);
                $authors = apply_filters('wplms_course_instructors',$authors,$post->ID);
                
                if(current_user_can( 'manage_options' ) || in_array($uid,$authors)){
                    locate_template( array( 'course/single/admin.php'  ), true );   
                }else{
                    locate_template( array( 'course/single/front.php' ) );
                }
            break;
            default:
                locate_template( array( 'course/single/front.php' ) );
        }
        do_action('wplms_load_templates');
    else :
        
        if ( isset($_POST['review_course']) && isset($_POST['review']) && wp_verify_nonce($_POST['review'],get_the_ID()) ){
             global $withcomments;
              $withcomments = true;
              comments_template('/course-review.php',true);
        }else if(isset($_POST['submit_course']) && isset($_POST['review']) && wp_verify_nonce($_POST['review'],get_the_ID())){ // Only for Validation purpose
            
            bp_course_check_course_complete();
            
        // Looking at home location
        }else if ( bp_is_course_home() ){
            
            // Use custom front if one exists
            $custom_front = locate_template( array( 'course/single/front.php' ) );
            if     ( ! empty( $custom_front   ) ) : vibe_include_template("course/front$course_layout.php",'course/single/front.php');
            
            elseif ( bp_is_active( 'structure'  ) ) : locate_template( array( 'course/single/structure.php'  ), true );

            // Otherwise show members
            elseif ( bp_is_active( 'members'  ) ) : locate_template( array( 'course/single/members.php'  ), true );

            endif;

        // Not looking at home
        }else {
            
            // Course Admin/Instructor
            if     ( bp_is_course_admin_page() ) : locate_template( array( 'course/single/admin.php'        ), true );

                // Course Members
            elseif ( bp_is_course_members()    ) : locate_template( array( 'course/single/members.php'      ), true );

            // Anything else (plugins mostly)
            else                                : 
                locate_template( array( 'course/single/plugins.php'      ), true );
            endif;
        }
    endif;
















/**
* multiple custom routing with one page
*/
function include_template_files_on_page( $action ) {

	$action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';

	switch ( $action ) {
	    case 'add-list' :
			$template = __DIR__ .'views/address-new.php';
			break;
		case 'edit-list' :
			$template = __DIR__ .'views/address-edit.php';
			break;
		case 'view-list' :
			$template = __DIR__ .'views/address-view.php';
			break;
		default :
			$template = __DIR__ .'views/address-list.php';
			break;			
	}
	if( file_exists( $template ) ) {
		include $template;
	}
	return $action;
}
add_filter( 'template_include', 'include_template_files_on_page' );



