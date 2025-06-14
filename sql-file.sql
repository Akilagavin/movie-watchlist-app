CREATE DATABASE movie_watchlist;

USE movie_watchlist;

CREATE TABLE movies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  genre VARCHAR(100),
  poster_url TEXT,
  watched BOOLEAN DEFAULT FALSE,
  rating INT,
  review TEXT
);
