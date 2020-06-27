<?php
namespace DatatableServerside;

class Index{
    private $CI;
    public function __construct(){
       $this->CI =& get_instance();
       $this->CI->load->database();
    }

    private function datatableapi_query($postData,$fdata){
        $i = 0;
        if(isset($postData['search']['value'])){
        foreach($fdata['column_search'] as $item){
            if($postData['search']['value']){
                if($i===0){
                    $this->CI->db->group_start();
                    $this->CI->db->like($item, $postData['search']['value']);
                }else{
                    $this->CI->db->or_like($item, $postData['search']['value']);
                }
                if(count($fdata['column_search']) - 1 == $i){
                    $this->CI->db->group_end();
                }
            }
            $i++;
        } }
        
        if(isset($postData['order'])){
            $this->CI->db->order_by($fdata['column_order'][$postData['order']['0']['column']], $postData['order']['0']['dir']);
        }else if(isset($fdata['order'])){
            $order = $fdata['order'];
            $this->CI->db->order_by(key($order), $order[key($order)]);
        }
    }
    public function load($postData,$fdata){
        $this->datatableapi_query($postData,$fdata);
        $numrows = $this->CI->db->count_all_results('', false);
        $length = empty($postData['length']) ? 10 : $postData['length'];
        $start = empty($postData['start']) ? 0 : $postData['start'];
        if($length != -1){
            $this->CI->db->limit($length, $start);
        }
        $query = $this->CI->db->get();
        $data = $query->result();
        return array("data"=>$data,"numrows"=>$numrows);
    }
}
