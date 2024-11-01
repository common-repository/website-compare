<?php


// https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slugs][]=hello-dolly&request[slugs][]=wordpress-importer
// is plugin updated
// https://stackoverflow.com/questions/55329625/is-there-a-wordpress-function-to-detect-if-a-plugin-is-not-up-to-date

// pull push
// https://wordpress.stackexchange.com/questions/175789/get-plugin-information-from-multiple-sites-externally

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.lehelmatyus.com
 * @since      1.0.0
 *
 * @package    Website_Compare
 * @subpackage Website_Compare/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Website_Compare
 * @subpackage Website_Compare/includes
 * @author     Lehel Mátyus <contact@lehelmatyus.com>
 */
class Website_Compare_Plugin_Getter{

    /**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
    private $version;

    private $plgncmpr_settings_manual_options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plgncmpr_settings_manual_options = get_option( 'plgncmpr_settings_manual_options' );

	}


    public function __get_all_plugins(){
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();
        return $all_plugins;
    }

    public function __map_simplify_plugin_item($item){

        $element = new Plgn_Cmpr_Plugin([]);
        $element->setName($item['Name']);
        $element->setVersion($item['Version']);

        return $element;
    }

    public function __clean_item_key($key){
        return strtok($key, '/');
    }


    public function __get_all_plugins_arr(){
        $all_plugins = $this->__get_all_plugins();
        $all_plugins_simple = array_map(array('Website_Compare_Plugin_Getter','__map_simplify_plugin_item'), $all_plugins);

        foreach ($all_plugins_simple as $key => $value) {
            $clean_key = $this->__clean_item_key($key);
            $all_plugins_simple[$key]->setLongId($key);
            $all_plugins_simple[$key]->setId($clean_key);
            $all_plugins_simple[$key]->setIsActive(is_plugin_active($key));
        }
        ksort($all_plugins_simple);

        $plgncmpr_general_options = get_option( 'plgncmpr_general_options' );

        $site_plugins['site'] = new Plgn_Cmpr_SiteMeta(array(
            'url' => get_site_url(),
            'site_label' => $plgncmpr_general_options['site_label'] ?: '',
            'site_environment' => $plgncmpr_general_options['site_environment'] ?: '',
            'site_color' => $plgncmpr_general_options['site_color'] ?: '',
        ));
        $site_plugins['plugins'] = $all_plugins_simple;

        return $site_plugins;

    }

    public function __get_all_plugins_json(){
        return json_encode($this->__get_all_plugins_arr());
    }

    public function __get_compare_plugins_tables(){

        $this_site_data = $this->__get_all_plugins_arr();
        $this_site_plugins = $this_site_data['plugins'];

        $extracted_data = $this->__extract_other_plugins_fron_JSONs($this->plgncmpr_settings_manual_options);
        
        if (empty($extracted_data)){
            _e("<b>----> Invalid data from comparable website. Check if both website run the same version of Website Compare</b>","website-compare");
            return NULL;
        }
        $other_sites_plugins = $extracted_data['plugins'];

        $this_site_meta = $this_site_data['site'];
        $other_sites_meta = $extracted_data['site'];
        // v_dump($other_sites_meta);

        // $this_site_meta->seturl("ddd");
        // v_dump($other_sites_meta);


        /**
         * Create array of Site Metas
         */
        $site_metas = [];
        $site_metas[] = $this_site_meta;
        $site_metas[] = $other_sites_meta;
        
        // foreach ($other_sites_meta as $site_meta) {
        //     $site_metas[] = $site_meta;
        // }


        $testdata = false;
        if ($testdata){
            unset($this_site_plugins['prism/prism.php']);

            $second_site_plugins = $this->__get_all_plugins_arr();
            unset($second_site_plugins['prism/prism.php']);
            unset($second_site_plugins['akismet/akismet.php']);

            $third_site_plugins = $this->__get_all_plugins_arr();
            unset($third_site_plugins['advanced-custom-fields/acf.php']);

            // fill with test data instead
            $other_sites_plugins = array();
            $other_sites_plugins[] = $second_site_plugins;
            $other_sites_plugins[] = $third_site_plugins;
        }

