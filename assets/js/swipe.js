document.addEventListener('DOMContentLoaded', function() {
    const breadDisplay = document.querySelector('.bread-display');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    let currentBreadIndex = parseInt(new URLSearchParams(window.location.search).get('bread')) || 0;
    const totalBreads = document.querySelectorAll('.bread-card').length;

    // Handle button navigation
    prevBtn.addEventListener('click', () => navigateBread(-1));
    nextBtn.addEventListener('click', () => navigateBread(1));

    // Touch swipe for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    breadDisplay.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, false);

    breadDisplay.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, false);

    function handleSwipe() {
        if (touchEndX < touchStartX - 50) {
            // Swipe left - next bread
            navigateBread(1);
        } else if (touchEndX > touchStartX + 50) {
            // Swipe right - previous bread
            navigateBread(-1);
        }
    }

    function navigateBread(direction) {
        const newIndex = currentBreadIndex + direction;
        
        // Check bounds
        if (newIndex >= 0 && newIndex < totalBreads) {
            currentBreadIndex = newIndex;
            window.location.href = `dashboard.php?bread=${currentBreadIndex}`;
        }
    }
});