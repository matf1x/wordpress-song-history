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
            'songs' => null
        );

        /**
         * The constructor to initialize the plugin
         */
        public function __construct() {
            
            // Add a shortcode to the Wordpress Library
            add_shortcode('ra-playlist', array($this, 'loadPlaylistForDay'));

        }

        /**
         * Create the header to show
         */
        private function createHeader() {

            // Create a print
            print('
            <div class="playlistHeader">
                <a href="./?date=' . date('Y-m-d', strtotime('- 1 days', strtotime($this->settings['selectedDay']))) . '"><i class="fas fa-angle-left previousBtn"></i></a>
                <div>' . strftime('%A %e %B %Y', strtotime($this->settings['selectedDay'])) . '</div>
                <a href="./?date=' . date('Y-m-d', strtotime('+ 1 days', strtotime($this->settings['selectedDay']))) . '"><i class="fas fa-angle-right nextBtn"></i></a>
            </div>

            <div class="playlistContent">
            ');

        }

        /**
         * Load the playlist for the selected date
         */
        public function loadPlaylistForDay($atts) {

            // Connect to the database
            $this->connect();

            // Check if attributes are set, otherwise, use the default value
            $atts = shortcode_atts(
                array(
                    'limit' => 25,
                    'start' => 0
                ), $atts, 'ra-playlist');

            // Get the songs by date
            $selectSongs = $this->selectSongsByDate();

            // Create the header for the page
            $this->createHeader();

            // Check if everything worked as planned
            if(!$selectSongs) {
                die($this->printError());
            }

            $this->printSongs($this->songs);

            // Create the closing tag
            print('
            </div>
            ');

        }

        /**
         * Get the songs for a specific date
         */
        private function selectSongsByDate() {

            // Wrap in a try to chatch all the errors
            try {

                // Set default date
                $this->settings["selectedDay"] = ($_GET['date']) ? $_GET['date'] : date('Y-m-d');

                // Get the next day
                $this->settings["nextDay"] = date('Y-m-d', strtotime('+1 day', strtotime($this->settings["selectedDay"])));
                
                // Get the songs from a specific date
                $query = 'SELECT songLibrary.artist, songLibrary.title, songLibrary.cover, songHistory.start FROM songHistory INNER JOIN songLibrary ON songHistory.trackGuid = songLibrary.trackGuid WHERE songHistory.start > ? AND songHistory.start < ? ORDER BY songHistory.start ASC';

                // Prepare the query
                $sth = $this->conn->prepare($query);

                // Execute the query
                $sth->execute(array($this->settings["selectedDay"], $this->settings["nextDay"]));

                // Get the songs from the query
                $this->songs = $sth->fetchAll(PDO::FETCH_ASSOC);

                // Default return     
                return true;

            } catch(Exception $e) {

                // Return an error
                return false;

            }


        }
        
        /**
         * Print the songs from a specified array
         */
        private function printSongs($songs) {
            // Loop trough songs
            foreach($songs as $song) {
                // Create element
                print('
                <div class="songItem">
                    <div class="songItem--time">
                        ' . date('H:i', strtotime($song['start'])) . '
                    </div>
                    <div class="songItem--cover">
                        <img src="data:image/png;charset=utf-8;base64,' . $song['cover'] . '">
                    </div>
                    <div class="songItem--content">
                        <div>
                            <p class="artist">' . $song['artist'] . '</p>
                            <p class="title">' . $song['title'] . '</p>
                        </div>
                    </div>
                </div>
                ');
            }
        }

        /**
         * Print an error when needed
         */
        private function printError() {
            print('
                <div class="errorBox">
                    <h2>Uhoh</h2>
                    <p>Het lijkt erop dat het even niet mogelijk is om de songs op te halen. Probeer later opnieuw</p>
                </div>
            </div>
            ');
        }

    }
}