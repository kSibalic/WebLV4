<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'auth/check_session.php';
?>
<!DOCTYPE html>
<html lang="hr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Karlo Sibalic" />
    <meta name="description" content="LV1 iz kolegija Web programiranje" />
    <meta name="keywords" content="HTML, CSS, FERIT" />
    <link rel="stylesheet" href="style/style.css" />
    <title>LV1 - Web programiranje</title>
  </head>
  <body>
    <header aria-labelledby="naslovna-traka">
      <h1>Dobrodosli na moju web stranicu</h1>
    </header>

    <nav aria-labelledby="primarna-navigacija">
      <ul>
        <li class="dropdown">
          <a href="javascript:void(0)" class="dropbtn">Menu</a>
          <div class="dropdown-content" id="primarna-navigacija">
            <a href="index.php">Pocetna</a>
            <a href="pages/grafikon.php">Grafikon</a>
            <a href="pages/slike.php">Galerija</a>
            <a href="pages/videoteka.php">Videoteka</a>
            <?php if (is_logged_in()): ?>
              <a href="auth/logout.php">Odjava</a>
            <?php else: ?>
              <a href="auth/login.php">Prijava</a>
              <a href="auth/register.php">Registracija</a>
            <?php endif; ?>
          </div>
        </li>
      </ul>
    </nav>

    <h1>Vremenski Podaci</h1>
    <div class="data-container">
      <table aria-labelledby="tablica-vremenskih-prognoza">
        <tr>
          <th>Temperature (°C)</th>
          <th>Humidity (%)</th>
          <th>Wind Speed (km/h)</th>
          <th>Precipitation (%)</th>
          <th>Cloud Cover</th>
          <th>Atmospheric Pressure</th>
          <th>UV Index</th>
          <th>Season</th>
          <th>Visibility (km)</th>
          <th>Location</th>
          <th>Weather Type</th>
        </tr>
        <?php
        // Ovdje možemo dodati dohvaćanje podataka iz baze ako je potrebno
        $weather_data = [
            [23, 69, 9.5, 10, 'clear', 1012.91, 9, 'Spring', 8.5, 'inland', 'Sunny'],
            [29, 82, 18, 78, 'overcast', 991.54, 3, 'Spring', 4, 'inland', 'Rainy'],
            // ... ostali podaci ...
        ];

        foreach ($weather_data as $row) {
            echo '<tr>';
            foreach ($row as $cell) {
                echo '<td>' . htmlspecialchars($cell) . '</td>';
            }
            echo '</tr>';
        }
        ?>
      </table>
    </div>
  </body>
</html> 