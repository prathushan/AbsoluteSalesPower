// Highlight text in the service page

document.addEventListener('DOMContentLoaded', function() {
    const texts = document.querySelectorAll('.text');
    
    // Check if there are any .text elements
    if (texts.length === 0) {
        console.error('No elements with class text found.');
        return; // Exit if no elements found
    }

    let currentIndex = 0;

    function highlightText() {
        // Remove the highlighted class from all elements
        texts.forEach(text => text.classList.remove('highlighted'));
        
        // Add the highlighted class to the current element
        texts[currentIndex].classList.add('highlighted');
        
        // Update index for the next word
        currentIndex = (currentIndex + 1) % texts.length;
    }

    // Run highlightText every second (1000 ms)
    setInterval(highlightText, 1000);
});





// ---------
// Confetti Effect for About Us Page
document.addEventListener('DOMContentLoaded', function () {
    const confettiSections = document.querySelectorAll('.confetti-section');

    function createConfetti(section) {
        const confetti = document.createElement('div');
        confetti.classList.add('confetti');
        confetti.style.left = (Math.random() * 100) + 'vw';
        confetti.style.animationDuration = (2 + Math.random() * 3) + 's';
        confetti.style.opacity = Math.random();
        confetti.style.backgroundColor = 'hsl(' + (Math.random() * 360) + ', 100%, 50%)';

        section.appendChild(confetti);
        confetti.addEventListener('animationend', function () {
            confetti.remove();
        });
    }

    confettiSections.forEach(function (section) {
        setInterval(function () {
            createConfetti(section);
        }, 300);
    });
});


// Make Footer H2 Clickable
document.addEventListener("DOMContentLoaded", function () {
    const headingLink = document.querySelector('.footer-h2 a');
    
    if (headingLink) {
        // Add a click event to the entire h2 element, including the arrow
        const parentHeading = headingLink.closest('.footer-h2');
        
        parentHeading.addEventListener('click', function () {
            window.location.href = headingLink.href;
        });

        // Ensure the cursor shows it's clickable for the entire h2
        parentHeading.style.cursor = 'pointer';
    }
});


// Continuous Heading Animation
document.addEventListener('DOMContentLoaded', function () {
    const heading = document.querySelector('.services-prog-det-head');
    if (heading) {
        heading.style.transition = 'opacity 1s ease, transform 1s ease';

        function animateHeading() {
            // Set initial state
            heading.style.opacity = '0';
            heading.style.transform = 'translateY(20px)';
            
            // Trigger the animation after a short delay
            setTimeout(() => {
                heading.style.opacity = '1';
                heading.style.transform = 'translateY(0)';
            }, 500);

            // Reset animation after it completes
            setTimeout(() => {
                heading.style.opacity = '0';
                heading.style.transform = 'translateY(20px)';
            }, 3000); // Reset after 3 seconds
        }

        // Run the animation in a loop
        setInterval(animateHeading, 3500); // Repeat every 3.5 seconds
    }
});


// -------------
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.services-button-tab1 .wp-block-button__link');
    const contentSections = document.querySelectorAll('.services-content-tab1 > .wp-block-group');

    // Ensure there are buttons and content sections before proceeding
    if (buttons.length > 0 && contentSections.length > 0) {
        buttons.forEach((button, index) => {
            button.addEventListener('click', function () {
                // Remove active class from all buttons
                buttons.forEach(btn => btn.classList.remove('active'));
                // Add active class to the clicked button
                this.classList.add('active');

                // Hide all content sections
                contentSections.forEach(section => {
                    section.classList.remove('active');
                });
                // Show the clicked button's corresponding content section
                contentSections[index].classList.add('active');
            });
        });

        // Show the first content section and set the first button as active by default
        contentSections[0].classList.add('active');
        buttons[0].classList.add('active');
    }
});


