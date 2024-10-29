document.addEventListener('DOMContentLoaded', function() {
    const movieLinks = document.querySelectorAll('.show-details');
    const modalElement = document.getElementById('movieDetailsModal');

    movieLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const movieId = this.getAttribute('data-movie-id');
            fetch(`/movie/${movieId}/details`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('movieDetailsContent').innerHTML = html;
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                });
        });
    });

    // Initialisation du modal
    modalElement.addEventListener('show.bs.modal', function (event) {
    });
});