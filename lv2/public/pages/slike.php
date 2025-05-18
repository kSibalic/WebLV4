<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../auth/check_session.php';

require_login();

$images = get_all_images();
?>
<!DOCTYPE html>
<html lang="hr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="Karlo Sibalic">
		<meta name="description" content="LV1 iz kolegija Web programiranje">
		<meta name="keywords" content="HTML, CSS, FERIT">
		<link rel="stylesheet" href="../style/style_slike.css">
        <link rel="stylesheet" href="../style/style.css">
		<title>LV1 - Web programiranje</title> 
	</head>
<body>
    <nav aria-labelledby="primarna-navigacija">
        <ul>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn">Menu</a>
                <div class="dropdown-content" id="primarna-navigacija">
                    <a href="../index.php">Pocetna</a>
                    <a href="grafikon.php">Grafikon</a>
                    <a href="#">Galerija</a>
                    <a href="videoteka.php">Videoteka</a>
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

    <section class="galerija">
        <h1>Galerija slika</h1>
        <div class="img-gallery-magnific">
            <?php foreach ($images as $index => $image): ?>
            <div class="magnific-img">
                <a href="#slika<?php echo $index + 1; ?>">
                    <img src="../pictures/<?php echo htmlspecialchars($image['filename']); ?>" 
                         alt="<?php echo htmlspecialchars($image['title']); ?>" 
                         loading="lazy">
                </a>
                <div class="img-description">
                    <h3><?php echo htmlspecialchars($image['title']); ?></h3>
                    <?php if ($image['avg_rating']): ?>
                        <div class="rating-stars">
                            <?php
                            $rating = round($image['avg_rating']);
                            for ($i = 1; $i <= 5; $i++) {
                                echo '<i class="fas fa-star star ' . ($i <= $rating ? 'filled' : '') . '"></i>';
                            }
                            ?>
                            <span class="rating-count">
                                (<?php echo number_format($image['avg_rating'], 1); ?> / <?php echo $image['rating_count']; ?> ocjena)
                            </span>
                        </div>
                    <?php endif; ?>
                    <?php if (is_logged_in()): ?>
                    <form class="rating-form" action="../rate_image.php" method="POST">
                        <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <input type="radio" name="rating" value="<?php echo $i; ?>" 
                               id="rating_<?php echo $image['id']; ?>_<?php echo $i; ?>">
                        <label for="rating_<?php echo $image['id']; ?>_<?php echo $i; ?>">
                            <?php echo $i; ?>★
                        </label>
                        <?php endfor; ?>
                        <button type="submit">Ocijeni</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php foreach ($images as $index => $image): ?>
    <div id="slika<?php echo $index + 1; ?>" class="lightbox">
        <a href="#" class="close">&times;</a>
        <img src="../pictures/<?php echo htmlspecialchars($image['filename']); ?>" 
             alt="<?php echo htmlspecialchars($image['title']); ?>">
        <div class="lightbox-content">
            <h3><?php echo htmlspecialchars($image['title']); ?></h3>
            <p><?php echo htmlspecialchars($image['description']); ?></p>
            <p>Autor: <?php echo htmlspecialchars($image['author']); ?></p>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html> 