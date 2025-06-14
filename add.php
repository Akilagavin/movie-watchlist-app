<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'];
  $genre = $_POST['genre'];
  $poster = $_POST['poster'];
  $rating = $_POST['rating'];
  $review = $_POST['review'];

  $stmt = $conn->prepare("INSERT INTO movies (title, genre, poster_url, rating, review) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssds", $title, $genre, $poster, $rating, $review);
  $stmt->execute();

  header("Location: index.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add New Movie | Movie Watchlist</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="add.css" />
</head>
<body>
  <div class="container">
    <header>
      <h1>🎬 Add New Movie</h1>
    </header>
    
    <div class="form-container">
      <form method="POST" class="movie-form">
        <div class="input-group">
          <label for="title">Movie Title</label>
          <input type="text" name="title" placeholder="Enter movie title" required>
        </div>
        
        <div class="input-group">
          <label for="genre">Genre</label>
          <input type="text" name="genre" placeholder="e.g., Action, Drama, Comedy">
        </div>
        
        <div class="input-group">
          <label for="poster">Poster URL</label>
          <input type="text" name="poster" placeholder="https://example.com/poster.jpg">
        </div>
        
        <div class="input-group">
          <label for="rating">Rating (1-10)</label>
          <input type="number" name="rating" min="0" max="10" step="0.1" placeholder="0.0">
        </div>
        
        <div class="input-group">
          <label for="review">Review</label>
          <textarea name="review" placeholder="Your thoughts about the movie"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-plus"></i> Add Movie
        </button>
      </form>
      
      <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Watchlist
      </a>
    </div>
  </div>
</body>
</html>
