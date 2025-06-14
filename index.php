<?php
include 'db.php';

$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';

// --- MODIFIED: Use prepared statements for security ---
$sql = "SELECT * FROM movies WHERE title LIKE ?";
$search_param = "%$search%";

if ($filter == 'watched') {
  $sql .= " AND watched = 1";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $search_param);
} elseif ($filter == 'unwatched') {
  $sql .= " AND watched = 0";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $search_param);
} else {
  // No filter
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $search_param);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🎬 Movie Watchlist | Modern</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #6b46c1;
      --primary-dark: #553c9a;
      --background: #0f172a;
      --card-bg: #1e293b;
      --text: #f1f5f9;
      --text-secondary: #cbd5e1;
      --border: #334155;
      --success: #10b981;
      --danger: #ef4444;
      --warning: #f59e0b;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: var(--background);
      color: var(--text);
      min-height: 100vh;
      padding: 2rem 1rem;
      background: linear-gradient(135deg, var(--background) 0%, #1e293b 100%);
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
    }

    header {
      text-align: center;
      margin-bottom: 2.5rem;
      position: relative;
    }

    header h1 {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      background: linear-gradient(90deg, var(--primary), #8b5cf6);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      display: inline-block;
    }

    header p {
      color: var(--text-secondary);
      font-size: 1.1rem;
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.6;
    }

    .controls {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      justify-content: center;
      margin-bottom: 2rem;
      background: rgba(30, 41, 59, 0.7);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 1.5rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .search-form {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
    }

    .input-group {
      flex: 1;
      min-width: 250px;
    }

    .input-group input, 
    .input-group select {
      width: 100%;
      padding: 0.9rem 1.2rem;
      font-size: 1rem;
      background: rgba(15, 23, 42, 0.6);
      border: 1px solid var(--border);
      border-radius: 12px;
      color: var(--text);
      outline: none;
      transition: all 0.3s ease;
    }

    .input-group input:focus, 
    .input-group select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.3);
    }

    .search-form button {
      background: var(--primary);
      color: white;
      border: none;
      padding: 0.9rem 1.8rem;
      border-radius: 12px;
      cursor: pointer;
      font-size: 1rem;
      font-weight: 600;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .search-form button:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(107, 70, 193, 0.4);
    }

    .add-link {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: var(--success);
      color: white;
      text-decoration: none;
      padding: 0.8rem 1.5rem;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    }

    .add-link:hover {
      background: #059669;
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4);
    }

    .movie-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 2rem;
      margin-top: 2rem;
    }

    .movie-card {
      background: var(--card-bg);
      border-radius: 16px;
      overflow: hidden;
      transition: all 0.3s ease;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
      border: 1px solid var(--border);
    }

    .movie-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
      border-color: var(--primary);
    }

    .poster-container {
      height: 300px;
      overflow: hidden;
      position: relative;
    }

    .poster-container img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .movie-card:hover .poster-container img {
      transform: scale(1.05);
    }

    .status-badge {
      position: absolute;
      top: 1rem;
      right: 1rem;
      background: rgba(0, 0, 0, 0.7);
      padding: 0.4rem 0.8rem;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .watched {
      background: rgba(16, 185, 129, 0.8);
    }

    .unwatched {
      background: rgba(239, 68, 68, 0.8);
    }

    .movie-details {
      padding: 1.5rem;
    }

    .movie-title {
      font-size: 1.4rem;
      margin-bottom: 0.8rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .movie-title .rating {
      display: flex;
      align-items: center;
      gap: 0.3rem;
      background: rgba(245, 158, 11, 0.2);
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
      color: #fbbf24;
    }

    .movie-genre {
      display: inline-block;
      background: rgba(107, 70, 193, 0.2);
      color: var(--primary);
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .movie-review {
      color: var(--text-secondary);
      line-height: 1.6;
      margin-bottom: 1.5rem;
      font-size: 0.95rem;
    }

    .movie-actions {
      display: flex;
      gap: 0.8rem;
      border-top: 1px solid var(--border);
      padding-top: 1.2rem;
    }

    .action-btn {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.6rem;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.08);
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }

    .action-btn:hover {
      background: rgba(107, 70, 193, 0.2);
      color: var(--text);
    }

    .toggle-btn:hover {
      background: rgba(16, 185, 129, 0.2);
    }

    .edit-btn:hover {
      background: rgba(59, 130, 246, 0.2);
    }

    .delete-btn:hover {
      background: rgba(239, 68, 68, 0.2);
    }

    .empty-state {
      grid-column: 1 / -1;
      text-align: center;
      padding: 4rem 2rem;
      background: rgba(30, 41, 59, 0.5);
      border-radius: 16px;
      margin-top: 2rem;
    }

    .empty-state i {
      font-size: 3.5rem;
      margin-bottom: 1.5rem;
      color: var(--text-secondary);
    }

    .empty-state h3 {
      font-size: 1.8rem;
      margin-bottom: 1rem;
      color: var(--text);
    }

    .empty-state p {
      color: var(--text-secondary);
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.6;
    }

    @media (max-width: 768px) {
      .movie-grid {
        grid-template-columns: 1fr;
      }
      
      .controls {
        padding: 1rem;
      }
      
      .search-form {
        flex-direction: column;
      }
      
      header h1 {
        font-size: 2.2rem;
      }
    }
  </style>
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
          <select name="filter" onchange="this.form.submit()">
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
          <?php
            // --- MODIFIED: Preserve search/filter in action URLs ---
            $query_params = http_build_query([
                'search' => $search,
                'filter' => $filter
            ]);
            
            $toggle_url = 'toggle.php?id=' . $row['id'] . '&' . $query_params;
            $edit_url = 'edit.php?id=' . $row['id'] . '&' . $query_params;
            $delete_url = 'delete.php?id=' . $row['id'] . '&' . $query_params;
          ?>
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
                <a href="<?= $toggle_url ?>" class="action-btn toggle-btn">
                  <i class="fas fa-sync-alt"></i> Toggle
                </a>
                <a href="<?= $edit_url ?>" class="action-btn edit-btn">
                  <i class="fas fa-edit"></i> Edit
                </a>
                <a href="<?= $delete_url ?>" class="action-btn delete-btn" onclick="return confirm('Delete this movie?');">
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
        }, 100 * (index % 10)); // Use modulo to keep animation fast on long lists
      });
    });
  </script>
</body>
</html>