document.addEventListener('DOMContentLoaded', function() {
    let allMoviesData = [];
    let filteredData = [];
    let cartItems = [];
    
    fetch('../ajax/get_movies.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allMoviesData = data.movies;
                filteredData = [...allMoviesData];
                displayMoviesData(filteredData);
                populateFilterOptions();
            } else {
                console.error('Greška pri dohvaćanju filmova:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    
    // Prikaz podataka
    function displayMoviesData(data) {
        const tableBody = document.querySelector('#movies-table tbody');
        const noResults = document.getElementById('no-results');
        
        if (!data || data.length === 0) {
            tableBody.innerHTML = '';
            noResults.style.display = 'block';
            return;
        }
        
        noResults.style.display = 'none';
        tableBody.innerHTML = '';
        
        const displayData = data.slice(0, 25);
        
        const countInfo = document.getElementById('count-info');
        countInfo.textContent = `Prikazano ${displayData.length} od ${data.length} filmova`;
        
        displayData.forEach(movie => {
            const row = document.createElement('tr');
            
            row.innerHTML = `
                <td>${movie.title || 'N/A'}</td>
                <td>${movie.genre || 'N/A'}</td>
                <td>${movie.year || 'N/A'}</td>
                <td>${movie.duration || 'N/A'}</td>
                <td>${movie.avg_vote || 'N/A'}</td>
                <td>
                    <button class="action-button add-to-cart" data-movie-id="${movie.filmtv_id}">
                        U košaricu
                    </button>
                </td>
            `;
            
            tableBody.appendChild(row);
        });
        
        // Event listener za dodavanje u košaricu
        const addToCartButtons = document.querySelectorAll('.add-to-cart');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                const movieId = this.dataset.movieId;
                addToCart(movieId);
                this.classList.add('added-animation');
                setTimeout(() => {
                    this.classList.remove('added-animation');
                }, 500);
            });
        });
    }
    
    function populateFilterOptions() {
        const genreFilter = document.getElementById('genre-filter');
        const genres = new Set();
        
        allMoviesData.forEach(movie => {
            if (movie.genre) {
                genres.add(movie.genre);
            }
        });
        
        genres.forEach(genre => {
            const option = document.createElement('option');
            option.value = genre;
            option.textContent = genre;
            genreFilter.appendChild(option);
        });
        
        const countryFilter = document.getElementById('country-filter');
        const countries = new Set();
        
        allMoviesData.forEach(movie => {
            if (movie.country) {
                countries.add(movie.country);
            }
        });
        
        countries.forEach(country => {
            const option = document.createElement('option');
            option.value = country;
            option.textContent = country;
            countryFilter.appendChild(option);
        });
        
        const allYears = allMoviesData.map(movie => movie.year).filter(year => year);
        const minYear = Math.min(...allYears);
        const maxYear = Math.max(...allYears);
        
        const yearMin = document.getElementById('year-min');
        const yearMax = document.getElementById('year-max');
        
        yearMin.min = minYear;
        yearMin.max = maxYear;
        yearMin.placeholder = `Od (${minYear})`;
        
        yearMax.min = minYear;
        yearMax.max = maxYear;
        yearMax.placeholder = `Do (${maxYear})`;
    }
    
    function setupEventListeners() {
        const genreFilter = document.getElementById('genre-filter');
        const countryFilter = document.getElementById('country-filter');
        const yearMin = document.getElementById('year-min');
        const yearMax = document.getElementById('year-max');
        const ratingSlider = document.getElementById('rating-slider');
        const ratingValue = document.getElementById('rating-value');
        const resetButton = document.getElementById('reset-filters');
        
        ratingSlider.addEventListener('input', function() {
            ratingValue.textContent = this.value;
        });
        
        // Filtriranje podataka
        function applyFilters() {
            const selectedGenre = genreFilter.value;
            const selectedCountry = countryFilter.value;
            const minYearValue = yearMin.value ? parseInt(yearMin.value) : 0;
            const maxYearValue = yearMax.value ? parseInt(yearMax.value) : 9999;
            const minRating = parseFloat(ratingSlider.value);
            
            filteredData = allMoviesData.filter(movie => {
                // Žanr
                if (selectedGenre && movie.genre !== selectedGenre) return false;
                
                // Država
                if (selectedCountry && movie.country !== selectedCountry) return false;
                
                // Godina
                if (movie.year < minYearValue || movie.year > maxYearValue) return false;
                
                // Ocjena
                if (movie.avg_vote < minRating) return false;
                
                return true;
            });
            
            displayMoviesData(filteredData);
        }
        
        // Event listener za sve filtere
        genreFilter.addEventListener('change', applyFilters);
        countryFilter.addEventListener('change', applyFilters);
        yearMin.addEventListener('input', applyFilters);
        yearMax.addEventListener('input', applyFilters);
        ratingSlider.addEventListener('input', applyFilters);
        
        // Reset
        resetButton.addEventListener('click', function() {
            genreFilter.value = '';
            countryFilter.value = '';
            yearMin.value = '';
            yearMax.value = '';
            ratingSlider.value = 0;
            ratingValue.textContent = '0';
            
            filteredData = [...allMoviesData];
            displayMoviesData(filteredData);
        });
    }
    
    function updateCartCount(count) {
        document.getElementById('cart-count').textContent = count;
    }
    
    function updateCartItems(items) {
        const container = document.getElementById('cart-items-container');
        const emptyMessage = document.getElementById('empty-cart-message');
      
        if (!items || items.length === 0) {
            container.innerHTML = '<div id="empty-cart-message">Vaša košarica je prazna</div>';
            return;
        }
      
        let html = '';
        items.forEach(item => {
            html += `
                <div class="cart-item" data-movie-id="${item.filmtv_id}">
                    <div class="cart-item-info">
                        <h3>${item.title}</h3>
                        <p>${item.year} • ${item.duration} min</p>
                    </div>
                    <button class="remove-from-cart" data-movie-id="${item.filmtv_id}">
                        ✕
                    </button>
                </div>
            `;
        });
        
        container.innerHTML = html;

        document.querySelectorAll('.remove-from-cart').forEach(button => {
            button.addEventListener('click', function() {
                const movieId = this.dataset.movieId;
                removeFromCart(movieId);
            });
        });
    }

    function addToCart(movieId) {
        fetch('../ajax/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add&movie_id=${movieId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
                updateCartItems(data.cart_items);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function removeFromCart(movieId) {
        fetch('../ajax/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove&movie_id=${movieId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
                updateCartItems(data.cart_items);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    document.querySelectorAll('.add-to-wishlist').forEach(button => {
        button.addEventListener('click', function() {
            const movieId = this.dataset.movieId;
            addToCart(movieId);
        });
    });

    // Inicijalno učitavanje košarice
    fetch('../ajax/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cart_count);
            updateCartItems(data.cart_items);
        }
    })
    .catch(error => console.error('Error:', error));

    // Postavljanje event listenera za košaricu
    const cartIcon = document.getElementById('cart-icon');
    const cartSidebar = document.getElementById('cart-sidebar');
    const closeCart = document.getElementById('close-cart');
    const overlay = document.getElementById('overlay');
    const rentButton = document.getElementById('rent-button');

    // Otvaranje/zatvaranje košarice
    cartIcon.addEventListener('click', function() {
        if (cartSidebar.classList.contains('active')) {
            cartSidebar.classList.remove('active');
            overlay.classList.remove('active');
        } else {
            cartSidebar.classList.add('active');
            overlay.classList.add('active');
        }
    });

    closeCart.addEventListener('click', function() {
        cartSidebar.classList.remove('active');
        overlay.classList.remove('active');
    });

    overlay.addEventListener('click', function() {
        cartSidebar.classList.remove('active');
        overlay.classList.remove('active');
    });

    // Event listener za gumb za posudbu
    rentButton.addEventListener('click', function() {
        fetch('../ajax/process_rental.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                fetch('../ajax/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=get'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateCartCount(data.cart_count);
                        updateCartItems(data.cart_items);
                        cartSidebar.classList.remove('active');
                        overlay.classList.remove('active');
                    }
                });
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Došlo je do greške pri obradi posudbe.');
        });
    });

    displayMoviesData(allMoviesData);
    populateFilterOptions();
    setupEventListeners();
});