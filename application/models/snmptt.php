<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Snmptt extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    function read($q) {
        $query = $this->db->get_where('snmptt', $q);
        return $query;
    }
    
    function archive($q) {
    }
    
    function delete($q) {
    }
    
}