        // Merge multiple array into one compare array
        // Create Compare array from this sites plugins and all other sites plugins

        // add the current sites data to the all array
        $all_sites_plgn_arr[] = $this_site_plugins;
        
        // add the imported sites plugins to the all array
        foreach ($other_sites_plugins as $site) {
            $all_sites_plgn_arr[] = $site;
        }

        // Number of sites we are comparing
        $sites_number = count($all_sites_plgn_arr);

        // build comparison array
        $comp_arr = [];
        foreach($all_sites_plgn_arr as $site_id => $a_site_plgn_arr){
            foreach($a_site_plgn_arr as $plugin){
                $comp_arr[$plugin->getId()]['sites'][$site_id] = $plugin;
            }
        }

        // var_dump($comp_arr);
        // Get collor code array for every row
        // iterate on the compare array
        foreach ($comp_arr as $plugin_id => $comp_plugin_row) {

            // iterate on the individual sites for this plugin
            for ($site_id=0; $site_id < $sites_number; $site_id++) {

                $comp_arr[$plugin_id]['row_color_code'][$site_id]['row'] = 0;
                $comp_arr[$plugin_id]['row_color_code'][$site_id]['column'] = ['active'=>0, 'version' =>0];

                if(
                    (array_key_exists(0, $comp_arr[$plugin_id]['sites'])) &&
                    (! array_key_exists($site_id, $comp_arr[$plugin_id]['sites']))
                ){
                    // $comp_arr[$plugin_id]['row_color_code'][$site_id] = -1;
                    $comp_arr[$plugin_id]['row_color_code'][$site_id]['row'] = -1;
                }
                if(
                    (! array_key_exists(0, $comp_arr[$plugin_id]['sites'])) &&
                    (array_key_exists($site_id, $comp_arr[$plugin_id]['sites']))
                ){
                    // $comp_arr[$plugin_id]['row_color_code'][$site_id] = 1;
                    $comp_arr[$plugin_id]['row_color_code'][$site_id]['row'] = 1;
                }

                /**
                 * Column color code
                 */
                // if not first one (self that we compare to)
                // if site exist and not yet colored for row
                if(
                    $site_id != 0 &&
                    array_key_exists($site_id, $comp_arr[$plugin_id]['sites']) &&
                    $comp_arr[$plugin_id]['row_color_code'][$site_id]['row'] == 0
                ){
                    $comp_arr[$plugin_id]['row_color_code'][$site_id]['column'] = $this->__get_column_colors($comp_arr[$plugin_id]['sites'], $site_id);
                }

            }
        }


        ksort($comp_arr);
        // v_dump($comp_arr);

