<?php
include 'db.php';
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $rating = $_POST['rating'];
  $review = $_POST['review'];
  $stmt = $conn->prepare("UPDATE movies SET rating = ?, review = ? WHERE id = ?");
  $stmt->bind_param("dsi", $rating, $review, $id);
  $stmt->execute();
  header("Location: index.php");
  exit;
}

$result = $conn->query("SELECT * FROM movies WHERE id = $id");
$movie = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Movie | Movie Watchlist</title>
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
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      width: 100%;
    }

    header {
      text-align: center;
      margin-bottom: 2.5rem;
      position: relative;
    }

    header h1 {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      background: linear-gradient(90deg, var(--primary), #8b5cf6);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      display: inline-block;
    }

    .movie-card {
      background: var(--card-bg);
      border-radius: 16px;
      overflow: hidden;
      transition: all 0.3s ease;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
      border: 1px solid var(--border);
      margin-bottom: 2rem;
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

    .movie-details {
      padding: 1.5rem;
    }

    .movie-title {
      font-size: 1.8rem;
      margin-bottom: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .movie-title .rating {
      display: flex;
      align-items: center;
      gap: 0.3rem;
      background: rgba(245, 158, 11, 0.2);
      padding: 0.5rem 1rem;
      border-radius: 20px;
      color: #fbbf24;
      font-size: 1.2rem;
    }

    .movie-genre {
      display: inline-block;
      background: rgba(107, 70, 193, 0.2);
      color: var(--primary);
      padding: 0.5rem 1.2rem;
      border-radius: 20px;
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
    }

    .form-container {
      background: rgba(30, 41, 59, 0.7);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
      border: 1px solid var(--border);
    }

    .input-group {
      margin-bottom: 1.5rem;
    }

    .input-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--text-secondary);
    }

    .input-group input, 
    .input-group textarea,
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

    .input-group textarea {
      min-height: 150px;
      resize: vertical;
    }

    .input-group input:focus, 
    .input-group textarea:focus,
    .input-group select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.3);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.9rem 1.8rem;
      border-radius: 12px;
      cursor: pointer;
      font-size: 1rem;
      font-weight: 600;
      transition: all 0.3s ease;
      text-decoration: none;
      border: none;
      margin-top: 0.5rem;
      width: 100%;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(107, 70, 193, 0.4);
    }

    .btn-secondary {
      background: rgba(255, 255, 255, 0.08);
      color: var(--text);
    }

    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: translateY(-2px);
    }

    .rating-slider {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-top: 0.5rem;
    }

    .rating-slider input[type="range"] {
      flex: 1;
      height: 8px;
      -webkit-appearance: none;
      background: rgba(15, 23, 42, 0.6);
      border-radius: 4px;
      outline: none;
    }

    .rating-slider input[type="range"]::-webkit-slider-thumb {
      -webkit-appearance: none;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: var(--primary);
      cursor: pointer;
    }

    .rating-value {
      font-size: 1.2rem;
      font-weight: bold;
      min-width: 40px;
      text-align: center;
      color: var(--warning);
    }

    @media (max-width: 768px) {
      header h1 {
        font-size: 2.2rem;
      }
      
      .form-container {
        padding: 1.5rem;
      }
      
      .movie-title {
        font-size: 1.5rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.8rem;
      }
      
      .movie-title .rating {
        padding: 0.4rem 0.8rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <h1>🎬 Edit Movie</h1>
    </header>
    
    <div class="movie-card">
      <div class="poster-container">
        <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="Movie poster for <?= htmlspecialchars($movie['title']) ?>">
      </div>
      
      <div class="movie-details">
        <div class="movie-title">
          <span><?= htmlspecialchars($movie['title']) ?></span>
          <span class="rating">
            <i class="fas fa-star"></i> <?= htmlspecialchars($movie['rating']) ?>/10
          </span>
        </div>
        
        <div class="movie-genre">
          <?= htmlspecialchars($movie['genre']) ?>
        </div>
      </div>
    </div>
    
    <div class="form-container">
      <form method="POST" class="movie-form">
        <div class="input-group">
          <label for="rating">Rating (1-10)</label>
          <input type="number" id="rating" name="rating" min="0" max="10" step="0.1" 
                 value="<?= htmlspecialchars($movie['rating']) ?>" 
                 placeholder="0.0" required>
          
          <div class="rating-slider">
            <input type="range" id="rating-slider" min="0" max="10" step="0.5" 
                   value="<?= htmlspecialchars($movie['rating']) ?>">
            <span class="rating-value"><?= htmlspecialchars($movie['rating']) ?></span>
          </div>
        </div>
        
        <div class="input-group">
          <label for="review">Review</label>
          <textarea id="review" name="review" 
                    placeholder="Your thoughts about the movie"><?= htmlspecialchars($movie['review']) ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Update Movie
        </button>
      </form>
      
      <a href="index.php" class="btn btn-secondary" style="margin-top: 1rem;">
        <i class="fas fa-arrow-left"></i> Back to Watchlist
      </a>
    </div>
  </div>
  
  <script>
    // Update rating value when slider changes
    const ratingSlider = document.getElementById('rating-slider');
    const ratingInput = document.getElementById('rating');
    const ratingValue = document.querySelector('.rating-value');
    
    if (ratingSlider && ratingInput) {
      ratingSlider.addEventListener('input', function() {
        ratingInput.value = this.value;
        ratingValue.textContent = this.value;
      });
      
      ratingInput.addEventListener('input', function() {
        ratingSlider.value = this.value;
        ratingValue.textContent = this.value;
      });
    }
    
    // Form validation
    document.querySelector('.movie-form').addEventListener('submit', function(e) {
      const rating = parseFloat(document.getElementById('rating').value);
      
      if (isNaN(rating) || rating < 0 || rating > 10) {
        alert('Please enter a valid rating between 0 and 10');
        e.preventDefault();
      }
    });
  </script>
</body>
</html>