// 1. Récupérer toutes les cartes
const cartes = document.querySelectorAll('.produit');

cartes.forEach(function (carte) {

    // 2. Quand la souris bouge sur la carte
    carte.addEventListener('mousemove', function (e) {
        const rect = carte.getBoundingClientRect();

        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const rotY = (x / rect.width - 0.5) * 20;
        const rotX = (0.5 - y / rect.height) * 20;

        carte.style.transform = `perspective(800px) rotateX(${rotX}deg) rotateY(${rotY}deg) translateY(-10px)`;
    });

    // 3. Quand la souris quitte la carte
    carte.addEventListener('mouseleave', function () {
        carte.style.transform = '';
    });

});

