document.addEventListener('DOMContentLoaded', function () {
    var counters = document.querySelectorAll('.hms-vital-value[data-count]');
    var supportsFinePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    counters.forEach(function (counter, index) {
        var target = parseInt(counter.getAttribute('data-count'), 10);

        if (isNaN(target)) {
            return;
        }

        var duration = 900 + (index * 90);
        var startTime = null;

        function updateCounter(timestamp) {
            if (!startTime) {
                startTime = timestamp;
            }

            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            counter.textContent = Math.round(target * eased);

            if (progress < 1) {
                window.requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        }

        window.requestAnimationFrame(updateCounter);
    });

    if (!supportsFinePointer || prefersReducedMotion) {
        return;
    }

    var interactiveCards = document.querySelectorAll('.hms-interactive');

    interactiveCards.forEach(function (card) {
        var frame = null;

        card.addEventListener('mousemove', function (event) {
            var rect = card.getBoundingClientRect();
            var x = event.clientX - rect.left;
            var y = event.clientY - rect.top;
            var px = (x / rect.width) * 100;
            var py = (y / rect.height) * 100;
            var rotateY = ((x / rect.width) - 0.5) * 6;
            var rotateX = (((y / rect.height) - 0.5) * -6);

            if (frame) {
                window.cancelAnimationFrame(frame);
            }

            frame = window.requestAnimationFrame(function () {
                card.style.setProperty('--mx', px + '%');
                card.style.setProperty('--my', py + '%');
                card.style.setProperty('--ry', rotateY.toFixed(2) + 'deg');
                card.style.setProperty('--rx', rotateX.toFixed(2) + 'deg');
            });
        });

        card.addEventListener('mouseleave', function () {
            if (frame) {
                window.cancelAnimationFrame(frame);
            }

            card.style.setProperty('--mx', '50%');
            card.style.setProperty('--my', '50%');
            card.style.setProperty('--ry', '0deg');
            card.style.setProperty('--rx', '0deg');
        });
    });
});
