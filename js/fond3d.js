// Fond 3D du site : sol néon, étoiles, produits flottants et effet bloom

const canvas = document.getElementById('fond-3d');
const estMobile = window.innerWidth < 768;
const pageAccueil = document.querySelector('.hero') !== null;

// Rendu
const rendu = new THREE.WebGLRenderer({ canvas: canvas, antialias: true });
rendu.setPixelRatio(estMobile ? 1 : Math.min(window.devicePixelRatio, 2));
rendu.setSize(window.innerWidth, window.innerHeight);
rendu.outputEncoding = THREE.sRGBEncoding;
rendu.toneMapping = THREE.ACESFilmicToneMapping; // évite que les zones claires deviennent blanches
rendu.toneMappingExposure = 0.9;

// Scène avec le même dégradé que le site
const scene = new THREE.Scene();

function creerFondDegrade() {
    const toile = document.createElement('canvas');
    toile.width = 2;
    toile.height = 512;

    const pinceau = toile.getContext('2d');
    const degrade = pinceau.createLinearGradient(0, 0, 0, 512);
    degrade.addColorStop(0, '#0f0c29');
    degrade.addColorStop(1, '#302b63');
    pinceau.fillStyle = degrade;
    pinceau.fillRect(0, 0, 2, 512);

    return new THREE.CanvasTexture(toile);
}

scene.background = creerFondDegrade();
scene.fog = new THREE.Fog(0x1a1640, 35, 95);

// Caméra
const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 200);
camera.position.set(0, 8, 30);

// Lumières (pour les reflets des téléphones)
scene.add(new THREE.AmbientLight(0xffffff, 0.6));

const lumiereBlanche = new THREE.DirectionalLight(0xffffff, 1.5);
lumiereBlanche.position.set(0, 10, 20);
scene.add(lumiereBlanche);

const lumiereViolette = new THREE.PointLight(0x8b5cf6, 3, 80);
lumiereViolette.position.set(-25, 5, 10);
scene.add(lumiereViolette);

const lumiereRose = new THREE.PointLight(0xec4899, 3, 80);
lumiereRose.position.set(25, 5, 10);
scene.add(lumiereRose);

// Sol en grille
const segments = estMobile ? 40 : 80;
const solGeometrie = new THREE.PlaneGeometry(160, 160, segments, segments);
const sol = new THREE.Mesh(
    solGeometrie,
    new THREE.MeshBasicMaterial({ color: 0x8b5cf6, wireframe: true, transparent: true, opacity: 0.18 })
);
sol.rotation.x = -Math.PI / 2;
sol.position.y = -8;
scene.add(sol);

const pointsDuSol = solGeometrie.attributes.position;

// Étoiles
const nbEtoiles = 1500;
const positionsEtoiles = new Float32Array(nbEtoiles * 3);

for (let i = 0; i < nbEtoiles * 3; i++) {
    positionsEtoiles[i] = (Math.random() - 0.5) * 200;
}

const etoilesGeometrie = new THREE.BufferGeometry();
etoilesGeometrie.setAttribute('position', new THREE.BufferAttribute(positionsEtoiles, 3));

const etoiles = new THREE.Points(
    etoilesGeometrie,
    new THREE.PointsMaterial({ color: 0xffffff, size: 0.3, transparent: true, opacity: 0.8 })
);
scene.add(etoiles);

// Produits flottants (accueil uniquement, pas sur téléphone)
const produits = [];
const chargeur = new THREE.GLTFLoader();

function chargerProduit(chemin, x, inclinaison) {
    chargeur.load(chemin, function (gltf) {
        const modele = gltf.scene;

        // même taille pour tous les modèles
        const taille = new THREE.Box3().setFromObject(modele).getSize(new THREE.Vector3());
        modele.scale.setScalar(10 / Math.max(taille.x, taille.y, taille.z));

        // on recentre le modèle pour qu'il tourne sur lui-même
        const centre = new THREE.Box3().setFromObject(modele).getCenter(new THREE.Vector3());
        modele.position.sub(centre);

        const pivot = new THREE.Group();
        pivot.add(modele);
        pivot.position.set(x, 3, 0);
        pivot.rotation.z = inclinaison;
        pivot.userData.decalage = Math.random() * Math.PI * 2;

        scene.add(pivot);
        produits.push(pivot);
    });
}

if (pageAccueil && !estMobile) {
    chargerProduit('modeles/iphone13Pro.glb', -20, 0.3);
    chargerProduit('modeles/samsung_galaxy_s21_ultra.glb', 20, -0.3);
}

// Effet bloom
const composeur = new THREE.EffectComposer(rendu);
composeur.addPass(new THREE.RenderPass(scene, camera));

const bloom = new THREE.UnrealBloomPass(
    new THREE.Vector2(window.innerWidth, window.innerHeight),
    0.6,  // force
    0.4,  // étendue
    0.55  // seuil
);
composeur.addPass(bloom);

// Souris
const souris = { x: 0, y: 0 };

window.addEventListener('mousemove', function (e) {
    souris.x = e.clientX / window.innerWidth - 0.5;
    souris.y = e.clientY / window.innerHeight - 0.5;
});

// Animation
const horloge = new THREE.Clock();

function animer() {
    const temps = horloge.getElapsedTime();

    // vagues du sol
    for (let i = 0; i < pointsDuSol.count; i++) {
        const x = pointsDuSol.getX(i);
        const y = pointsDuSol.getY(i);
        pointsDuSol.setZ(i, Math.sin(x * 0.08 + temps) * 1.5 + Math.cos(y * 0.08 + temps * 0.8) * 1.5);
    }
    pointsDuSol.needsUpdate = true;

    // les produits tournent, flottent et remontent quand on scrolle
    produits.forEach(function (pivot) {
        const d = pivot.userData.decalage;
        pivot.rotation.y = temps * 0.5 + d + souris.x * 1.5;
        pivot.position.y = 3 + Math.sin(temps + d) + window.scrollY * 0.03;
    });

    etoiles.rotation.y = temps * 0.01;

    // la caméra suit la souris en douceur
    camera.position.x += (souris.x * 6 - camera.position.x) * 0.05;
    camera.position.y += (8 - souris.y * 4 - camera.position.y) * 0.05;
    camera.lookAt(0, 0, 0);

    composeur.render();
    requestAnimationFrame(animer);
}

window.addEventListener('resize', function () {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    rendu.setSize(window.innerWidth, window.innerHeight);
    composeur.setSize(window.innerWidth, window.innerHeight);
});

// image fixe si l'utilisateur a désactivé les animations
if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    composeur.render();
} else {
    animer();
}