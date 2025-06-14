
 Core Features
1.Movie Management
   - Add new movies with title, genre, poster URL, rating, and review
   - Edit existing movies (update rating and review)
   - Delete movies from the watchlist
   - Toggle watched/unwatched status

2.Viewing & Organization
   - View all movies in a responsive card-based grid
   - Filter movies by:
     - All movies
     - Watched only
     - Unwatched only
   - Search movies by title
   - Sort movies visually with animations

3.Visual Presentation
   - Movie cards with:
     - Poster images
     - Title
     - Genre tags
     - Star ratings (0-10)
     - Watched/unwatched status badges
     - User reviews
   - Dark theme with purple/pink accents
   - Hover animations and transitions
   - Responsive design (mobile-friendly)

Technical Components
1. Database
   - MySQL database (`movie_watchlist`)
   - Table structure: 
     ```sql
     movies(id, title, genre, poster_url, rating, review, watched)
     ```

2. Key Pages
   - `index.php` - Main dashboard
   - `add.php` - Add new movie form
   - `edit.php` - Edit movie details
   - `delete.php` - Delete movie handler
   - `toggle.php` - Toggle watched status

3. Modern UI Elements
   - Glassmorphism effect (frosted glass panels)
   - Animated card entrances
   - Interactive rating slider (edit page)
   - Status badges with color coding:
     - Watched: Green  
     - Unwatched: Red 
   - Gradient text headers
   - Font Awesome icons throughout
   - Hover effects on all interactive elements

 Security Features
1. Input sanitization (`htmlspecialchars()`)
2. Confirmation prompts for deletions
3. Form validation (rating limits)
4. Prepared statements for database operations

 User Experience
1. Empty state handling (when no movies match search)
2. Smooth animations and transitions
3. Visual feedback on interactions
4. Consistent design language across all pages
5. Clear status indicators
6. Responsive layout for all device sizes

The application allows users to maintain a personal movie database where they can track which films they've watched, rate them, add personal reviews, and organize their viewing experience with search/filter capabilities - all presented in a modern dark-themed interface.