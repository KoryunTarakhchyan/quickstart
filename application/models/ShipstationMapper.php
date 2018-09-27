<?php
class Atlas_Model_ShipstationMapper {
    protected $_name;
    protected $_password;
    protected $_url;

    public function __construct($name = "7cb92316f875439eace8179d9c7c1fee", $password = "cea556de07e24b719e22a55c3cab38ec") {
        $this->_url = "https://ssapi.shipstation.com/";
        if (trim($name) != "" && trim($password) != "") {
            $this->_name = $name;
            $this->_password = $password;
        } else {
            $this->_name = "X";
            $this->_password = "X";
        }
        return $this;
    }#end __construct function

    protected function setTransaction($transaction, $variables) {
        $this->_url .= $transaction;
        if (is_array($variables)) {
            $this->_url .= "?";
            $counter = 0;
            foreach ($variables as $index => $value) {
                $this->_url .= (($counter > 0) ? "&" : "") . $index . "=" . $value;
                ++$counter;
            }
        }
        return $this;
    }#end setTransaction function

    protected function execute() {
        // execute curl communication in order to retrieve data from M3
        $process = curl_init($this->_url);
        curl_setopt($process, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Authorization: Basic ' . base64_encode($this->_name . ":" . $this->_password)));
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($process, CURLOPT_TIMEOUT, 60);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $results = json_decode(curl_exec($process), true);
        curl_close($process);
        return $results;
    }#end execute() function

    public function PullData($transaction, $values) {
        return $this->setTransaction($transaction, $values)
                        ->execute();
    }#end listTransaction function
}

?>