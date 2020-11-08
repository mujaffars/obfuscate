<?php
require 'src/Obfuscator.php';

$sData = <<<'DATA'
    
defined('BASEPATH') OR exit('No direct script access allowed');

class MwTransaction_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('MwTransaction/Mw_customers_Model');
        $this->load->model('MwTransaction/Mw_invoice_Model');
    }

    public function createCustomer() {
        $returnData = $this->Mw_customers_Model->createCustomer();
        echo $this->encdec->returnjson($returnData);
    }

    public function listCustomer() {
        echo 'heaaaaare';exit;
        $returnData = $this->Mw_customers_Model->listCustomer();
        echo $this->encdec->returnjson($returnData);
    }

    public function searchCustomer() {
        $data = $this->encdec->loadjson();
        $array = json_decode(json_encode($data), true);
        
        $searchForArray = array();
        if (isset($array['vehicleNo']) && $array['vehicleNo'] !== "") {
            $searchForArray['vehicle_no'] = $array['vehicleNo'];
        } else if (isset($array['mobile_no']) && $array['mobile_no'] !== "") {
            $searchForArray['mobile_no'] = $array['mobile_no'];
        }
        if (isset($array['id']) && $array['id'] !== "") {
            $searchForArray['id'] = $array['id'];
        }

        $returnData = $this->Mw_customers_Model->searchCustomer($searchForArray);

        if ($array['getfor'] == "custinv") {
            $custInvoiceDtl['cust'] = $returnData;
            if ($returnData) {
                // Get all the invoices
                $invSearchArr = array('c_id' => $returnData->id);
                $custInvoices = $this->Mw_invoice_Model->getAllInvoices($invSearchArr);
                
                $custInvoices = json_decode(json_encode($custInvoices), true);
                $arrInvoices = array();
                
                if ($custInvoices) {
                    foreach ($custInvoices As $key => $rowInvoice) {
                        $arrInvoices[$key] = $rowInvoice;
                        $arrInvoices[$key]['formDate'] = date('d-M-Y', strtotime($rowInvoice['invdate']));
                    }
                }
                $custInvoiceDtl['invoice'] = $arrInvoices;
                $custInvoiceDtl['code'] = 0;
                echo $this->encdec->returnjson($custInvoiceDtl);
            }
        } else {
            echo $this->encdec->returnjson($returnData);
        }
    }

    public function getCustomerAutocomp() {
        $returnData = $this->Mw_customers_Model->listCustomer();
        echo $this->encdec->returnjson($returnData);
    }

    public function updateCustomer() {
        $data = $this->encdec->loadjson();
        $array = json_decode(json_encode($data), true);
        
        $returnData = $this->Mw_customers_Model->updateCustomer($array);
        echo $this->encdec->returnjson($returnData);
    }
    
    public function deleteCustomer(){
        $data = $this->encdec->loadjson();
        $array = json_decode(json_encode($data), true);
        
        $returnData = $this->Mw_customers_Model->deleteCustomer($array['cust_id']);
        echo $this->encdec->returnjson($returnData);
    }
}
DATA;

$sObfusationData = new Obfuscator($sData, 'Class/Code NAME');
file_put_contents('my_obfuscated_data.php', '<?php ' . "\r\n" . $sObfusationData);