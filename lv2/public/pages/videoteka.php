<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../auth/check_session.php';

require_login();

$total_movies = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();

$movies = get_all_movies();
$genres = array_unique(array_column($movies, 'genre'));
$countries = array_unique(array_column($movies, 'country'));
?>
<!DOCTYPE html>
<html lang="hr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Karlo Sibalic" />
    <meta name="description" content="LV1 iz kolegija Web programiranje" />
    <meta name="keywords" content="HTML, CSS, FERIT" />
    <link rel="stylesheet" href="../style/style.css">
    <title>LV3 - Web programiranje</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
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
            <a href="../index.php">Pocetna</a>
            <a href="grafikon.php">Grafikon</a>
            <a href="slike.php">Galerija</a>
            <a href="#">Videoteka</a>
            <?php if (is_logged_in()): ?>
                <a href="../auth/logout.php">Odjava</a>
            <?php else: ?>
                <a href="../auth/login.php">Prijava</a>
                <a href="../auth/register.php">Registracija</a>
            <?php endif; ?>
          </div>
        </li>
      </ul>
    </nav>

    <h1>LV3 - Online Videoteka</h1>
    
    <!-- Filteri -->
    <div class="filters-container">
      <h3>Filtriraj filmove</h3>
      <div class="filter-row">
        <div class="filter-group">
          <label for="genre-filter">Žanr:</label>
          <select id="genre-filter">
            <option value="">Svi žanrovi</option>
            <?php foreach ($genres as $genre): ?>
            <option value="<?php echo htmlspecialchars($genre); ?>"><?php echo htmlspecialchars($genre); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="filter-group">
          <label for="country-filter">Država:</label>
          <select id="country-filter">
            <option value="">Sve države</option>
            <?php foreach ($countries as $country): ?>
            <option value="<?php echo htmlspecialchars($country); ?>"><?php echo htmlspecialchars($country); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="filter-group">
          <label for="year-range">Godina (od-do):</label>
          <div class="year-range-container">
            <input type="number" id="year-min" placeholder="Od" min="1900" max="2025">
            <span>-</span>
            <input type="number" id="year-max" placeholder="Do" min="1900" max="2025">
          </div>
        </div>
      </div>
      
      <div class="filter-row">
        <div class="filter-group">
          <label for="rating-slider">Minimalna ocjena: <span id="rating-value">0</span></label>
          <input type="range" id="rating-slider" min="0" max="10" step="0.1" value="0">
        </div>
        
        <div class="filter-group">
          <button id="reset-filters">Poništi filtere</button>
        </div>
      </div>
    </div>
    
    <div class="data-container">
      <div id="count-info" class="count-info">Prikazano <?php echo count($movies); ?> od <?php echo $total_movies; ?> filmova</div>
      <table id="movies-table" aria-labelledby="tablica-filmova">
        <thead>
          <tr>
            <th>Naslov filma</th>
            <th>Žanr</th>
            <th>Godina</th>
            <th>Trajanje (min)</th>
            <th>Ocjena</th>
            <th>Akcija</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($movies as $movie): ?>
          <tr data-movie-id="<?php echo $movie['filmtv_id']; ?>">
            <td><?php echo htmlspecialchars($movie['title']); ?></td>
            <td><?php echo htmlspecialchars($movie['genre']); ?></td>
            <td><?php echo htmlspecialchars($movie['year']); ?></td>
            <td><?php echo htmlspecialchars($movie['duration']); ?></td>
            <td><?php echo htmlspecialchars($movie['avg_vote']); ?></td>
            <td>
              <button class="add-to-wishlist" data-movie-id="<?php echo $movie['filmtv_id']; ?>">
                Dodaj u listu želja
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div id="no-results" style="display: none;">Nema rezultata koji odgovaraju odabranim filterima.</div>
    </div>

    <div class="cart-icon" id="cart-icon">
      🛒
      <span class="cart-count" id="cart-count">0</span>
    </div>

    <!-- Košarica -->
    <div class="cart-sidebar" id="cart-sidebar">
      <div class="cart-header">
        <h2>Vaša košarica</h2>
        <span class="close-cart" id="close-cart">&times;</span>
      </div>
      <div id="cart-items-container">
        <div class="empty-cart-message" id="empty-cart-message">
          Vaša košarica je prazna.
        </div>
      </div>
      <button class="rent-button" id="rent-button">Posudi filmove</button>
    </div>

    <div class="overlay" id="overlay"></div>

    <footer aria-labelledby="podnozje">
      <p>&copy; 2025. Web Programiranje. Sva prava pridrzana.</p>
    </footer>

    <script>
      const movies = <?php echo json_encode($movies); ?>;
    </script>
    <script src="../scripts/script.js"></script>
  </body>
</html> 