        return $this->__get_all_plugins_table($sites_number, $comp_arr, $site_metas);

    }

    public function __get_all_plugins_table($sites_number, $comp_arr, $site_metas){

        $item_class = "";
        $item_colum_class = "";
        $output = "";
        $max_width = $sites_number * 600;



        $output .= "<table class='plgn-cmpr-comp-table wp-list-table widefat fixed striped table-view-list posts' style='max-width: " . $max_width . "px;'>";
            $output .= "<tr>";
            $output .= "<th style='width: 20px;'></th>";
            for ($i=0; $i < $sites_number; $i++) {
                $output .= "<th style='' colspan='5'>" . $site_metas[$i]->gettMetaAsTitle() .  "</th>";
            }

        $output .= "</tr>";
        $output .= "<table>";
        
        $output .= "<table class='plgn-cmpr-comp-table wp-list-table widefat fixed striped table-view-list posts' style='max-width: " . $max_width . "px;'>";
            $output .= "<thead>";
            $output .= "<tr>";
                $output .= "<th style='width: 20px;'>#</th>";
                    for ($i=0; $i < $sites_number; $i++) {
                        $output .= "<th style='width:5px;'></th>";
                        $output .= "<th>Plugin</th>";
                        $output .= "<th>Active</th>";
                        $output .= "<th>Version</th>";
                        $output .= "<th style='width:1px; padding:8px 3px;'></th>";
                    }

                $output .= "</tr>";
            $output .= "</thead>";
            $output .= "<tbody>";
                $count = 0;
                if ((is_array($comp_arr) || is_object($comp_arr)) && is_iterable($comp_arr)) {
                    foreach ($comp_arr as $key => $plugin_row) {
                        $count++;
                        // $output .= "<tr ". (++$count%5 ? "" : "style='background-color: rgba(255, 20, 147, 0.1) !important;'") ." >";
                        $output .= "<tr>";
                        $output .= "<td style='width: 20px;'>{$count}</td>";

                        $plugin_row_sites = $plugin_row['sites'];

                        for ($i=0; $i < $sites_number; $i++) {

                            $item_change = $this->__get_item_change_for_table($plugin_row['row_color_code'][$i]['row']);
                            $item_class = $this->__get_item_class_for_table($plugin_row['row_color_code'][$i]['row']);

                            $item_colum_class_code = $plugin_row['row_color_code'][$i]['column'];
                            // var_dump($item_colum_class_code);
                            $td_class_active = $this->__get_td_diffclass_for_table($item_colum_class_code, 'active');
                            $td_class_version = $this->__get_td_diffclass_for_table($item_colum_class_code, 'version');

                            if (array_key_exists($i, $plugin_row_sites)){

                                $output .= "<td  class='{$item_class}' style='width:5px;'>{$item_change}</td>";
                                $output .= "<td  class='{$item_class}' >{$plugin_row_sites[$i]->getId()}</td>";
                                // <span style='font-size:36px; color: rgb(26, 137, 23)'>&rarrpl;<span>      <span style='font-size:36px; color: rgb(26, 137, 23); -moz-transform: scaleX(-1);'>&cularrp;</span> <span style='font-size:36px; color: rgb(26, 137, 23); -moz-transform: scaleX(-1);'>&rarrpl;<span>       </td>
                                $output .= "<td  class='{$item_class} {$td_class_active}' >{$plugin_row_sites[$i]->getIsActiveCheck()}</td>";
                                $output .= "<td  class='{$item_class} {$td_class_version}' >{$plugin_row_sites[$i]->getVersion()}</td>";
                                // $output .= "<td  class='{$item_class}' ><span style='font-size:36px; color: rgb(26, 137, 23)'>&cularrp;</span> </td>";
                                $output .= "<td  class='{$item_class}'  style='border-right:1px solid #ccc;border-left:1px solid #ccc; width:1px; padding:8px 3px;'></td>";
                            }else{
                                $output .= "<td  class='{$item_class}' style='width:5px;'>{$item_change}</td>";
                                $output .= "<td class='{$item_class}'></td>";
                                $output .= "<td class='{$item_class}'></td>";
                                $output .= "<td class='{$item_class}'></td>";
                                $output .= "<td class='{$item_class}' style='border-right:1px solid #ccc;border-left:1px solid #ccc; width:1px; padding:8px 3px;'></td>";
                            }
                        }

                        $output .= "</tr>";
                    }
                }else{
                    $output .= "<tr>";
                        $output .= "</td>";
                            $output .= __("Invalid data from comparable website","website-compare");
                        $output .= "</td>";
                    $output .= "</tr>";
                }
            $output .= "</tbody>";


        $output .= "</table>";

        return $output;

    }

    public function __get_item_class_for_table($class_code_value){
        if($class_code_value < 0){
            return 'item-negative';
        }
        if($class_code_value > 0){
            return 'item-positive';
        }
        return '';
    }


    /**
     * Creates classes for TDs
     */
    public function __get_td_diffclass_for_table($class_code_value, $column){

        if($class_code_value[$column] < 0){
            return $column .'-negative';
        }
        if($class_code_value[$column] > 0){
            return $column .'-positive';
        }

        return '';
    }


    public function __get_item_change_for_table($class_code_value){
        if($class_code_value < 0){
            return '-';
            // return '<span class="dashicons dashicons-minus"></span>';
        }
        if($class_code_value > 0){
            return '+';
            // return '<span class="dashicons dashicons-plus-alt2"></span>';
        }
        return '';
    }

    public function __extract_other_plugins_fron_JSONs($sites_data){

        $plugins_sites_array = array();
        if ((is_array($sites_data) || is_object($sites_data)) && is_iterable($sites_data)) {

            // Extract Plugins
            foreach ($sites_data as $key => $site_data) {

                $arr_from_json = '';
                try {
                    $arr_from_json = json_decode($site_data, $associative=true, $depth=512, JSON_THROW_ON_ERROR);
                } catch (Exception $e) {
                    // handle exception
                }
                if ($arr_from_json === null && json_last_error() !== JSON_ERROR_NONE) {
                    _e("json data is incorrect","website-compare");
                }

                // create array on "plugin" objects
                $plugins_arr = array();

                if ((is_array($arr_from_json['plugins']) || is_object($arr_from_json['plugins'])) && !empty($arr_from_json['plugins']) && is_iterable($arr_from_json['plugins'])) {
                    foreach ($arr_from_json['plugins'] as $key => $plugin_vals) {
                        $plugins_arr[$key] = new Plgn_Cmpr_Plugin($plugin_vals);
                    }
                }else{
                    _e("<br>ERROR: 3<br>","website-compare");
                    return NULL;
                }

                $plugins_sites_array[] = $plugins_arr;

            }

            // Extract Site Meta
            if ((is_array($arr_from_json['site']) || is_object($arr_from_json['site'])) && !empty($arr_from_json['site']) ) {
                $site_meta = new Plgn_Cmpr_SiteMeta($arr_from_json['site']);
            }



        }else{
            _e("<br>ERROR: 4<br>","website-compare");
            return NULL;
        }
        $pluginsdata['plugins'] = $plugins_sites_array;
        $pluginsdata['site'] = $site_meta;

        return $pluginsdata;
    }

    public function __get_column_colors($comp_arr_row, $site_id_to_compare){
        $column_colors = ['active'=>0, 'version' =>0];

        // if (!array_key_exists($site_id_to_compare, $comp_arr_row)){
        //     return $column_colors;
        // }

        // var_dump($comp_arr_row);
        // var_dump($site_id_to_compare);

        $this_site_plugin = $comp_arr_row[0];
        $comp_site_plugin = $comp_arr_row[$site_id_to_compare];

        $column_colors['active'] = $this_site_plugin->compareIsActive($comp_site_plugin);
        $column_colors['version'] = $this_site_plugin->compareVersion($comp_site_plugin);

        return $column_colors;
    }

    // public function __get_all_plugins_tables(){
    //     $output = "";
    //     $output .= "<table>";
    //         $output .= "<tr>";
    //             $output .= "<td style='padding-right:10px;'>";
    //                 $home_url = get_home_url();
    //                 $output .= "<h3> This Site <small>({$home_url})</small>  <-> Other Site</h3>";
    //                 $output .= $this->__get_all_plugins_table();
    //             $output .= "</td>";
    //             // $output .= "<td style='padding-left:10px;'>";
    //             //     $output .= "<h3> <big>B)</big> Other Site </h3>";
    //             //     $output .= $this->__get_all_plugins_table();
    //             // $output .= "</td>";
    //         $output .= "</tr>";
    //     $output .= "</table>";

    //     echo $output;

    // }


}
