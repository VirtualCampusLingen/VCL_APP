<?php
include '_boilerplate.html';
?>
    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <h1>Herzlich Willkommen</h1>
		<h2>im Administrationsbereich vom Virtuellen Campus Lingen</h2>
        <p>Hier finden Sie alle Einstellm&ouml;glichkeiten zur Administration des Virtuellen Rundgangs.</p>
      </div>
    </div>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-lg-4">
          <h2>Infotexte</h2>
          <p>Hier haben Sie die Möglichkeit Infotexte innerhalb der 360-Grad Bilder anzulegen.</p>
          <p><a class="btn btn-default" href="edit_infotext.php">Infotexte bearbeiten &raquo;</a></p>
       </div>
        <div class="col-lg-4">
          <h2>Fotos</h2>
          <p>Hier haben Sie die Möglichkeit die 360-Grad Bilder anzulegen und zuverwalten.</p>
          <p><a class="btn btn-default" href="edit_picture.php">Fotos bearbeiten &raquo;</a></p>
        </div>
		<div class="col-lg-4">
          <h2>Übersichtskarte</h2>
          <p>Hier haben Sie die Möglichkeit die 360-Grad Bilder auf der Übersichtskarte zu positionieren</p>
          <p><a class="btn btn-default" href="edit_map.php">Übersichtskarte bearbeiten &raquo;</a></p>
        </div>
      </div>

      <hr>

      <footer>
        <p>&copy; VCL 2013</p>
      </footer>
    </div> <!-- /container -->        
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-1.10.1.min.js"><\/script>')</script>

        <script src="assets/js/vendor/bootstrap.min.js"></script>

        <script src="assets/js/main.js"></script>

        <script>
            var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src='//www.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
    </body>
</html>
