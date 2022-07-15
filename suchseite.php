<?php
/**
 * Plugin Name: DGEJ Zeitschriften Datenbank - Dateiname: lessingsuche.php
 */
class lessingsuche {
    public function __construct() {
        add_filter( 'the_content', array( $this, 'searchform' ), 10, 1 );
        add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
    }
    
    
    
    public function searchform ($content) {
        if (is_page(1381))
            return '<div>
      <h1>Titelsuche in Beiträgen der DAJ - TEST</h1>
      <form id="searchform" method="get" action="suche.php" style="font-family: sans-serif;
    color: #a1b1bc;
    background: #fff;
    padding: 10px 20px;
    width: 50%;">
	  <div class="vision-search-form"><input type="text" name="q" id="q" placeholder="Suche" class="vision-search-field"/><input type="image" src="https://cdn.pixabay.com/photo/2017/01/13/01/22/magnifying-glass-1976105_960_720.png" height="40" class="vision-search-submit"/></div>
      </form>
      <div id="ergebnisse"></div>
    </div>';
            elseif (is_page(1383)) require 'ausgabe2.php';
            else return $content;
    }
    
    public function scripts () {
        wp_enqueue_script( 'lessingsuche', plugin_dir_url( __FILE__ ) . 'scripts.js', array( 'jquery' ), null, true );
        
        // set variables for script
        wp_localize_script( 'dgejsuche', 'settings', array(
            'url' => plugin_dir_url( __FILE__ )
        ) );
    }
    
    private function makelink ($var, $e) {
        if ($var != "") {
            echo "<a href=\"$var\">";
            switch ($e) {
                case "andb": echo "ADB/NDB-Link"; break;
                case "bibl": echo "Bibl. Angabe"; break;
                case "wiki": echo "Wikipedia"; break;
                case "int": echo "sonst. Link"; break;
                case "gnd": echo "GND"; break;
                case "bild": echo"Portr&auml;t"; break;
                case "quelle": echo "Quelle"; break;
            }
            echo "</a>";
        }
    }
    
