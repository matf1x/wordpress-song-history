<?php
// Include other classes
require_once 'DB.php';

if( ! class_exists( 'Playlist' )) {
    class Playlist extends DBPlaylist {

        // Setup handlers
        private $settings = array(
            'selectedDay' => '',
            'nextDay' => '',
            'shows' => null,
            'songs' => array()
        );

        /**
         * The constructor to initialize the plugin
         */
        public function __construct() {
            
            // Add a shortcode to the Wordpress Library
            add_shortcode('ra-playlist', array($this, 'loadPlaylist'));

        }

        /**
         * Main function for the shortcode
         */
        public function loadPlaylist($atts) {

            // Show the header
            $this->showHeader();

            // Connect to the database
            $this->connect();

            // Get the songs based on the date
            if(!$this->getSongs()) { $this->error(); return; }

            // Show the songs
            $this->listSongs();
        }

        /**
         * Get the songs from the database based on a specific date
         */
        private function getSongs() {

            // Execute the query
            try{

                // Create the SQL query
                $query = 'SELECT songLibrary.artist, songLibrary.title, songLibrary.cover, songHistory.start FROM songHistory INNER JOIN songLibrary ON songHistory.trackGuid = songLibrary.trackGuid WHERE songHistory.start > ? AND songHistory.start < ? ORDER BY songHistory.start ASC';

                // Prepare the query
                $sth = $this->conn->prepare($query);

                // Execute the created query
                $sth->execute(array($this->settings["selectedDay"], $this->settings["nextDay"]));

                // Fetch the songs and put them into the holder
                $this->settings['songs'] = $sth->fetchAll(PDO::FETCH_ASSOC);

            } catch (Exception $e) {

                // Return false to stop the execution
                return false;

            }

            // Default return
            return true;
        }

        /**
         * Generatea error message to the user
         */
        private function error() {

            print('
            <div class="playlist playlist--error">
                <div class="sa">
                    <div class="sa-error">
                        <div class="sa-error-x">
                            <div class="sa-error-left"></div>
                            <div class="sa-error-right"></div>
                        </div>
                        <div class="sa-error-placeholder"></div>
                        <div class="sa-error-fix"></div>
                    </div>
                </div>
            </div>

            <h3>Er is geen playlist gevonden</h3>
            <p>Voor de geselecteerde dag, konden geen songs ophehaald worden.</p>
            ');

            return;
        }

        /**
         * Show a header where the user can navigate to a new date
         */
        private function showHeader() {

            // First Create the correct dates that needs to be handled
            $this->settings["selectedDay"] = ($_GET['date']) ? $_GET['date'] : date('Y-m-d');
            $this->settings["nextDay"] = date('Y-m-d', strtotime('+1 day', strtotime($this->settings["selectedDay"])));

            // Setup the formatter
            $fmt = datefmt_create('nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Brussels', IntlDateFormatter::GREGORIAN, 'EEEE dd MMMM yyyy');

            // Start the header
            print('
            <div class="playlistHeader">
                <a href="./?date=' . date('Y-m-d', strtotime('- 1 days', strtotime($this->settings['selectedDay']))) . '"><i class="fas fa-angle-left previousBtn"></i></a>
                <div>' . $fmt->format(datetime::createfromformat('Y-m-d', $this->settings["selectedDay"])) . '</div>
            ');

            // Check if there is a next date based
            if($this->settings["nextDay"] > date('Y-m-d'))
                print('<a href="#"><i class="fas fa-angle-right disabled"></i></a>');
            else
                print('<a href="./?date=' . $this->settings["nextDay"] . '"><i class="fas fa-angle-right nextBtn"></i></a>');

            // Close the header
            print('</div>');
            

        }

        /**
         * List all the songs
         */
        private function listSongs() {

            $fmt = datefmt_create('nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Brussels', IntlDateFormatter::GREGORIAN, 'HH:mm');

            // First, generate the playlist holder
            print('<div class="playlist">');

            // Loop over the songs
            foreach($this->settings['songs'] as $song) {
                // Do some other stuff here
                print('
                <div class="playlist--item">
                    <div class="playlist--item__timestamp">
                        ' . $fmt->format(datetime::createfromformat('Y-m-d H:i:s', $song['start'])) . '
                    </div>
                    <div class="playlist--item__cover">
                        <img src="data:image/png;charset=utf-8;base64,' . $song['cover'] . '">
                    </div>
                    <div class="playlist--item__info">
                        <p><span>' . $song['artist'] . '</span></p>
                        <p>' . $song['title'] . '</p>
                    </div>
                </div>
                ');
            }

            // At last, close the playlist holder
            print('</div>');
        }
    }
}