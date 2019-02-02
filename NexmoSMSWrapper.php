<?php 

    /**
     * NexmoSMSWrapper need to send sms easily using Nexmo REST Api
     * It's unoffical package.
     */

    interface INexmoSMSWrapper {
        public function __construct($api_key, $secret);

        public function set_sender($value);
        
        public function set_receiver($value);

        public function set_timeout($value);

        public function send($message, $from, $to, $ttl);
    }

    class NexmoSMSWrapper implements INexmoSMSWrapper {
        private $from = "";
        private $to = "";
        private $ttl = "";

        private $api_key = "";
        private $api_secret = "";
        private $endpoint = "https://rest.nexmo.com/sms/json";

        public function __construct($api_key = "", $api_secret = "") {
            if (!$api_key || !$api_secret) {
                return;
            }

            $this->api_key = $api_key;
            $this->api_secret = $api_secret;
        }

        /**
         * Set default params 
         * @param string $value Sender name. 
         * @return NexmoSMSWrapper
         */
        public function set_sender($value = "") {
            $this->from = $value;
            return $this;
        }

        /**
         * @param string $value Receive number. 
         * @return NexmoSMSWrapper
         */
        public function set_receiver($value = "") {
            $this->to = $value;
            return $this;
        }
        
        /**
         * @param string $value Timeout (in seconds). 
         * @return NexmoSMSWrapper
         */
        public function set_timeout($value = 0) {
            $this->ttl = $value * 100;
            return $this;
        }

        public function send($message = "", $from = "", $to = "", $ttl = 20000) {
            $from = $this->from ? $this->from : $from;
            $to = $this->to ? $this->to : $to;
            $ttl = $this->ttl ? $this->ttl : $ttl;

            $data = array(
                'from' => $from,
                'to' => $to,
                'text' => $message,
                'ttl' => $ttl,
                'api_secret' => $this->api_secret,
                'api_key' => $this->api_key
            );


            $context = $this->get_context($data);
            $result = file_get_contents($this->get_endpoint(), false, $context);
            return $result;
        }

        /**
         * Generate context for POST request
         * @param array $data;
         * @return array stream context result
         */
        private function get_context($data = array()) {
            $options = array(
                'http' => array(
                    'header' => "Content-Type: application/json\r\n",
                    'method' => 'POST',
                    'content' => json_encode($data)
                )
            );

            return stream_context_create($options);
        }

        private function get_endpoint() {
            if (!$this->api_key || !$this->api_secret) {
                return false;
            }
            return $this->endpoint . "?" . http_build_query(array("api_key" => $this->api_key, "api_secret" => $this->api_secret));
        }
    }
?>