<?php
include 'db.php';

// Preserve search and filter parameters for redirect
$redirect_params = [];
if (!empty($_GET['search'])) {
    $redirect_params['search'] = $_GET['search'];
}
if (!empty($_GET['filter'])) {
    $redirect_params['filter'] = $_GET['filter'];
}
$redirect_url = 'index.php';
if (!empty($redirect_params)) {
    $redirect_url .= '?' . http_build_query($redirect_params);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Use a prepared statement to prevent SQL injection
    // First, get the current 'watched' status
    $stmt = $conn->prepare("SELECT watched FROM movies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $movie = $result->fetch_assoc();
        // Flip the boolean value (0 becomes 1, 1 becomes 0)
        $new_status = !$movie['watched']; 
        $new_status_int = (int)$new_status;
        
        // Update the database with the new status
        $update_stmt = $conn->prepare("UPDATE movies SET watched = ? WHERE id = ?");
        $update_stmt->bind_param("ii", $new_status_int, $id);
        $update_stmt->execute();
    }
}

// Redirect back to the main page with original search/filter
header("Location: " . $redirect_url);
exit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Movie Watchlist</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="toggle.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>🎬 Movie Watchlist</h1>
      <p>Track all your watched and unwatched movies in one place. Rate them and add personal reviews.</p>
    </header>
    
    <div class="controls">
      <form method="GET" class="search-form">
        <div class="input-group">
          <input type="text" name="search" placeholder="Search movies..." value="<?= htmlspecialchars($search) ?>">
        </div>
        
        <div class="input-group">
          <select name="filter">
            <option value="">All Movies</option>
            <option value="watched" <?= $filter == 'watched' ? 'selected' : '' ?>>Watched</option>
            <option value="unwatched" <?= $filter == 'unwatched' ? 'selected' : '' ?>>Unwatched</option>
          </select>
        </div>
        
        <button type="submit">
          <i class="fas fa-search"></i> Search
        </button>
      </form>
    </div>
    
    <div style="text-align: center; margin-bottom: 2rem;">
      <a class="add-link" href="add.php">
        <i class="fas fa-plus"></i> Add New Movie
      </a>
    </div>
    
    <div class="movie-grid">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="movie-card">
            <div class="poster-container">
              <img src="<?= htmlspecialchars($row['poster_url']) ?>" alt="Movie poster for <?= htmlspecialchars($row['title']) ?>">
              <div class="status-badge <?= $row['watched'] ? 'watched' : 'unwatched' ?>">
                <i class="fas <?= $row['watched'] ? 'fa-check' : 'fa-times' ?>"></i>
                <?= $row['watched'] ? 'Watched' : 'Unwatched' ?>
              </div>
            </div>
            
            <div class="movie-details">
              <div class="movie-title">
                <span><?= htmlspecialchars($row['title']) ?></span>
                <span class="rating">
                  <i class="fas fa-star"></i> <?= htmlspecialchars($row['rating']) ?>/10
                </span>
              </div>
              
              <div class="movie-genre">
                <?= htmlspecialchars($row['genre']) ?>
              </div>
              
              <p class="movie-review">
                <?= htmlspecialchars($row['review']) ?>
              </p>
              
              <div class="movie-actions">
                <a href="toggle-script.php?id=<?= $row['id'] ?>" class="action-btn toggle-btn">
                  <i class="fas fa-sync-alt"></i> Toggle
                </a>
                <a href="edit.php?id=<?= $row['id'] ?>" class="action-btn edit-btn">
                  <i class="fas fa-edit"></i> Edit
                </a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Delete this movie?');">
                  <i class="fas fa-trash"></i> Delete
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-film"></i>
          <h3>No Movies Found</h3>
          <p>Try adjusting your search or add a new movie to your watchlist.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
  
  <script>
    // Add subtle animations to elements
    document.addEventListener('DOMContentLoaded', function() {
      const cards = document.querySelectorAll('.movie-card');
      
      cards.forEach((card, index) => {
        setTimeout(() => {
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, 100 * index);
      });
    });
  </script>
</body>
</html>