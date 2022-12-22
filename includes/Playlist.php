<?php
if( ! class_exists( 'Playlist' )) {
    class Playlist {

        // Setup handlers
        private $settings = array(
            'songsApiUri' => 'https://www.radioaccent.be/api/song/playlist/',
            'showsApiUri' => 'https://www.radioaccent.be/api/shows/date/',
            'selectedDate' => '',
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
                <a href="./?date=' . date('Y-m-d', strtotime('- 1 days', strtotime($this->settings['selectedDate']))) . '"><i class="fas fa-angle-left previousBtn"></i></a>
                <div>' . strftime('%A %e %B %Y', strtotime($this->settings['selectedDate'])) . '</div>
                <a href="./?date=' . date('Y-m-d', strtotime('+ 1 days', strtotime($this->settings['selectedDate']))) . '"><i class="fas fa-angle-right nextBtn"></i></a>
            </div>

            <div class="playlistContent">
            ');

        }

        /**
         * Load the playlist for the selected date
         */
        public function loadPlaylistForDay($atts) {

            // Check if attributes are set, otherwise, use the default value
            $atts = shortcode_atts(
                array(
                    'limit' => 25,
                    'start' => 0
                ), $atts, 'ra-playlist');

            // Set default date
            $this->settings["selectedDate"] = ($_GET['date']) ? $_GET['date'] : date('Y-m-d');

            // Set the correct URL's for the API
            $this->settings['songsApiUri'] = $this->settings['songsApiUri'] . $this->settings['selectedDate'];
            $this->settings['showsApiUri'] = $this->settings['showsApiUri'] . $this->settings['selectedDate']; 

            // Create the header for the page
            $this->createHeader();
            
            // Get the songs from date
            $songsData = $this->getSongsFromDate();

            // Check if there was an error
            if(!$songsData['status'])
                $this->printError();

            // If there was no error, show the songs
            $this->printSongs(json_decode($songsData['songs']));

            // Create the closing tag
            print('
            </div>
            ');

        }

        /**
         * Get the songs for a specific date
         */
        private function getSongsFromDate() {

            // Create the Curl
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $this->settings['songsApiUri']);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

            // Execute the curl
            $responseBody = curl_exec($c);

            // Close the curl
            curl_close($c);

            if ($responseBody === false) {
                return array("status" => false,  "message" => curl_error($c));
            }

            // Return the body         
            return array("status" => true,  "songs" => $responseBody);

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
            ');
        }

        /**
         * Get the shows for a specific date
         */
        private function getShowsForDate() {
            // This will be for later, because another plugin needs to be developed for this to work
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
                        ' . date('H:i', strtotime($song->startTime)) . '
                    </div>
                    <div class="songItem--cover">
                        <img src="' . $song->cover . '">
                    </div>
                    <div class="songItem--content">
                        <div>
                            <p class="artist">' . $song->artist . '</p>
                            <p class="title">' . $song->title . '</p>
                        </div>
                    </div>
                </div>
                ');

            }

            // temp
            /**print('<pre>');
            print_r($songs);
            print('</pre>');**/

        }

    }
}