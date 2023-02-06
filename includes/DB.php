<?php
if( ! class_exists( 'DBPlaylist' )) {
    class DBPlaylist {
        // Global helpers
        private $settings = array(
            'DB_HOST'       => 'localhost',
            'DB_USER'       => '',
            'DB_PASSWORD'   => '',
            'DB_NAME'       => ''
        );
        public $conn = null;

        /**
         * The constructor to initialize the plugin
         */
        public function connect() {
            try {
                $this->conn = new PDO("mysql:host=".$this->settings['DB_HOST'].";dbname=".$this->settings['DB_NAME'], $this->settings['DB_USER'], $this->settings['DB_PASSWORD']);
            } catch(PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        }
    }
}
?>