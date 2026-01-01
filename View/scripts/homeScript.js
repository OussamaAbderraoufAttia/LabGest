// Carousel functionality
let currentSlide = 0;
let slides = [];
let carouselInterval;

// Load carousel data
$(document).ready(function () {
    loadCarousel();
    loadNews();
    loadPartners();
    startCarousel();
});

function loadCarousel() {
    $.ajax({
        url: 'index.php?router=get-carousel',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            slides = data;
            if (slides.length > 0) {
                updateSlide(0);
                updateIndicators();
            }
        },
        error: function () {
            console.error('Failed to load carousel data');
            // Fallback data
            slides = [
                { titre: 'Bienvenue au LRE', description_courte: 'Découvrez nos dernières recherches en IA' },
                { titre: 'Recherche d\'Excellence', description_courte: 'Innovation et collaboration' },
                { titre: 'Événements 2025', description_courte: 'Conférences et ateliers à venir' }
            ];
            updateSlide(0);
            updateIndicators();
        }
    });
}

function updateSlide(index) {
    if (slides.length === 0) return;

    currentSlide = index;
    const slide = slides[index];

    // Update background image class
    $('.hero-slider').removeClass('slide-0 slide-1 slide-2').addClass('slide-' + index);

    $('#slideTitle').fadeOut(300, function () {
        $(this).text(slide.titre || 'Laboratoire de Recherche ESI').fadeIn(300);
    });

    $('#slideDescription').fadeOut(300, function () {
        $(this).text(slide.description_courte || 'Excellence en recherche informatique').fadeIn(300);
    });

    // Update indicators
    $('.indicator').removeClass('active');
    $('.indicator').eq(index).addClass('active');
}

function updateIndicators() {
    const indicatorsContainer = $('#sliderIndicators');
    indicatorsContainer.empty();

    for (let i = 0; i < slides.length; i++) {
        const indicator = $('<span></span>')
            .addClass('indicator')
            .addClass(i === 0 ? 'active' : '')
            .click(function () {
                updateSlide(i);
                restartCarousel();
            });
        indicatorsContainer.append(indicator);
    }
}

function startCarousel() {
    carouselInterval = setInterval(function () {
        const nextSlide = (currentSlide + 1) % slides.length;
        updateSlide(nextSlide);
    }, 5000); // 5 seconds as required
}

function restartCarousel() {
    clearInterval(carouselInterval);
    startCarousel();
}

// Load news/events
function loadNews() {
    $.ajax({
        url: 'index.php?router=get-news',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            const newsGrid = $('#newsGrid');
            newsGrid.empty();

            if (data.length === 0) {
                newsGrid.append('<p style="text-align: center; color: #666;">Aucune actualité disponible pour le moment.</p>');
                return;
            }

            data.forEach(function (item) {
                const card = $('<div></div>').addClass('news-card fade-in-up');
                const date = new Date(item.date_event);
                const formattedDate = date.toLocaleDateString('fr-FR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                card.html(`
                    <h4>${item.titre}</h4>
                    <p>${item.description ? item.description.substring(0, 150) + '...' : ''}</p>
                    <span class="date"><i class="fa-solid fa-calendar"></i> ${formattedDate}</span>
                `);

                newsGrid.append(card);
            });
        },
        error: function () {
            console.error('Failed to load news');
        }
    });
}

// Load partners (mock data for now)
function loadPartners() {
    const partners = [
        'Sonatrach',
        'MESRS',
        'USTHB',
        'CERIST',
        'Algérie Télécom',
        'Condor Electronics'
    ];

    const partnersTrack = $('#partnersTrack');
    partnersTrack.empty();

    // Duplicate for infinite scroll effect
    const allPartners = [...partners, ...partners];

    allPartners.forEach(function (partner) {
        const logo = $('<div></div>')
            .addClass('partner-logo')
            .css({
                'display': 'flex',
                'align-items': 'center',
                'justify-content': 'center',
                'font-weight': '700',
                'font-size': '1.2rem',
                'color': '#667eea'
            })
            .text(partner);

        partnersTrack.append(logo);
    });
}

// Load events in events section
function loadEvents() {
    $.ajax({
        url: 'index.php?router=get-events', // Corrected endpoint
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            const eventsGrid = $('#eventsGrid');
            eventsGrid.empty();

            if (data.length === 0) {
                eventsGrid.append('<p style="text-align: center; color: #666;">Aucun événement à venir.</p>');
                return;
            }

            data.slice(0, 6).forEach(function (event) {
                const date = new Date(event.date_event);
                const day = date.getDate();
                const month = date.toLocaleDateString('fr-FR', { month: 'short' });

                const card = $('<div></div>').addClass('event-card fade-in-up');

                card.html(`
                    <div class="event-date">
                        <div class="day">${day}</div>
                        <div class="month">${month.toUpperCase()}</div>
                    </div>
                    <div class="event-content">
                        <h4>${event.titre}</h4>
                        <p><i class="fa-solid fa-location-dot"></i> ${event.lieu || 'ESI'}</p>
                        <span class="event-type">${event.type || 'Événement'}</span>
                    </div>
                `);

                eventsGrid.append(card);
            });
        },
        error: function () {
            console.error('Failed to load events');
        }
    });
}

// Initialize events loading
$(document).ready(function () {
    loadEvents();
});
