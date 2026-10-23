// Effet 3D des cartes produit au survol de la souris

const cartes = document.querySelectorAll('.produit');

cartes.forEach(function (carte) {

    carte.addEventListener('mousemove', function (e) {
        const rect = carte.getBoundingClientRect();

        // position de la souris dans la carte
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        // inclinaison max de 10° de chaque côté
        const rotY = (x / rect.width - 0.5) * 20;
        const rotX = (0.5 - y / rect.height) * 20;

        carte.style.transform = `perspective(800px) rotateX(${rotX}deg) rotateY(${rotY}deg) translateY(-10px)`;
    });

    // la carte revient à plat
    carte.addEventListener('mouseleave', function () {
        carte.style.transform = '';
    });
});