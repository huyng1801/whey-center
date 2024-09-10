document.addEventListener('DOMContentLoaded', function () {
    let slideIndex = 1;
    const slides = document.querySelectorAll(".banner");
    const dots = document.querySelectorAll(".dot");
    const container = document.querySelector(".banner-container");
    const totalSlides = slides.length;
    const originalSlidesCount = totalSlides - 2;

    container.style.transform = `translateX(${-slideIndex * 100}%)`;

    function showSlides(index) {
        slideIndex = index;

        if (slideIndex >= totalSlides - 1) {
            container.style.transition = 'none';
            slideIndex = 0;
            container.style.transform = `translateX(${-slideIndex * 100}%)`;
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    container.style.transition = 'transform 0.5s ease-in-out';
                    slideIndex++;
                    container.style.transform = `translateX(${-slideIndex * 100}%)`;
                    dots.forEach((dot, i) => {
                        dot.classList.toggle("active", i === ((slideIndex - 1) % originalSlidesCount));
                    });
                });
            });
        } else if (slideIndex <= 0) {
            container.style.transition = 'none';
            slideIndex = totalSlides - 1;
            container.style.transform = `translateX(${-slideIndex * 100}%)`;
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    container.style.transition = 'transform 0.5s ease-in-out';
                    slideIndex--;
                    container.style.transform = `translateX(${-slideIndex * 100}%)`;
                    dots.forEach((dot, i) => {
                        dot.classList.toggle("active", i === ((slideIndex - 1) % originalSlidesCount));
                    });
                });
            });
        } else {
            container.style.transition = 'transform 0.5s ease-in-out';
            container.style.transform = `translateX(${-slideIndex * 100}%)`;
            dots.forEach((dot, i) => {
                dot.classList.toggle("active", i === ((slideIndex - 1) % originalSlidesCount));
            });
        }

     
    }

    document.querySelector(".prev").addEventListener("click", function () {
        showSlides(slideIndex - 1);
    });

    document.querySelector(".next").addEventListener("click", function () {
        showSlides(slideIndex + 1);
    });

    dots.forEach((dot, index) => {
        dot.addEventListener("click", function () {
            showSlides(index + 1);
        });
    });

    setInterval(function () {
        showSlides(slideIndex + 1);
    }, 6000);

    showSlides(slideIndex);
});