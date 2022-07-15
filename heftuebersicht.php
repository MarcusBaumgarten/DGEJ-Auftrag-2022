<?php
echo "<spannk rel='stylesheet' type='text/css' href='../../wp-content/plugins/zeitschriften/zeitschriften.css' />";
header('Content-Type: text/html; charset=ISO-8859-1');
require_once('incl/password.inc.php');

/** in Plugin: DGEJ Zeitschriften Datenbank - ausgabe2.php **/

$db = new mysqli($host, $user, $password, $database);

$sql = "SELECT * FROM dgej_gesamt2";

if ( $result = $db->query($sql) ) {
    
    ?>
    <h1>DGEJ Zeitschriften Datenbank - TEST</h1>
    
    <span><a href="#review">Rezensionen</a></span><br/>
    <span><a href="#article">Artikel</a></span>
    <br/>
    <hr/>
    <h2 id="review">Rezensionen</h2>
    <ol class="datensatz"> 
    <?php
    while ($inhalt = $result->fetch_object()) {
        if ($inhalt->Review == 'R') {
    ?>
	<li><?php
	    printf("%s, %s", $inhalt->Reviewer1_nachname, $inhalt->Reviewer1_vorname);
            if ( strlen($inhalt->Autor1_nachname) > 0 ) { 
                echo ": Rezension von:";?>
                <span class="author"><?php
		    printf("%s, %s", $inhalt->Autor1_nachname, $inhalt->Autor1_vorname);
		?></span><?php
            }    
            if ( strlen($inhalt->Autor2_nachname) > 0 ) {
                echo "; ";?>
		<span class="author"><?php 
		    printf("%s, %s", $inhalt->Autor2_nachname, $inhalt->Autor2_vorname);
		?></span><?php
	    }
    if (strlen($inhalt->Funktion) > 0) {
        echo "(";
        printf($inhalt->Funktion);
        echo ")";
    }
      echo ": ";
    ?>
    <span class="title"><?php echo $inhalt->Title; ?></span><?php
      if ( strlen($inhalt->Series) > 0 ) {
        echo " (";
	    ?><span class="series"><?php
		    echo $inhalt->Series;
	    ?></span>
            <span class="nr"><?php
		 echo $inhalt->{"nr."};
	    ?></span><?php 
    		echo ")";
	    }
	    echo ", ";
    	    ?>
    <span class="Ort"><?php echo $inhalt->ort; ?></span><?php
		echo ":";?>
    <span class="verlag"><?php echo $inhalt->verlag; ?></span>
    <span class="jahr"><?php echo $inhalt->jahr; ?></span><?php
		echo ". DAJ ";
	    ?><span class="ausgabe"><?php echo $inhalt->ausgabe; ?></span><?php
       		echo ", ";
	    ?><span class="seitenzahl"><?php
		echo "S. "; echo $inhalt->seitenzahl;
	    ?></span><?php
		echo ".";?>
      </li>
    <?php
  }
    }
}

?>
</ol>
<?php $result->data_seek(0);?>
<hr/>
    
<h2 id="article">Artikel</h2>
    <ol class="datensatz"> 
 <?php
    while ($inhalt = $result->fetch_object()) {
        
        if ($inhalt->Article == 'A') {
        
    ?> 
    
       <li>      
          <span class="author">
            <?php printf("%s, %s", $inhalt->Autor1_nachname, $inhalt->Autor1_vorname); ?>
          </span>
    <?php
      if ( strlen($inhalt->Autor2_nachname) > 0 ) { 
        echo "; ";
    ?>
        <span class="author">
          <?php printf("%s, %s", $inhalt->Autor2_nachname, $inhalt->Autor2_vorname); ?>
        </span>
      <?php
      }
      if ( strlen($inhalt->Autor3_nachname) > 0 ) {
        echo "; ";
     ?>
        <span class="author">
          <?php printf("%s, %s", $inhalt->Autor3_nachname, $inhalt->Autor3_vorname); ?>
        </span>
    <?php }
      echo ": ";
    ?>
    <span class="title">
    <?php echo $inhalt->Title; ?>
    </span>
    <?php      
        echo ". DAJ ";
     ?>
        
    <span class="ausgabe">
    <?php echo $inhalt->ausgabe; ?>
    </span>    
        <span class="seitenzahl">
          <?php echo ", S. "; echo $inhalt->seitenzahl; ?>
        </span>
        <?php echo ".";?>
      </li>
    <?php
  }
}

?>
</ol>

