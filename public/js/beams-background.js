/**
 * Fond animé "faisceaux lumineux" en canvas, sans dépendance externe.
 * Portage vanilla JS du composant React BeamsBackground.
 * Usage : <canvas data-beams-background></canvas> dans un conteneur
 * position:relative ; le canvas se redimensionne sur le conteneur parent.
 */
(function () {
    const MINIMUM_BEAMS = 20;

    function createBeam(width, height) {
        const angle = -35 + Math.random() * 10;
        return {
            x: Math.random() * width * 1.5 - width * 0.25,
            y: Math.random() * height * 1.5 - height * 0.25,
            width: 30 + Math.random() * 60,
            length: height * 2.5,
            angle: angle,
            speed: 0.6 + Math.random() * 1.2,
            opacity: 0.12 + Math.random() * 0.16,
            hue: 190 + Math.random() * 70,
            pulse: Math.random() * Math.PI * 2,
            pulseSpeed: 0.02 + Math.random() * 0.03,
        };
    }

    function resetBeam(beam, index, totalBeams, canvas) {
        const column = index % 3;
        const spacing = canvas.width / 3;

        beam.y = canvas.height + 100;
        beam.x = column * spacing + spacing / 2 + (Math.random() - 0.5) * spacing * 0.5;
        beam.width = 100 + Math.random() * 100;
        beam.speed = 0.5 + Math.random() * 0.4;
        beam.hue = 190 + (index * 70) / totalBeams;
        beam.opacity = 0.2 + Math.random() * 0.1;
        return beam;
    }

    function drawBeam(ctx, beam, opacityFactor) {
        ctx.save();
        ctx.translate(beam.x, beam.y);
        ctx.rotate((beam.angle * Math.PI) / 180);

        const pulsingOpacity = beam.opacity * (0.8 + Math.sin(beam.pulse) * 0.2) * opacityFactor;

        const gradient = ctx.createLinearGradient(0, 0, 0, beam.length);
        gradient.addColorStop(0, `hsla(${beam.hue}, 85%, 65%, 0)`);
        gradient.addColorStop(0.1, `hsla(${beam.hue}, 85%, 65%, ${pulsingOpacity * 0.5})`);
        gradient.addColorStop(0.4, `hsla(${beam.hue}, 85%, 65%, ${pulsingOpacity})`);
        gradient.addColorStop(0.6, `hsla(${beam.hue}, 85%, 65%, ${pulsingOpacity})`);
        gradient.addColorStop(0.9, `hsla(${beam.hue}, 85%, 65%, ${pulsingOpacity * 0.5})`);
        gradient.addColorStop(1, `hsla(${beam.hue}, 85%, 65%, 0)`);

        ctx.fillStyle = gradient;
        ctx.fillRect(-beam.width / 2, 0, beam.width, beam.length);
        ctx.restore();
    }

    function initBeamsBackground(canvas, options) {
        const intensity = (options && options.intensity) || "strong";
        const opacityMap = { subtle: 0.7, medium: 0.85, strong: 1 };
        const opacityFactor = opacityMap[intensity] ?? 1;

        const ctx = canvas.getContext("2d");
        if (!ctx) return;

        const container = canvas.parentElement;
        let beams = [];
        let animationFrame;

        function updateCanvasSize() {
            const dpr = window.devicePixelRatio || 1;
            const rect = container.getBoundingClientRect();
            canvas.width = rect.width * dpr;
            canvas.height = rect.height * dpr;
            canvas.style.width = rect.width + "px";
            canvas.style.height = rect.height + "px";
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.scale(dpr, dpr);

            const totalBeams = MINIMUM_BEAMS * 1.5;
            beams = Array.from({ length: totalBeams }, () => createBeam(canvas.width, canvas.height));
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.filter = "blur(35px)";

            const totalBeams = beams.length;
            beams.forEach((beam, index) => {
                beam.y -= beam.speed;
                beam.pulse += beam.pulseSpeed;

                if (beam.y + beam.length < -100) {
                    resetBeam(beam, index, totalBeams, canvas);
                }

                drawBeam(ctx, beam, opacityFactor);
            });

            animationFrame = requestAnimationFrame(animate);
        }

        updateCanvasSize();
        window.addEventListener("resize", updateCanvasSize);
        animate();

        return function destroy() {
            window.removeEventListener("resize", updateCanvasSize);
            cancelAnimationFrame(animationFrame);
        };
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("[data-beams-background]").forEach(function (canvas) {
            initBeamsBackground(canvas, { intensity: canvas.dataset.intensity || "strong" });
        });
    });
})();
