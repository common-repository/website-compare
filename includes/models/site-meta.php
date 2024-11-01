<?php

class Plgn_Cmpr_SiteMeta {

    public $url;
    public $site_environment;
    public $site_color;
    public $site_label;
    public $somethingsomething;


    public function __construct($site) {
        $this->url = "";
        $this->site_environment = "";
        $this->site_color = "";
        $this->site_label = "";
        $this->somethingsomething = "";

        if(!empty($site['url'])){
            $this->url = $site['url'];
        }
        if(!empty($site['site_environment'])){
            $this->site_environment = $site['site_environment'];
        }
        if(!empty($site['site_color'])){
            $this->site_color = $site['site_color'];
        }
        if(!empty($site['site_label'])){
            $this->site_label = $site['site_label'];
        }
        if(!empty($site['somethingsomething'])){
            $this->somethingsomething = $site['somethingsomething'];
        }
    }

    public function geturl(){
        return $this->url;
    }
    public function seturl($url){
        if($url)
        $this->url = $url;
    }

    public function getSiteEnvironmentClass(){
        if (!empty($this->site_environment)){
            return "plcmpr-env-ind--" . strtolower($this->site_environment);
        }
        return "";
    }

    public function getSiteEnvironment(){
        return $this->site_environment;
    }
    public function setSiteEnvironment($site_environment){
        if($site_environment)
        $this->site_environment = $site_environment;
    }


    public function get_site_color(){
        return $this->site_color;
    }
    public function set_site_color($site_color){
        if($site_color)
        $this->site_color = $site_color;
    }


    public function getSiteLabel(){
        return $this->site_label;
    }
    public function setSiteLabel($site_label){
        if($site_label)
        $this->site_label = $site_label;
    }
    public function getSiteTitle(){
        return  esc_html($this->site_label);
    }


    public function gettMetaAsTitle(){
        $oputput = "";

        $oputput .= "<p class='plcmpr-site-title'>";
            $oputput .= $this->getSiteTitle();
            $oputput .= " ";
                $oputput .= "<span class='plcmpr-env-ind " . $this->getSiteEnvironmentClass() . "'>";
                    $oputput .= $this->getSiteEnvironment();
                $oputput .= "</span>";
            $oputput .= "<br>";
            $oputput .= "<small>(";
                $oputput .= $this->geturl();
            $oputput .= ")</small>";
        $oputput .= "</p>";

        return $oputput;
    }



    public function getIsActive(){
        return $this->somethingsomething;
    }
    public function getIsActiveCheck(){
        if($this->somethingsomething){
            return '<span class="dashicons dashicons-saved"></span>';
        }
        return '';
    }
    public function setIsActive($somethingsomething){
        if($somethingsomething){
            $this->somethingsomething = $somethingsomething;
        }else{
            $this->somethingsomething = false;
        }
    }
    // public function compareIsActive(Plgn_Cmpr_site $othersite){
    //     if ($this->somethingsomething == $othersite->getIsActive()){
    //         return 0;
    //     }
    //     if ($this->somethingsomething && !$othersite->getIsActive()){
    //         return -1;
    //     }
    //     if (!$this->somethingsomething && $othersite->getIsActive()){
    //         return 1;
    //     }
    //     return -100;
    // }

}
