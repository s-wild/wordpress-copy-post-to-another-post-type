<?php
/*
     Plugin Name: Immediate Deliveries
     Author: Simon Wild
*/

add_filter('page_row_actions','my_action_row', 10, 2);


/**
* Add link to action menu
*
*/
function my_action_row($actions, $post){
	$actions['in_google'] = '<a href="admin.php?action=create_immediate_delivery&amp;post='.$post->ID.'">Create immediate delivery</a>';
    //check for your post type
    if ($post->post_type =="ms-home"){
        /*do you stuff here
        you can unset to remove actions
        and to add actions ex:*/
        

    }
    return $actions;
}


/**
* Create new node if action link is clicked.
*
*/
function createNode(){
     // Check if action is set in the url and if it is immediate delivery.
     if (isset($_GET['action'])) {
          if($_GET['action'] == "create_immediate_delivery"){
               // Check if post id is in URL.
               if (isset($_GET['post'])) {
                    global $wpdb;
                    $current_post = get_post(($_GET['post'])); 
                    $current_post_meta = get_post_meta($_GET['post']); 
                    
                    // Set post
                    $my_post = array( 
                         'post_title'     => $current_post->post_title,
                         'post_type'      => 'immediate-delivery',
                         'post_name'      => $current_post->post_name,
                         'post_content'   => $current_post->post_content,
                         'post_status'    => 'publish',
                         'comment_status' => 'closed',
                         'ping_status'    => 'closed',
                         'post_author'    => 1,
                         'menu_order'     => 0
                    );

                    $price = 999;
                    if(isset($current_post_meta['wpcf-price'][0])){
                         $price = $current_post_meta['wpcf-price'][0];
                    }
                    
                    $PageID = wp_insert_post( $my_post, false ); // Get Post ID - FALSE to return 0 instead of wp_error.
                    
                    wp_set_post_categories($PageID, wp_get_post_categories( $_GET['post']));
                    
                    // Add to custom fields such as price.
                    update_post_meta( $PageID,  'wpcf-price', $price);
                    update_post_meta( $PageID,  'wpcf-original-price', $price );
                    update_post_meta( $PageID,  'wpcf-parent-id', $current_post->ID );
                    //update_post_meta( $PageID,  'wpcf-im-beds-min', $current_post_meta->ID );
                    
                    // Redirect to newly created immediate delivery, then exit. 
                    wp_redirect(site_url() . "/wp-admin/post.php?post=" . $PageID . "&action=edit");
                    exit();
               }
          }
     }
}
add_action('init', 'createNode');