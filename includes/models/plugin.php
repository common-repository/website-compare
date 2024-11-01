<?php

class Plgn_Cmpr_Plugin {

    public $name;
    public $version;
    public $id;
    public $long_id;
    public $is_active;


    public function __construct($plugin) {
        $this->name = "";
        $this->version = "";
        $this->id = "";
        $this->long_id = "";
        $this->is_active = "";

        if(!empty($plugin['name'])){
            $this->name = $plugin['name'];
        }
        if(!empty($plugin['version'])){
            $this->version = $plugin['version'];
        }
        if(!empty($plugin['id'])){
            $this->id = $plugin['id'];
        }
        if(!empty($plugin['long_id'])){
            $this->long_id = $plugin['long_id'];
        }
        if(!empty($plugin['is_active'])){
            $this->is_active = $plugin['is_active'];
        }
    }

    public function getName(){
        return $this->name;
    }
    public function setName($name){
        if($name)
        $this->name = $name;
    }

    public function getVersion(){
        return $this->version;
    }
    public function setVersion($version){
        if($version)
        $this->version = $version;
    }

    public function compareVersion(Plgn_Cmpr_Plugin $otherPlugin){

        $this_version = $this->getVersion();
        $other_version = $otherPlugin->getVersion();

        // check if valid version string at all
        if(
            version_compare( $this_version, '0.0.1', '>=' ) >= 0 &&
            version_compare( $other_version, '0.0.1', '>=' ) >= 0
            ) {
        } else {
            return -100;
        }

        if ($this_version==$other_version){
            return 0;
        }

        $code = 0;
        if (version_compare( $this_version, $other_version, '<' )){
            $code = 1;
        }else{
            $code = -1;
        }

        return $code;

    }

    public function getId(){
        return $this->id;
    }
    public function setId($id){
        if($id)
        $this->id = $id;
    }

    public function getLongId(){
        return $this->long_id;
    }
    public function setLongId($long_id){
        if($long_id)
        $this->long_id = $long_id;
    }

    public function getIsActive(){
        return $this->is_active;
    }
    public function getIsActiveCheck(){
        if($this->is_active){
            return '<span class="dashicons dashicons-saved"></span>';
        }
        return '';
    }
    public function setIsActive($is_active){
        if($is_active){
            $this->is_active = $is_active;
        }else{
            $this->is_active = false;
        }
    }
    public function compareIsActive(Plgn_Cmpr_Plugin $otherPlugin){
        if ($this->is_active == $otherPlugin->getIsActive()){
            return 0;
        }
        if ($this->is_active && !$otherPlugin->getIsActive()){
            return -1;
        }
        if (!$this->is_active && $otherPlugin->getIsActive()){
            return 1;
        }
        return -100;
    }

}
