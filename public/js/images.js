// Function to load images from data-src
function loadImages(container = document) {
    container.querySelectorAll("img[data-src]").forEach(img => {
        img.src = img.dataset.src;
    });
}

// Load images on initial page load
document.addEventListener("DOMContentLoaded", () => {
    loadImages();
});

// Load images after HTMX content updates
document.body.addEventListener('htmx:afterSettle', function(evt) {
    // Load images in the newly inserted content
    loadImages(evt.target);
});