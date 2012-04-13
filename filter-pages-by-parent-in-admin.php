<?php
/*
Plugin Name: Filter by parent in admin
Plugin URI: http://www.electricstudio.co.uk
Description: Filter pages in wp-admin by their parent page
Version: 1.3
Author: James Irving-Swift
Author URI: http://www.irving-swift.com
License: GPL2
*/


class ES_page_filter {

    public function __construct(){
        add_action('restrict_manage_posts',array($this, 'filter_by_parent_in_admin'));
        add_filter('parse_query',array($this,'filter_the_pages'));
    }
    
    public function filter_by_parent_in_admin(){
        global $pagenow;
        
	    if ($pagenow=='edit.php' && $_GET['post_type']=='page') {
	        
	        echo "Show only children of: ";
	        
	        if (isset($_GET['parentId'])) {
		        $dropdown_options = array(
		            'show_option_none' => __( ' - ' ),
		            'depth' => 2,
		            'hierarchical' => 1,
		            'post_type' => $_GET['post_type'],
		            'sort_column' => 'name',
		            'selected' => $_GET['parentId'],
		            'name' => 'parentId'
		        );
	        } else {
		        $dropdown_options = array(
		            'show_option_none' => __( ' - ' ),
		            'depth' => 2,
		            'hierarchical' => 1,
		            'post_type' => $_GET['post_type'],
		            'sort_column' => 'name',
		            'name' => 'parentId'
		        );
	        }
	        
	        wp_dropdown_pages( $dropdown_options );   
        }
    } //END METHOD filter_by_parent_in_admin
    
    public function filter_the_pages($query) {
    
        if (isset($_GET['parentId'])) {
	        global $pagenow;
    
	        $childPages = get_pages(
	            array(
	                'child_of' => $_GET['parentId'],
	                'post_status' => array('publish','draft','trash')
	                )
	             );
	        
	        $filteredPages = array($_GET['parentId']);
	        
	        foreach($childPages as $cp){
	        	array_push($filteredPages, $cp->ID);
	        }
	        
	        $qv = &$query->query_vars;
	        if ($pagenow=='edit.php' && $qv['post_type']=='page') {
	            $qv['post__in'] = $filteredPages;
	        }
        
        }
    
    } //END METHOD filter_the_pages
    
} //END CLASS

if(is_post_type_hierarchical($_GET['post_type'])){
    $es_page_filter = new ES_page_filter();
}