    private function makeContent($search) {
        
        $start = isset($_GET["start"]) ? $_GET["start"] : 1;
        if ($start < 1) $start = 1;
        
        $DB = new mysqli(
            "localhost", // Datenbank-Host
            "cms_wpdgej", // Datenbank-Benutzer
            "GRZNxJRD9sIf0xqc", // Datenbank-Passwort
            "dgej_zeitschriften" // Datenbank-Name
            );
        $DB->set_charset("utf8");
        
        $que = "SELECT id FROM orte WHERE ortsname = '$search'";
        $result = $DB->query($que);
        $ort;
        if ($DB->errno == 0) {
            $ort = $result->fetch_array()[0];
        } else {
            echo $que;
            echo $DB->errno;
            echo "<hr>";
        }
        if ($ort == "") {
            $where = "";
        } else {
            $where = "WHERE `ort` = " . $ort;
        }
        
        $que = "SELECT personenlexikon.id, adb, anmerkung, beschreibung, absatz, absatz2, quelle, biblink, bild, geburt, geschlecht, gestorben, gnd,
            intlink, name, pseudonym, namelink AS namenszusatz, titel, verbindung, vorname, wiki, orta.ortsname AS ort,
            ortg.ortsname AS geburtsort, orts.ortsname AS sterbeort FROM `personenlexikon`
            LEFT JOIN orte AS orta ON (orta.id = personenlexikon.ort)
            LEFT JOIN orte AS ortg ON (ortg.id = personenlexikon.geburtsort)
            LEFT JOIN orte AS orts ON (orts.id = personenlexikon.sterbeort)
            $where ORDER BY name, vorname";
            
            $result = $DB->query($que);
            
            if ($DB->errno == 0) {
                $i = $start + 1;
                
                while ($res = $result->fetch_assoc()) {
                    $que = "SELECT `id`, `verbindung` FROM `x-verbindungen` WHERE `person` =" . $res["id"];
                    $verbResult = $DB->query($que);
                    $verb = $res["verbindung"];
                    /*$timeBirth = strtotime($res["geburt"]);
                     $res["geburt"] = date("d-m-Y", $timeBirth);
                     $timeDeath = strtotime($res["gestorben"]);
                     $res["gestorben"] = date("d-m-Y", $timeDeath);*/
                    $res["geburt"] = $this->parseDate($res["geburt"]);
                    $res["gestorben"] = $this->parseDate($res["gestorben"]);
                    while ($ver = $verbResult->fetch_assoc()) {
                        $verb .= " – " . $ver["verbindung"];
                    }
                    
                    $resp .= '
          <div class="person">
            <span class="gesamt">';
                    if (isset($res["name"]) && $res["name"] != "")
                        $resp .= '<span class="name">' . $res['name'] . ', </span>';
                        if (isset($res["vorname"]) && $res["vorname"] != "")
                            $resp .= '<span class="vorname">' . $res['vorname'] .'</span>';
                            if (isset($res["pseudonym"]) && $res["pseudonym"] != "")
                                $resp .= '<span class="pseudonym">Pseudonym:' . $res['pseudonym'] .'</span>';
                                if (isset($res["geburt"]) && $res["geburt"] != "")
                                    $resp .= '<span class="geboren">geboren am: ' . $res['geburt'] . ' in: ' . $res['geburtsort'] . '</span>';
                                    if (isset($res["gestorben"]) && $res["gestorben"] != "")
                                        $resp .= '<span class="gestorben">gestorben am: ' . $res['gestorben'] .' in: ' . $res['sterbeort'] . '</span>';
                                        
                                        //  if (isset($verb) && $verb != "")
                                        //  $resp .= '<span class="verbindungen">Verhältnis zu Lessing: ' . $verb . '</span>';
                                        
                                        if (isset($res["beschreibung"]) && $res["beschreibung"] != "")
                                            $resp .= '<span class="beschreibung">' . $res['beschreibung'] .'</span>';
                                            if (isset($res["absatz"]) && $res["absatz"] != "")
                                                $resp .= '<span class="absatz">' . $res['absatz'] .'</span>';
                                                if (isset($res["absatz2"]) && $res["absatz2"] != "")
                                                    $resp .= '<span class="absatz">' . $res['absatz2'] .'</span>';
                                                    if (isset($res["gnd"]) && $res["gnd"] != "0")
                                                        $resp .= '<span class="gnd"><a href="http://d-nb.info/gnd/' . $res['gnd'] . '">Link zur GND</a></span>';
                                                        if (isset($res["adb"]) && $res["adb"] != "")
                                                            $resp .= '<span class="adb"><a href="' . $res['adb'] . '">Link zur ADB</a></span>';
                                                            if (isset($res["wiki"]) && $res["wiki"] != "")
                                                                $resp .= '<span class="wiki"><a href="' . $res['wiki'] . '">Link zur Wikipedia</a></span>';
                                                                if (isset($res["biblink"]) && $res["biblink"] != "")
                                                                    $resp .= '<span class="biblink"><a href="' . $res['biblink'] . '">Link zur NDB</a></span>';
                                                                    if (isset($res["intlink"]) && $res["intlink"] != "")
                                                                        $resp .= '<span class="intlink"><a href="' . $res['intlink'] . '">Online-Quelle</a></span>';
                                                                        if (isset($res["bild"]) && $res["bild"] != "")
                                                                            $resp .= '<span class="bild"><a href="' . $res['bild'] . '" target="_blank">' . '<img width="200px" src="' . $res['bild'] . '"/></a></span>';
                                                                            if (isset($res["quelle"]) && $res["quelle"] != "")
                                                                                $resp .= '<span class="quelle">Quelle: ' . $res['quelle'] .'</span>';
                                                                                
                                                                                $resp .= '</span>
                </div>';
                }
            }
            else {
                echo $DB->error;
                echo $que;
            }
            
            $resp .= '</div>';
            return $resp;
    }
}

new lessingsuche();
?>

