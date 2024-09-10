let slideIndex = 1;

const container = document.querySelector(".carousel-inner-main");
const slides = container.querySelectorAll(".carousel-item-main");
const thumbnailCarousel = document.getElementById('thumbnailCarousel');
const thumbnails = thumbnailCarousel.querySelectorAll('.clickable-thumbnail');
const prevButton = document.querySelector('#productImageCarousel .carousel-control-prev');
const nextButton = document.querySelector('#productImageCarousel .carousel-control-next');

const totalSlides = slides.length;
const originalSlidesCount = totalSlides - 2;

function showSlides(index) {
    slideIndex = index;
    
    if (slideIndex >= totalSlides - 1) {
        container.style.transition = 'none';
        slideIndex = 0;
        container.style.transform = `translateX(${-slideIndex * 100}%)`;
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                container.style.transition = 'transform 0.3s ease-in-out';
                slideIndex++;
                container.style.transform = `translateX(${-slideIndex * 100}%)`;
                thumbnails.forEach((thumb, i) => {
                    thumb.classList.toggle("selected", i === ((slideIndex - 1) % originalSlidesCount));
                });
            });
        });
    } else if (slideIndex <= 0) { 
        container.style.transition = 'none';
        slideIndex = totalSlides - 1;
        container.style.transform = `translateX(${-slideIndex * 100}%)`;
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                container.style.transition = 'transform 0.3s ease-in-out';
                slideIndex--;
                container.style.transform = `translateX(${-slideIndex * 100}%)`;
                thumbnails.forEach((thumb, i) => {
                    thumb.classList.toggle("selected", i === ((slideIndex - 1) % originalSlidesCount));
                });
            });
        });
    } else {
        container.style.transition = 'transform 0.3s ease-in-out';
        container.style.transform = `translateX(${-slideIndex * 100}%)`;
        thumbnails.forEach((thumb, i) => {
            thumb.classList.toggle("selected", i === ((slideIndex - 1) % originalSlidesCount));
        });
    }
}

prevButton.addEventListener('click', () => {
    showSlides(slideIndex - 1);
});

nextButton.addEventListener('click', () => {
    showSlides(slideIndex + 1);
});

thumbnails.forEach((thumbnail, index) => {
    thumbnail.addEventListener('click', () => {
        showSlides(index + 1);
    });
});

showSlides(slideIndex);

const quantityInput = document.getElementById('quantity');
const increaseBtn = document.getElementById('increaseQty');
const decreaseBtn = document.getElementById('decreaseQty');
const flavorIdInput = document.getElementById('flavorId');
const flavorButtons = document.querySelectorAll('.flavor-button');

function updateQuantityLimit() {
    const selectedFlavor = document.querySelector('.flavor-button.selected');
    const maxQuantity = parseInt(selectedFlavor.dataset.stock, 10);
    const currentQuantity = parseInt(quantityInput.value, 10);

    if (currentQuantity > maxQuantity) {
        quantityInput.value = maxQuantity;
    }
}

increaseBtn.addEventListener('click', function () {
    let currentValue = parseInt(quantityInput.value);
    const selectedFlavor = document.querySelector('.flavor-button.selected');
    const maxQuantity = parseInt(selectedFlavor.dataset.stock, 10);

    if (!isNaN(currentValue) && currentValue < maxQuantity) {
        quantityInput.value = currentValue + 1;
    }
});

decreaseBtn.addEventListener('click', function () {
    let currentValue = parseInt(quantityInput.value);
    if (!isNaN(currentValue) && currentValue > 1) {
        quantityInput.value = currentValue - 1;
    }
});

quantityInput.addEventListener('input', function () {
    let currentValue = parseInt(quantityInput.value);
    const selectedFlavor = document.querySelector('.flavor-button.selected');
    const maxQuantity = parseInt(selectedFlavor.dataset.stock, 10);

    if (isNaN(currentValue) || currentValue < 1) {
        quantityInput.value = 1;
    } else if (currentValue > maxQuantity) {
        quantityInput.value = maxQuantity;
    }
});

quantityInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        updateQuantityLimit();
    }
});

flavorButtons.forEach(button => {
    button.addEventListener('click', function () {
        flavorButtons.forEach(btn => btn.classList.remove('selected'));
        this.classList.add('selected');
        flavorIdInput.value = this.dataset.flavorId;
        updateQuantityLimit();
    });
});
const toggle = document.querySelector('.accordion-description-toggle');
const additionalInfo = document.querySelector('.accordion-additional-information');
const icon = document.querySelector('.accordion-description-toggle .icon-toggle');

toggle.addEventListener('click', function () {
    this.classList.toggle('active');

    if (this.classList.contains('active')) {
    icon.classList.remove('fa-chevron-up');  
    icon.classList.add('fa-chevron-down');  
} else {
    icon.classList.remove('fa-chevron-down'); 
    icon.classList.add('fa-chevron-up'); 
}
});