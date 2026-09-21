document.addEventListener("DOMContentLoaded", function () {

    const testimonialSwiper = new Swiper(".testimonial-swiper", {

        slidesPerView: 3,

        spaceBetween: 16,

        speed: 500,

        navigation: {
            nextEl: ".testimonial-next",
            prevEl: ".testimonial-prev"
        },

        breakpoints: {

            0: {
                slidesPerView: 1,
                spaceBetween: 12
            },

            576: {
                slidesPerView: 2,
                spaceBetween: 12
            },

            992: {
                slidesPerView: 3,
                spaceBetween: 16
            }

        }

    });

});