import * as THREE from 'three';
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js';
import { RoundedBoxGeometry } from 'three/examples/jsm/geometries/RoundedBoxGeometry.js';
import { RectAreaLightUniformsLib } from 'three/examples/jsm/lights/RectAreaLightUniformsLib.js';

/*
 * Deneysel 3D galeri. İki düzen var:
 *  - oda: ana eser arka duvarda, sanatçının diğer eserleri yan duvarlarda, her esere bir spot.
 *  - salon (data.mode === 'hall'): satıştaki tüm eserler, ara duvarlı uzun salon; gerçek spot
 *    yalnızca ziyaretçiye en yakın eserlere verilir, diğerlerinde ışık hüzmesi taklit edilir.
 * Ölçüler cm cinsinden gelir, sahne birimi metredir.
 */

const data = window.GALLERY;
const HALL = data.mode === 'hall';
const EYE = 1.6;
const isMobile = matchMedia('(pointer: coarse)').matches;

const stage = document.getElementById('stage');
const renderer = new THREE.WebGLRenderer({ antialias: true });
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.outputColorSpace = THREE.SRGBColorSpace;
// Neutral ton eşleme eserin renklerini ACES gibi kaydırmaz
renderer.toneMapping = THREE.NeutralToneMapping;
renderer.toneMappingExposure = 0.88;
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap;
stage.appendChild(renderer.domElement);

const scene = new THREE.Scene();
scene.background = new THREE.Color('#e9e6e1');
const pmrem = new THREE.PMREMGenerator(renderer);
scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture;
scene.environmentIntensity = 0.35;

const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.05, 100);
camera.rotation.order = 'YXZ';

RectAreaLightUniformsLib.init();

// galerilerde yaygın 3000K sıcak beyaz
const SPOT_COLOR = '#fff0de';

// ---------------------------------------------------------------- yardımcılar

function canvasTexture(w, h, draw) {
    const c = document.createElement('canvas');
    c.width = w;
    c.height = h;
    draw(c.getContext('2d'), w, h);
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    tex.anisotropy = renderer.capabilities.getMaxAnisotropy();
    return tex;
}

function woodTexture() {
    const tex = canvasTexture(1024, 1024, (g, w, h) => {
        const cols = 12;
        const plankW = w / cols;
        const plankL = h;
        for (let col = 0; col < cols; col++) {
            const offset = ((col * 7) % 5) / 5 * plankL;
            for (let row = -1; row < 1; row++) {
                const y = row * plankL + offset;
                const l = 54 + Math.random() * 6;
                g.fillStyle = `hsl(${28 + Math.random() * 4}, ${34 + Math.random() * 6}%, ${l}%)`;
                g.fillRect(col * plankW, y, plankW, plankL);
                // damarlar
                for (let i = 0; i < 14; i++) {
                    g.strokeStyle = `rgba(110, 70, 35, ${0.04 + Math.random() * 0.07})`;
                    g.lineWidth = 0.6 + Math.random() * 1.6;
                    const x = col * plankW + Math.random() * plankW;
                    g.beginPath();
                    g.moveTo(x, y);
                    g.bezierCurveTo(x + (Math.random() - 0.5) * 18, y + plankL * 0.33, x + (Math.random() - 0.5) * 18, y + plankL * 0.66, x + (Math.random() - 0.5) * 10, y + plankL);
                    g.stroke();
                }
                g.fillStyle = 'rgba(60, 40, 20, .35)';
                g.fillRect(col * plankW, y, plankW, 2);
            }
            g.fillStyle = 'rgba(60, 40, 20, .3)';
            g.fillRect(col * plankW, 0, 2, h);
        }
    });
    tex.wrapS = tex.wrapT = THREE.RepeatWrapping;
    return tex;
}

const LABEL = { w: 0.36, h: 0.24 };

function wrapText(g, text, maxWidth, maxLines) {
    const words = String(text).split(/\s+/);
    const lines = [''];
    for (const word of words) {
        const test = lines[lines.length - 1] ? `${lines[lines.length - 1]} ${word}` : word;
        if (g.measureText(test).width <= maxWidth || !lines[lines.length - 1]) {
            lines[lines.length - 1] = test;
        } else if (lines.length < maxLines) {
            lines.push(word);
        } else {
            lines[lines.length - 1] += '…';
            break;
        }
    }
    return lines;
}

function labelTexture(art, scale = 1) {
    return canvasTexture(Math.round(1500 * scale), Math.round(1000 * scale), (g) => {
        g.scale(scale, scale);
        const w = 1500, h = 1000;
        const pad = 90, max = w - pad * 2;
        g.fillStyle = '#ffffff';
        g.fillRect(0, 0, w, h);
        g.textBaseline = 'alphabetic';

        let y = 170;
        g.fillStyle = '#111';
        g.font = '600 96px Prompt, sans-serif';
        g.fillText(art.artist, pad, y, max);

        g.font = 'italic 400 84px Prompt, sans-serif';
        for (const line of wrapText(g, art.title, max, 2)) {
            y += 108;
            g.fillText(line, pad, y, max);
        }

        g.fillStyle = '#222';
        g.font = '400 66px Prompt, sans-serif';
        y += 40;
        for (const line of [[art.technique, art.year].filter(Boolean).join(', '), art.dimensionsText || 'Ölçü belirtilmemiş']) {
            if (!line) continue;
            for (const part of wrapText(g, line, max, 2)) {
                y += 84;
                g.fillText(part, pad, y, max);
            }
        }

        g.font = '600 76px Prompt, sans-serif';
        g.fillStyle = '#111';
        if (art.sold || art.reserved) {
            // galerilerde satılmış eser kırmızı noktayla işaretlenir
            g.fillStyle = art.sold ? '#c62828' : '#e0a100';
            g.beginPath();
            g.arc(pad + 30, h - 115, 30, 0, Math.PI * 2);
            g.fill();
            g.fillStyle = '#111';
            g.fillText(art.sold ? 'Satıldı' : 'Rezerve', pad + 85, h - 90);
        } else if (art.price) {
            g.fillText(art.price, pad, h - 90);
        }
    });
}

/** Görsel oranını koruyarak uzun kenarı belirtilen ölçüye eşitler. */
function sizeFor(art, aspect) {
    const dims = art.dimensions;
    const long = dims ? Math.max(dims[0], dims[1]) / 100 : 0.8;
    const depth = dims && dims[2] ? THREE.MathUtils.clamp(dims[2] / 100, 0.02, 0.5) : 0.035;
    return aspect >= 1
        ? { w: long, h: long / aspect, d: depth }
        : { w: long * aspect, h: long, d: depth };
}

async function loadTexture(url) {
    try {
        const tex = await new THREE.TextureLoader().loadAsync(url);
        tex.colorSpace = THREE.SRGBColorSpace;
        tex.anisotropy = renderer.capabilities.getMaxAnisotropy();
        return tex;
    } catch {
        return null;
    }
}

// ---------------------------------------------------------------- sahne

const wallMat = new THREE.MeshStandardMaterial({ color: '#f4f2ee', roughness: 0.95 });
// anahtarlar duvarla zıt tonda: açık duvarda mat antrasit, koyu duvarda kırık beyaz
const switchMat = new THREE.MeshStandardMaterial({ color: '#2b2c2e', roughness: 0.45, metalness: 0.1 });
const switchBezelMat = new THREE.MeshStandardMaterial({ color: '#3d3f42', roughness: 0.55 });

function matchSwitchesToWall() {
    const dark = wallMat.color.getHSL({}).l < 0.4;
    switchMat.color.set(dark ? '#ecebe6' : '#2b2c2e');
    switchBezelMat.color.set(dark ? '#d9d7d1' : '#3d3f42');
}
// genel aydınlatma (sol üstteki buton) ve ortamla birlikte kısılan, ışıktan bağımsız malzemeler
const lighting = { level: 1, target: 1, hemi: null, basics: [], panels: [] };
const arts = [];      // { art, mesh, label, center, normal, size }
const clickables = [];
const occluders = []; // duvarlar: arkalarındaki eserlere tıklanmasın
let floor, room;
const obstacles = []; // { x, z, r } yürürken içinden geçilmeyen eşyalar
const rects = [];     // { x, z, hx, hz } dikdörtgen engeller (bank, ara duvar)

// ---------------------------------------------------------------- yükleme ekranı

const progress = (() => {
    const bar = document.getElementById('ld-bar');
    const pct = document.getElementById('ld-pct');
    const stepEl = document.getElementById('ld-step');
    const log = document.getElementById('ld-log');
    const wall = document.getElementById('ld-wall');
    let total = 1, done = 0, pending = 0, credit = 0, shown = 0, running = true;

    (function tick() {
        // beklenen iş varken çubuk bir sonraki adıma doğru yavaşça ilerler, takılmış görünmez
        const target = Math.min(1, (done + credit) / total);
        shown += (target - shown) * (target > shown ? 0.06 : 0);
        bar.style.width = `${(shown * 100).toFixed(1)}%`;
        pct.textContent = `${Math.round(shown * 100)}%`;
        if (running) requestAnimationFrame(tick);
    })();

    return {
        plan(n) { total = n; },
        step(text) {
            if (stepEl.textContent === text) return;
            stepEl.textContent = text;
            stepEl.style.animation = 'none';
            void stepEl.offsetWidth;
            stepEl.style.animation = '';
        },
        start(w = 1, share = 0.85) {
            pending += w;
            credit += w * share;
        },
        finish(w = 1, label, share = 0.85) {
            pending = Math.max(0, pending - w);
            credit = Math.max(0, credit - w * share);
            done += w;
            if (label) {
                const li = document.createElement('li');
                li.textContent = label;
                log.prepend(li);
                while (log.children.length > 3) log.lastChild.remove();
            }
        },
        frames(count) {
            const width = Math.min(440, window.innerWidth - 48);
            // çok eserde tek sıra yerine mozaik
            const many = count > 12;
            if (many) wall.classList.add('many');
            const max = many
                ? Math.max(14, Math.min(40, Math.floor(Math.sqrt((width * 150) / count)) - 8))
                : Math.min(52, (width - 10 * (count - 1)) / count);
            return Array.from({ length: count }, (_, i) => {
                const f = document.createElement('div');
                f.className = 'ld-frame' + (i === 0 && !many ? ' main' : '');
                f.style.width = `${max}px`;
                f.style.height = `${many ? max : max * 1.25}px`;
                f.style.animationDelay = `${(i % 12) * 0.15}s`;
                wall.appendChild(f);
                return {
                    fill(src, aspect) {
                        const box = many ? max : i === 0 ? max * 1.6 : max * 1.25;
                        const w = aspect >= 1 ? box : box * aspect;
                        f.style.width = `${w}px`;
                        f.style.height = `${aspect >= 1 ? box / aspect : box}px`;
                        const img = new Image();
                        img.src = src;
                        img.onload = () => f.classList.add('loaded');
                        f.appendChild(img);
                    },
                };
            });
        },
        async finishAll() {
            done = total;
            pending = 0;
            credit = 0;
            this.step('Hazır, iyi gezintiler');
            await new Promise((r) => setTimeout(r, 650));
            running = false;
            document.getElementById('loading').classList.add('done');
        },
    };
})();

const nextFrame = () => new Promise((r) => requestAnimationFrame(() => r()));

async function build() {
    const works = [data.main, ...data.others];
    const BUILD_STEPS = 3;
    progress.plan(1 + works.length * 3 + (data.artistPhoto ? 1 : 0) + BUILD_STEPS);

    progress.step('Yazı tipleri yükleniyor…');
    progress.start();
    await document.fonts.ready;
    progress.finish(1, 'Yazı tipleri');

    const frames = progress.frames(works.length);
    let loaded = 0;
    progress.step(`Eserler yükleniyor (0/${works.length})`);
    const loadWork = (art, i) => {
        progress.start(3, 0.15);
        return loadTexture(art.image).then((tex) => {
            loaded++;
            progress.finish(3, tex ? art.title : `${art.title} (yüklenemedi)`, 0.15);
            progress.step(`Eserler yükleniyor (${loaded}/${works.length})`);
            if (tex) frames[i].fill(art.image, tex.image.width / tex.image.height);
            return tex;
        });
    };
    const photoPromise = data.artistPhoto
        ? (progress.start(), loadTexture(data.artistPhoto).then((tex) => {
            progress.finish(1, 'Sanatçı fotoğrafı');
            return tex;
        }))
        : Promise.resolve(null);

    const [mainTex, ...otherTex] = await Promise.all(works.map(loadWork));
    const photoTex = await photoPromise;

    const items = [{ art: data.main, tex: mainTex }]
        .concat(data.others.map((art, i) => ({ art, tex: otherTex[i] })).filter((x) => x.tex));
    for (const it of items) {
        const img = it.tex?.image;
        it.size = sizeFor(it.art, img ? img.width / img.height : 1);
    }

    if (HALL) {
        await buildHallScene(items);
        return;
    }

    const main = items[0];
    const left = [], right = [];
    items.slice(1).forEach((it, i) => (i % 2 ? right : left).push(it));

    const gap = 1.1, labelSpace = 0.8;
    const wallNeed = (list) => list.reduce((s, it) => s + it.size.w + labelSpace + gap, gap);
    const tallest = Math.max(...items.map((it) => it.size.h));

    const W = Math.max(7, main.size.w + 2 * (labelSpace + 1.6));
    const D = Math.max(9, wallNeed(left) + 2.5, wallNeed(right) + 2.5);
    const H = Math.max(3.4, tallest + 1.2);
    room = { W, D, H };

    progress.step('Galeri odası kuruluyor…');
    progress.start();
    await nextFrame();
    buildRoom(W, D, H);
    progress.finish(1, 'Oda ve zemin');

    progress.step('Eserler duvara asılıyor, spotlar ayarlanıyor…');
    progress.start();
    await nextFrame();
    placeOnWall(main, new THREE.Vector3(0, 0, -D / 2), new THREE.Vector3(0, 0, 1), H, true);
    layoutWall(left, new THREE.Vector3(-W / 2, 0, -0.6), new THREE.Vector3(1, 0, 0), H, gap, labelSpace);
    layoutWall(right, new THREE.Vector3(W / 2, 0, -0.6), new THREE.Vector3(-1, 0, 0), H, gap, labelSpace);

    progress.finish(1, 'Eserler ve aydınlatma');

    progress.step('Son dokunuşlar…');
    progress.start();
    await nextFrame();
    buildBench(0, D / 2 - 3.6, 0);
    buildLounge(W, D);
    buildEntranceSign(W, D, H, photoTex);

    upgradeTexture(arts[0]);
    camera.position.set(0, EYE, D / 2 - 1.2);
    lookAtPoint(arts[0].center);
    // shader'ları önceden derle, ilk karede takılma olmasın
    renderer.compile(scene, camera);
    progress.finish(1, 'Mobilya ve son dokunuşlar');
    await progress.finishAll();
    focusOn(arts[0], 2400);
}

async function buildHallScene(items) {
    const plan = planHall(items);
    const { W, D, H } = plan;
    room = { W, D, H };

    progress.step('Sergi salonu kuruluyor…');
    progress.start();
    await nextFrame();
    const laneX = plan.PT / 2 + 1.3;
    const laneMidX = W / 4 + plan.PT / 4;
    buildRoom(W, D, H, [-W / 2 + 1.3, -laneX, laneX, W / 2 - 1.3], [-laneMidX, laneMidX]);
    for (const z of plan.partitions) buildPartition(z, plan);
    progress.finish(1, `Salon (${plan.partitions.length} ara duvar)`);

    progress.step(`${items.length} eser duvarlara asılıyor…`);
    progress.start();
    await nextFrame();
    for (const p of plan.placements) placeOnWall(p.it, p.point, p.normal, H, false, false);
    createSpotPool(isMobile ? 6 : 8, isMobile ? 0 : 3);
    progress.finish(1, 'Eserler ve aydınlatma');

    progress.step('Son dokunuşlar…');
    progress.start();
    await nextFrame();
    for (const x of [-1, 1]) buildBench(x * (W / 4 + plan.PT / 4), 0, Math.PI / 2);
    buildLounge(W, D);
    buildEntranceSign(W, D, H, null);

    const laneMid = -(W / 4 + plan.PT / 4);
    camera.position.set(laneMid + 1.2, EYE, D / 2 - 1.0);
    lookAtPoint(new THREE.Vector3(laneMid, EYE, -D / 2));
    updatePool(performance.now(), true);
    renderer.compile(scene, camera);
    progress.finish(1, 'Mobilya ve son dokunuşlar');
    await progress.finishAll();
    moveTo(new THREE.Vector3(laneMid + 0.6, EYE, D / 2 - 3.0), new THREE.Vector3(laneMid - 0.4, EYE - 0.05, 0), 2400);
}

/**
 * Salon planı: dış duvarlar + ortada iki yüzlü ara duvarlar. Eserler sırayla (sanatçıya göre)
 * yüzlere dizilir; sığmazsa salon uzatılıp yeniden denenir.
 */
function planHall(items) {
    const W = 14, PL = 6, PG = 3.2, PT = 0.2;
    const tallest = Math.max(...items.map((it) => it.size.h));
    const H = Math.max(3.6, tallest + 1.4);
    const PH = H - 0.6;
    const cyOf = (it) => Math.max(1.5, it.size.h / 2 + 0.35);
    // anahtar solda, etiket sağda
    const need = (it) => 0.45 + it.size.w + 0.65 + 0.6;

    for (let D = 14; D < 400; D += 2) {
        const partitions = [];
        for (let z = D / 2 - 4.5 - PL / 2; z - PL / 2 >= -D / 2 + 3; z -= PL + PG) partitions.push(z);

        const face = (cx, cz, nx, nz, length, maxH) => ({
            center: new THREE.Vector3(cx, 0, cz),
            normal: new THREE.Vector3(nx, 0, nz),
            length, maxH, used: 0, items: [],
        });
        const wallFrom = D / 2 - 2.2, wallTo = -D / 2 + 0.6;
        const faces = [
            face(-W / 2, (wallFrom + wallTo) / 2, 1, 0, wallFrom - wallTo, H - 0.3),
            face(0, -D / 2, 0, 1, W - 1.2, H - 0.3),
            face(W / 2, (wallFrom + wallTo) / 2, -1, 0, wallFrom - wallTo, H - 0.3),
            ...partitions.map((z) => face(PT / 2, z, 1, 0, PL - 0.3, PH - 0.15)),
            ...[...partitions].reverse().map((z) => face(-PT / 2, z, -1, 0, PL - 0.3, PH - 0.15)),
        ];

        let k = 0, ok = true;
        for (const it of items) {
            while (k < faces.length && (faces[k].used + need(it) > faces[k].length
                || cyOf(it) + it.size.h / 2 > faces[k].maxH)) k++;
            if (k === faces.length) { ok = false; break; }
            faces[k].items.push({ it, s: faces[k].used });
            faces[k].used += need(it);
        }
        if (!ok) continue;

        const placements = [];
        for (const f of faces) {
            const rightDir = new THREE.Vector3(0, 1, 0).cross(f.normal);
            // yüzdeki eserleri ortala
            const start = -f.length / 2 + (f.length - f.used) / 2;
            for (const { it, s: off } of f.items) {
                const point = f.center.clone().addScaledVector(rightDir, start + off + 0.45 + it.size.w / 2);
                placements.push({ it, point, normal: f.normal });
            }
        }
        return { W, D, H, PH, PL, PT, partitions, placements };
    }
    throw new Error('Salon planlanamadı');
}

function buildPartition(z, { PH, PL, PT }) {
    const wall = new THREE.Mesh(new THREE.BoxGeometry(PT, PH, PL), wallMat);
    wall.position.set(0, PH / 2, z);
    wall.castShadow = wall.receiveShadow = true;
    wall.userData.occluder = true;
    scene.add(wall);
    occluders.push(wall);

    const base = new THREE.Mesh(
        new THREE.BoxGeometry(PT + 0.03, 0.09, PL + 0.03),
        new THREE.MeshStandardMaterial({ color: '#ffffff', roughness: 0.6 }),
    );
    base.position.set(0, 0.045, z);
    scene.add(base);
    cornerShade(PT / 2, z, Math.PI / 2, PL, PH + 1, PH);
    cornerShade(-PT / 2, z, -Math.PI / 2, PL, PH + 1, PH);
    rects.push({ x: 0, z, hx: PT / 2 + 0.4, hz: PL / 2 + 0.4 });
}

// ---------------------------------------------------------------- salon: ışık havuzu

const spotPool = [];
let haloTexture = null;

function getHaloTexture() {
    // tavandan vuran spotun duvarda bıraktığı, üstü daha parlak ışık lekesi
    haloTexture ??= canvasTexture(256, 256, (g, w, h) => {
        const grad = g.createRadialGradient(w / 2, h * 0.46, 0, w / 2, h * 0.5, w / 2);
        grad.addColorStop(0, 'rgba(255,232,200,0.75)');
        grad.addColorStop(0.45, 'rgba(255,232,200,0.4)');
        grad.addColorStop(0.8, 'rgba(255,232,200,0.08)');
        grad.addColorStop(1, 'rgba(255,232,200,0)');
        g.fillStyle = grad;
        g.fillRect(0, 0, w, h);
    });
    return haloTexture;
}

function createSpotPool(count, shadowCount) {
    for (let i = 0; i < count; i++) {
        const light = new THREE.SpotLight(SPOT_COLOR, 0, 0, 0.5, 0.95, 2);
        if (i < shadowCount) {
            light.castShadow = true;
            light.shadow.mapSize.set(1024, 1024);
            light.shadow.bias = -0.0004;
            light.shadow.normalBias = 0.02;
            light.shadow.radius = 6;
            light.shadow.blurSamples = 16;
        }
        scene.add(light, light.target);
        spotPool.push({ light, entry: null, fade: 0 });
    }
}

let poolTick = 0;
const liveLabels = [];

/** En yakın eserlere gerçek spot ver, yakındaki etiketleri oluştur. */
function updatePool(now, force = false) {
    if (!spotPool.length || (!force && now - poolTick < 200)) return;
    poolTick = now;
    const cam = camera.position;
    const dist = (e) => Math.hypot(e.center.x - cam.x, e.center.z - cam.z);

    const wanted = arts.filter((e) => e.on).sort((a, b) => dist(a) - dist(b)).slice(0, spotPool.length);
    const wantedSet = new Set(wanted);
    for (const slot of spotPool) {
        if (slot.entry && !wantedSet.has(slot.entry)) {
            slot.entry.poolSlot = null;
            slot.entry = null;
        }
    }
    for (const e of wanted) {
        if (e.poolSlot) continue;
        const slot = spotPool.find((p) => !p.entry);
        if (!slot) break;
        slot.entry = e;
        slot.fade = 0;
        e.poolSlot = slot;
        slot.light.position.copy(e.spotPos);
        slot.light.angle = e.spotAngle;
        slot.light.target.position.copy(e.aim);
        slot.light.target.updateMatrixWorld();
    }

    // etiketler yalnızca yakınken oluşturulur, uzaklaşınca bellekten atılır
    for (const e of arts) {
        if (!e.labelReady && dist(e) < 7) {
            e.labelMat.map = labelTexture(e.art, 0.6);
            e.labelMat.color.setScalar(1);
            e.labelMat.needsUpdate = true;
            e.labelReady = true;
            liveLabels.push(e);
        }
    }
    if (liveLabels.length > 16) {
        liveLabels.sort((a, b) => dist(a) - dist(b));
        for (const e of liveLabels.splice(16)) {
            e.labelMat.map.dispose();
            e.labelMat.map = null;
            e.labelMat.needsUpdate = true;
            e.labelReady = false;
        }
    }
}

/**
 * Tavana gömülü, opal camlı ışık paneli. Duvarlarda üstten aşağı yumuşak bir geçiş verir;
 * genel ışık butonu bunları da kısar.
 */
function addCeilingPanel(x, z, H, width, length) {
    const light = new THREE.RectAreaLight('#fff8f0', 0, width, length);
    light.position.set(x, H - 0.01, z);
    light.rotation.x = -Math.PI / 2;
    scene.add(light);

    // ışık tek parça hesaplanır, görünen opal camlar ise aralıklı panellere bölünür
    const mat = new THREE.MeshBasicMaterial({ color: '#fffaf2', toneMapped: false });
    const frameMat = new THREE.MeshStandardMaterial({ color: '#e9e7e2', roughness: 0.6 });
    const count = Math.max(1, Math.round(length / 4));
    const pl = Math.min(2.2, length / count - 0.8), pw = Math.min(width, 0.9);
    for (let i = 0; i < count; i++) {
        const pz = z - length / 2 + (i + 0.5) * (length / count);
        const diffuser = new THREE.Mesh(new THREE.PlaneGeometry(pw, pl), mat);
        diffuser.position.set(x, H - 0.008, pz);
        diffuser.rotation.x = Math.PI / 2;
        scene.add(diffuser);
        for (const [fw, fl, fx, fz] of [[pw + 0.06, 0.03, 0, pl / 2 + 0.015], [pw + 0.06, 0.03, 0, -pl / 2 - 0.015],
            [0.03, pl, pw / 2 + 0.015, 0], [0.03, pl, -pw / 2 - 0.015, 0]]) {
            const f = new THREE.Mesh(new THREE.BoxGeometry(fw, 0.02, fl), frameMat);
            f.position.set(x + fx, H - 0.01, pz + fz);
            scene.add(f);
        }
    }

    lighting.panels.push({ light, mat, max: 7 });
}

let aoTexture = null;

/** Köşelerde ışığın azalmasını taklit eden, kenardan içeri sönen koyu şerit. */
function aoStrip(length, width, opacity) {
    aoTexture ??= canvasTexture(4, 128, (g, w, h) => {
        const grad = g.createLinearGradient(0, 0, 0, h);
        grad.addColorStop(0, 'rgba(0,0,0,1)');
        grad.addColorStop(0.35, 'rgba(0,0,0,0.35)');
        grad.addColorStop(1, 'rgba(0,0,0,0)');
        g.fillStyle = grad;
        g.fillRect(0, 0, w, h);
    });
    return new THREE.Mesh(
        new THREE.PlaneGeometry(length, width),
        new THREE.MeshBasicMaterial({ map: aoTexture, transparent: true, opacity, depthWrite: false }),
    );
}

/**
 * Bir duvarın dibi (duvar ve zemin tarafı) ile tavan birleşimi için gölge şeritleri.
 * (x, z) duvar yüzeyinin ortası, rotY duvarın odaya bakan yönü.
 */
function cornerShade(x, z, rotY, length, H, wallHeight = H) {
    const g = new THREE.Group();
    g.position.set(x, 0, z);
    g.rotation.y = rotY;

    const wallBase = aoStrip(length, 0.45, 0.22);
    wallBase.rotation.z = Math.PI;
    wallBase.position.set(0, 0.225, 0.004);
    g.add(wallBase);

    const floorEdge = aoStrip(length, 0.5, 0.28);
    floorEdge.rotation.x = -Math.PI / 2;
    floorEdge.position.set(0, 0.003, 0.25);
    g.add(floorEdge);

    if (wallHeight >= H - 0.01) {
        const top = aoStrip(length, 0.6, 0.16);
        top.position.set(0, H - 0.3, 0.004);
        g.add(top);
    }
    scene.add(g);
}

function buildRoom(W, D, H, railXs = null, panelXs = null) {
    const wood = woodTexture();
    wood.repeat.set(W / 2.4, D / 4.8);
    floor = new THREE.Mesh(
        new THREE.PlaneGeometry(W, D),
        new THREE.MeshStandardMaterial({ map: wood, roughness: 0.38, metalness: 0 }),
    );
    floor.rotation.x = -Math.PI / 2;
    floor.receiveShadow = true;
    scene.add(floor);

    const ceiling = new THREE.Mesh(
        new THREE.PlaneGeometry(W, D),
        new THREE.MeshStandardMaterial({ color: '#fafafa', roughness: 1, emissive: '#f4f1ec', emissiveIntensity: 0.35 }),
    );
    lighting.ceiling = ceiling.material;
    ceiling.rotation.x = Math.PI / 2;
    ceiling.position.y = H;
    scene.add(ceiling);

    const walls = [
        { w: W, pos: [0, H / 2, -D / 2], rot: 0 },
        { w: W, pos: [0, H / 2, D / 2], rot: Math.PI },
        { w: D, pos: [-W / 2, H / 2, 0], rot: Math.PI / 2 },
        { w: D, pos: [W / 2, H / 2, 0], rot: -Math.PI / 2 },
    ];
    const baseMat = new THREE.MeshStandardMaterial({ color: '#ffffff', roughness: 0.6 });
    for (const def of walls) {
        const wall = new THREE.Mesh(new THREE.PlaneGeometry(def.w, H), wallMat);
        wall.position.set(...def.pos);
        wall.rotation.y = def.rot;
        wall.receiveShadow = true;
        wall.userData.occluder = true;
        scene.add(wall);
        occluders.push(wall);

        const base = new THREE.Mesh(new THREE.BoxGeometry(def.w, 0.09, 0.015), baseMat);
        base.position.set(def.pos[0], 0.045, def.pos[2]);
        base.rotation.y = def.rot;
        base.translateZ(0.0075);
        scene.add(base);
        cornerShade(def.pos[0], def.pos[2], def.rot, def.w, H);
    }

    lighting.hemi = new THREE.HemisphereLight('#ffffff', '#cbbca8', 1.1);
    scene.add(lighting.hemi);
    for (const x of panelXs ?? [0]) addCeilingPanel(x, 0, H, 1.2, Math.max(2, D - 4));

    // tavan rayları
    const railMat = new THREE.MeshStandardMaterial({ color: '#222', roughness: 0.4, metalness: 0.6 });
    for (const x of railXs ?? [-W / 2 + 1.3, W / 2 - 1.3]) {
        const rail = new THREE.Mesh(new THREE.BoxGeometry(0.04, 0.03, D - 0.6), railMat);
        rail.position.set(x, H - 0.015, 0);
        scene.add(rail);
    }
    const rail = new THREE.Mesh(new THREE.BoxGeometry(W - 2.6, 0.03, 0.04), railMat);
    rail.position.set(0, H - 0.015, -D / 2 + 1.3);
    scene.add(rail);

    const hoverRing = new THREE.Mesh(
        new THREE.RingGeometry(0.16, 0.2, 40),
        new THREE.MeshBasicMaterial({ color: '#ffffff', transparent: true, opacity: 0.75, depthWrite: false }),
    );
    hoverRing.rotation.x = -Math.PI / 2;
    hoverRing.visible = false;
    scene.add(hoverRing);
    controls.ring = hoverRing;
}

function layoutWall(list, wallCenter, normal, H, gap, labelSpace) {
    if (!list.length) return;
    const rightDir = new THREE.Vector3(0, 1, 0).cross(normal);
    const total = list.reduce((s, it) => s + it.size.w + labelSpace, 0) + gap * (list.length - 1);
    let s = -total / 2;
    for (const it of list) {
        const pos = wallCenter.clone().addScaledVector(rightDir, s + it.size.w / 2);
        placeOnWall(it, pos, normal, H, !isMobile);
        s += it.size.w + labelSpace + gap;
    }
}

function placeOnWall(it, wallPoint, normal, H, shadows, realSpot = true) {
    const { w, h, d } = it.size;
    // müze standardı: merkez ~150 cm, büyük eserlerde alt kenar yerden en az 35 cm
    const cy = Math.max(1.5, h / 2 + 0.35);

    const group = new THREE.Group();
    group.position.set(wallPoint.x, cy, wallPoint.z);
    group.rotation.y = Math.atan2(normal.x, normal.z);
    scene.add(group);

    const side = new THREE.MeshStandardMaterial({ color: '#e7e2d8', roughness: 0.9 });
    const front = new THREE.MeshStandardMaterial({ map: it.tex, roughness: 0.62, color: it.tex ? '#ffffff' : '#cccccc' });
    const mesh = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), [side, side, side, side, front, side]);
    mesh.position.z = d / 2 + 0.004;
    mesh.castShadow = true;
    group.add(mesh);

    // etiket ışıklandırmadan bağımsız, her zaman net okunur (salonda yaklaşınca yazılır)
    const label = new THREE.Mesh(
        new THREE.BoxGeometry(LABEL.w, LABEL.h, 0.006),
        [0, 1, 2, 3, 4, 5].map((i) => new THREE.MeshBasicMaterial(
            i === 4
                ? { map: realSpot ? labelTexture(it.art) : null, toneMapped: false, color: '#ffffff' }
                : { color: '#d0d0d0' },
        )),
    );
    label.position.set(w / 2 + 0.22 + LABEL.w / 2, 1.45 - cy, 0.003);
    group.add(label);
    lighting.basics.push(...label.material);

    const center = new THREE.Vector3(wallPoint.x, cy, wallPoint.z).addScaledVector(normal, d);

    // eseri tavandan aydınlatan spot (salonda havuzdan atanır). Aydınlatma tasarımındaki
    // 30° kuralı: armatür, eserin merkezine dikeyle ~30° açı yapacak kadar duvardan uzakta.
    const drop = H - 0.06 - cy;
    const offset = THREE.MathUtils.clamp(drop * Math.tan(THREE.MathUtils.degToRad(30)), 0.9, 1.7);
    const spotPos = center.clone().addScaledVector(normal, offset).setY(H - 0.06);
    // ışığa yakın üst kenar daha parlak kalmasın diye nişan ortanın biraz altında
    const aim = center.clone().setY(cy - h * 0.12);
    const dist = spotPos.distanceTo(aim);
    // yumuşak kenarlı, eseri bir pay ile saran hüzme; küçük eserde dar, büyükte geniş
    const spotAngle = Math.min(1.15, Math.atan((Math.max(w, h) * 0.72 + 0.18) / dist));
    const spotMax = 6 + dist * dist * 2.9;
    let spot = null, halo = null;
    if (realSpot) {
        spot = new THREE.SpotLight(SPOT_COLOR, spotMax, 0, spotAngle, 0.95, 2);
        spot.position.copy(spotPos);
        spot.target.position.copy(aim);
        if (shadows) {
            spot.castShadow = true;
            spot.shadow.mapSize.set(1024, 1024);
            spot.shadow.bias = -0.0004;
            spot.shadow.normalBias = 0.02;
            spot.shadow.radius = 6;
            spot.shadow.blurSamples = 16;
        }
        scene.add(spot, spot.target);
    } else {
        halo = new THREE.Mesh(
            new THREE.PlaneGeometry(Math.max(w, h) * 1.5 + 0.7, h * 1.6 + 0.9),
            new THREE.MeshBasicMaterial({
                map: getHaloTexture(), transparent: true, depthWrite: false, blending: THREE.AdditiveBlending, opacity: 0,
            }),
        );
        halo.position.set(0, 0.05, 0.002);
        group.add(halo);
        front.emissive = new THREE.Color('#ffffff');
        front.emissiveMap = it.tex;
        front.emissiveIntensity = 0;
    }

    const fixture = new THREE.Mesh(
        new THREE.CylinderGeometry(0.045, 0.06, 0.16, 20),
        new THREE.MeshStandardMaterial({ color: '#1d1d1d', roughness: 0.4, metalness: 0.5 }),
    );
    fixture.position.copy(spotPos).setY(H - 0.1);
    fixture.lookAt(aim);
    fixture.rotateX(Math.PI / 2);
    scene.add(fixture);

    // armatürün ışık veren camı, spotla birlikte parlar/söner
    const lens = new THREE.Mesh(
        new THREE.CircleGeometry(0.04, 24),
        new THREE.MeshBasicMaterial({ color: '#fff3dc', toneMapped: false }),
    );
    lens.position.y = 0.081;
    lens.rotation.x = -Math.PI / 2;
    fixture.add(lens);

    const entry = {
        art: it.art, mesh, label, front, center, normal: normal.clone(), size: it.size, full: false,
        spot, spotMax, spotPos, spotAngle, aim, lens, halo, on: true, level: 1,
        poolSlot: null, poolLevel: 0, labelMat: label.material[4], labelReady: realSpot,
    };
    entry.switch = buildSwitch(entry, group, -(w / 2 + 0.28), 1.1 - cy);
    mesh.userData.entry = label.userData.entry = entry;
    arts.push(entry);
    clickables.push(mesh, label);
}

/** Duvara gömme, ışıklı (gösterge LED'li) tek anahtar; 110 cm yükseklikte. */
function buildSwitch(entry, group, x, y) {
    const sw = new THREE.Group();
    sw.position.set(x, y, 0);
    group.add(sw);

    const plate = new THREE.Mesh(new RoundedBoxGeometry(0.086, 0.086, 0.01, 3, 0.008), switchMat);
    plate.position.z = 0.005;
    plate.castShadow = true;
    sw.add(plate);

    const bezel = new THREE.Mesh(new RoundedBoxGeometry(0.06, 0.066, 0.004, 2, 0.003), switchBezelMat);
    bezel.position.z = 0.0105;
    sw.add(bezel);

    // düğme ortasından devrilir
    const pivot = new THREE.Group();
    pivot.position.z = 0.012;
    sw.add(pivot);
    const rocker = new THREE.Mesh(new RoundedBoxGeometry(0.052, 0.058, 0.01, 3, 0.004), switchMat);
    rocker.position.z = 0.004;
    rocker.castShadow = true;
    pivot.add(rocker);

    const led = new THREE.Mesh(
        new THREE.CircleGeometry(0.0028, 16),
        new THREE.MeshBasicMaterial({ color: '#ff8a1f', toneMapped: false }),
    );
    led.position.set(0, -0.019, 0.0092);
    pivot.add(led);

    for (const m of [plate, bezel, rocker, led]) m.userData.switchFor = entry;
    clickables.push(plate, bezel, rocker, led);
    return { pivot, led };
}

let audio = null;
function clickSound(on) {
    try {
        audio ??= new AudioContext();
        const t = audio.currentTime;
        const len = Math.floor(audio.sampleRate * 0.025);
        const buf = audio.createBuffer(1, len, audio.sampleRate);
        const ch = buf.getChannelData(0);
        for (let i = 0; i < len; i++) ch[i] = (Math.random() * 2 - 1) * Math.pow(1 - i / len, 4);
        const src = audio.createBufferSource();
        src.buffer = buf;
        const filter = audio.createBiquadFilter();
        filter.type = 'bandpass';
        filter.frequency.value = on ? 2600 : 1900;
        filter.Q.value = 1.2;
        const gain = audio.createGain();
        gain.gain.setValueAtTime(0.55, t);
        src.connect(filter).connect(gain).connect(audio.destination);
        src.start(t);
    } catch {
        // ses desteklenmiyorsa sessiz geç
    }
}

function toggleSpot(entry) {
    entry.on = !entry.on;
    clickSound(entry.on);
}

function setRoomLights(on) {
    lighting.target = on ? 1 : 0;
    document.getElementById('btn-lights').classList.toggle('on', on);
    clickSound(on);
}

/** Işık geçişleri: her karede hedefe doğru yumuşakça yaklaşır. */
function updateLights(dt) {
    lighting.level += (lighting.target - lighting.level) * Math.min(1, dt * 7);
    const a = lighting.level;
    if (lighting.hemi) lighting.hemi.intensity = 0.05 + 1.05 * a;
    scene.environmentIntensity = 0.03 + 0.27 * a;
    if (lighting.ceiling) lighting.ceiling.emissiveIntensity = 0.03 + 0.32 * a;
    for (const p of lighting.panels) {
        p.light.intensity = p.max * a;
        // kapalı opal cam koyu griye döner (renk doğrusal uzayda, küçük değer yeterince koyu)
        p.mat.color.setRGB(1, 0.98, 0.95).multiplyScalar(0.012 + 0.988 * a * a);
    }
    for (const m of lighting.basics) m.color.setScalar(0.3 + 0.7 * a);

    for (const slot of spotPool) {
        slot.fade = slot.entry ? Math.min(1, slot.fade + dt * 4) : 0;
        slot.light.intensity = slot.entry ? slot.entry.spotMax * slot.entry.level * slot.fade : 0;
    }

    for (const e of arts) {
        e.level += ((e.on ? 1 : 0) - e.level) * Math.min(1, dt * 10);
        if (e.spot) {
            e.spot.intensity = e.spotMax * e.level;
        } else {
            // gerçek spot gelene kadar duvardaki hüzme ve hafif parlama ışığı taklit eder
            e.poolLevel += ((e.poolSlot ? e.poolSlot.fade : 0) - e.poolLevel) * Math.min(1, dt * 8);
            const fake = e.level * (1 - e.poolLevel);
            e.halo.material.opacity = fake * (0.35 + 0.5 * (1 - lighting.level));
            e.front.emissiveIntensity = fake * 0.22;
        }
        e.lens.material.color.setRGB(1, 0.95, 0.86).multiplyScalar(0.12 + 0.88 * e.level);
        const sw = e.switch;
        sw.pivot.rotation.x += ((e.on ? -0.13 : 0.13) - sw.pivot.rotation.x) * Math.min(1, dt * 30);
        // spot kapalıyken gösterge yanar, karanlıkta anahtar bulunabilsin
        sw.led.material.color.setRGB(1, 0.54, 0.12).multiplyScalar(e.on ? 0.15 : 1);
    }
}

function buildBench(x, z, rotY) {
    const bench = new THREE.Group();
    const top = new THREE.Mesh(
        new THREE.BoxGeometry(1.6, 0.06, 0.45),
        new THREE.MeshStandardMaterial({ color: '#7a5634', roughness: 0.55 }),
    );
    top.position.y = 0.43;
    top.castShadow = true;
    bench.add(top);
    const legMat = new THREE.MeshStandardMaterial({ color: '#1d1d1d', roughness: 0.4, metalness: 0.6 });
    for (const x of [-0.7, 0.7]) {
        const leg = new THREE.Mesh(new THREE.BoxGeometry(0.04, 0.4, 0.4), legMat);
        leg.position.set(x, 0.2, 0);
        bench.add(leg);
    }
    bench.position.set(x, 0, z);
    bench.rotation.y = rotY;
    bench.add(contactShadow(1.9, 0.75));
    scene.add(bench);
    const along = Math.abs(Math.sin(rotY)) > 0.5;
    rects.push({ x, z, hx: along ? 0.525 : 1.1, hz: along ? 1.1 : 0.525 });
}

function contactShadow(w, d, opacity = 0.35) {
    const tex = canvasTexture(256, 256, (g, cw, ch) => {
        const grad = g.createRadialGradient(cw / 2, ch / 2, 0, cw / 2, ch / 2, cw / 2);
        grad.addColorStop(0, `rgba(0,0,0,${opacity})`);
        grad.addColorStop(1, 'rgba(0,0,0,0)');
        g.fillStyle = grad;
        g.fillRect(0, 0, cw, ch);
    });
    const m = new THREE.Mesh(
        new THREE.PlaneGeometry(w, d),
        new THREE.MeshBasicMaterial({ map: tex, transparent: true, depthWrite: false }),
    );
    m.rotation.x = -Math.PI / 2;
    m.position.y = 0.002;
    return m;
}

/** Giriş köşesinde koltuk, yan sehpa ve üzerinde kitaplar. */
function buildLounge(W, D) {
    const corner = new THREE.Group();
    const x = -W / 2 + 0.8, z = D / 2 - 0.8;
    corner.position.set(x, 0, z);
    // koltuk ana esere dönük
    corner.rotation.y = Math.atan2(-x, -D / 2 - z);
    scene.add(corner);

    const velvet = new THREE.MeshPhysicalMaterial({
        color: '#4a5e50', roughness: 0.85, sheen: 1, sheenRoughness: 0.45, sheenColor: '#9fb5a4',
    });
    const brass = new THREE.MeshStandardMaterial({ color: '#b8925a', roughness: 0.3, metalness: 0.9 });
    const part = (geo, mat, px, py, pz, rx = 0) => {
        const m = new THREE.Mesh(geo, mat);
        m.position.set(px, py, pz);
        m.rotation.x = rx;
        m.castShadow = m.receiveShadow = true;
        corner.add(m);
        return m;
    };

    // koltuk
    part(new RoundedBoxGeometry(0.84, 0.14, 0.78, 4, 0.05), velvet, 0, 0.3, 0);
    part(new RoundedBoxGeometry(0.62, 0.15, 0.62, 5, 0.06), velvet, 0, 0.43, 0.05);
    part(new RoundedBoxGeometry(0.84, 0.56, 0.17, 5, 0.07), velvet, 0, 0.62, -0.31, -0.17);
    for (const side of [-1, 1]) {
        part(new RoundedBoxGeometry(0.13, 0.3, 0.74, 5, 0.06), velvet, side * 0.355, 0.5, 0);
        for (const front of [-1, 1]) {
            const leg = part(new THREE.CylinderGeometry(0.016, 0.011, 0.24, 12), brass, side * 0.36, 0.12, front * 0.32);
            leg.rotation.z = side * 0.08;
            leg.rotation.x = front * 0.08;
        }
    }
    corner.add(contactShadow(1.2, 1.1, 0.4));

    // yan sehpa
    const tx = 0.68, tz = 0.12;
    const stone = new THREE.MeshStandardMaterial({ color: '#ddd3c4', roughness: 0.45 });
    const black = new THREE.MeshStandardMaterial({ color: '#1d1d1d', roughness: 0.4, metalness: 0.5 });
    part(new THREE.CylinderGeometry(0.23, 0.23, 0.03, 48), stone, tx, 0.52, tz);
    part(new THREE.CylinderGeometry(0.02, 0.02, 0.5, 16), black, tx, 0.26, tz);
    part(new THREE.CylinderGeometry(0.15, 0.16, 0.02, 40), black, tx, 0.01, tz);
    const books = [['#c9b79c', 0.03], ['#2f3b4a', 0.025], ['#8c3b2e', 0.028]];
    let by = 0.535;
    books.forEach(([color, hgt], i) => {
        const b = part(new THREE.BoxGeometry(0.2, hgt, 0.15), new THREE.MeshStandardMaterial({ color, roughness: 0.7 }), tx - 0.02, by + hgt / 2, tz);
        b.rotation.y = (i - 1) * 0.18;
        by += hgt;
    });
    const tableShadow = contactShadow(0.6, 0.6, 0.3);
    tableShadow.position.set(tx, 0.002, tz);
    corner.add(tableShadow);

    corner.updateMatrixWorld();
    const tableWorld = new THREE.Vector3(tx, 0, tz).applyMatrix4(corner.matrixWorld);
    obstacles.push({ x, z, r: 0.75 }, { x: tableWorld.x, z: tableWorld.z, r: 0.45 });
}

function buildEntranceSign(W, D, H, photoTex) {
    const group = new THREE.Group();
    group.position.set(0, 0, D / 2 - 0.01);
    group.rotation.y = Math.PI;
    scene.add(group);

    const top = Math.min(H - 0.3, 3.0);
    const tex = canvasTexture(2048, 512, (g, w, h) => {
        g.fillStyle = '#1a1a1a';
        g.textAlign = 'center';
        g.font = '300 190px Prompt, sans-serif';
        g.fillText('BeArtShare', w / 2, 230);
        g.font = '400 84px Prompt, sans-serif';
        g.fillStyle = '#555';
        g.fillText(data.subtitle || data.artist, w / 2, 400);
    });
    const sign = new THREE.Mesh(
        new THREE.PlaneGeometry(3.2, 0.8),
        new THREE.MeshBasicMaterial({ map: tex, transparent: true, toneMapped: false }),
    );
    lighting.basics.push(sign.material);
    sign.position.y = top - 0.4;
    group.add(sign);

    if (!photoTex) return;

    // sanatçı fotoğrafı, ince siyah çerçeveli
    const size = 0.9;
    const cy = Math.max(size / 2 + 0.9, top - 0.8 - 0.1 - size / 2);
    const frame = new THREE.Mesh(
        new THREE.BoxGeometry(size + 0.04, size + 0.04, 0.03),
        new THREE.MeshStandardMaterial({ color: '#1a1a1a', roughness: 0.5 }),
    );
    frame.position.set(0, cy, 0.015);
    frame.castShadow = true;
    group.add(frame);

    const photo = new THREE.Mesh(
        new THREE.PlaneGeometry(size, size),
        new THREE.MeshStandardMaterial({ map: photoTex, roughness: 0.7 }),
    );
    photo.position.set(0, cy, 0.031);
    group.add(photo);

    const spot = new THREE.SpotLight(SPOT_COLOR, 0, 0, 0.65, 0.95, 2);
    const center = new THREE.Vector3(0, cy, D / 2 - 0.05);
    spot.position.set(0, H - 0.06, D / 2 - 1.3);
    spot.intensity = 10 + spot.position.distanceToSquared(center) * 4;
    spot.target.position.copy(center);
    scene.add(spot, spot.target);
}

// ---------------------------------------------------------------- kontroller

const controls = {
    yaw: 0,
    pitch: 0,
    keys: new Set(),
    tween: null,
    ring: null,
    pointer: null,
};
const raycaster = new THREE.Raycaster();
const ndc = new THREE.Vector2();

function setCameraRotation() {
    controls.pitch = THREE.MathUtils.clamp(controls.pitch, -1.2, 1.2);
    camera.rotation.set(controls.pitch, controls.yaw, 0);
}

function lookAtPoint(p) {
    const dx = p.x - camera.position.x;
    const dz = p.z - camera.position.z;
    controls.yaw = Math.atan2(-dx, -dz);
    controls.pitch = Math.atan2(p.y - camera.position.y, Math.hypot(dx, dz));
    setCameraRotation();
}

function clampPosition(p) {
    const m = 0.45;
    p.x = THREE.MathUtils.clamp(p.x, -room.W / 2 + m, room.W / 2 - m);
    p.z = THREE.MathUtils.clamp(p.z, -room.D / 2 + m, room.D / 2 - m);
    // bank ve ara duvarların içinden geçme
    for (const r of rects) {
        const lx = p.x - r.x, lz = p.z - r.z;
        if (Math.abs(lx) < r.hx && Math.abs(lz) < r.hz) {
            if (r.hx - Math.abs(lx) < r.hz - Math.abs(lz)) p.x = r.x + Math.sign(lx || 1) * r.hx;
            else p.z = r.z + Math.sign(lz || 1) * r.hz;
        }
    }
    for (const o of obstacles) {
        const dx = p.x - o.x, dz = p.z - o.z, dist = Math.hypot(dx, dz);
        if (dist < o.r) {
            p.x = dist ? o.x + (dx / dist) * o.r : o.x + o.r;
            p.z = dist ? o.z + (dz / dist) * o.r : o.z;
        }
    }
    p.y = EYE;
    return p;
}

function shortestAngle(from, to) {
    let d = (to - from) % (Math.PI * 2);
    if (d > Math.PI) d -= Math.PI * 2;
    if (d < -Math.PI) d += Math.PI * 2;
    return from + d;
}

function moveTo(position, look, duration = 1200) {
    const target = clampPosition(position.clone());
    let yaw = controls.yaw, pitch = 0;
    if (look) {
        const dx = look.x - target.x, dz = look.z - target.z;
        yaw = shortestAngle(controls.yaw, Math.atan2(-dx, -dz));
        pitch = Math.atan2(look.y - EYE, Math.hypot(dx, dz));
    } else {
        pitch = controls.pitch * 0.3;
    }
    controls.tween = {
        t0: performance.now(),
        duration,
        from: { pos: camera.position.clone(), yaw: controls.yaw, pitch: controls.pitch },
        to: { pos: target, yaw, pitch },
    };
}

/** Önce hızlı (Thumbor) görsel, ardından R2'deki orijinal dosya. */
async function upgradeTexture(entry) {
    if (entry.full || !entry.art.imageFull) return;
    entry.full = true;
    const tex = await loadTexture(entry.art.imageFull);
    if (!tex) return;
    const old = entry.front.map;
    entry.front.map = tex;
    entry.front.needsUpdate = true;
    old?.dispose();
}

function focusOn(entry, duration = 1300) {
    upgradeTexture(entry);
    const { w, h } = entry.size;
    const vFov = THREE.MathUtils.degToRad(camera.fov);
    const tanV = Math.tan(vFov / 2);
    const dist = THREE.MathUtils.clamp(
        Math.max((h * 1.4) / 2 / tanV, (w * 1.4) / 2 / (tanV * camera.aspect), 1.2),
        1.2,
        Math.max(room.W, room.D) - 1,
    );
    const pos = entry.center.clone().addScaledVector(entry.normal, dist);
    moveTo(pos, entry.center, duration);
}

function pick(clientX, clientY) {
    ndc.set((clientX / window.innerWidth) * 2 - 1, -(clientY / window.innerHeight) * 2 + 1);
    raycaster.setFromCamera(ndc, camera);
    const hit = raycaster.intersectObjects([...clickables, floor, ...occluders], false)[0];
    return hit && !hit.object.userData.occluder ? hit : null;
}

const dom = renderer.domElement;
dom.addEventListener('pointerdown', (e) => {
    controls.pointer = { id: e.pointerId, x: e.clientX, y: e.clientY, moved: 0 };
    dom.setPointerCapture(e.pointerId);
});
dom.addEventListener('pointermove', (e) => {
    const p = controls.pointer;
    if (p && p.id === e.pointerId) {
        const dx = e.clientX - p.x, dy = e.clientY - p.y;
        p.moved += Math.abs(dx) + Math.abs(dy);
        p.x = e.clientX;
        p.y = e.clientY;
        if (p.moved > 4) {
            controls.tween = null;
            const k = isMobile ? 0.005 : 0.0035;
            controls.yaw += dx * k;
            controls.pitch += dy * k;
            setCameraRotation();
        }
        controls.ring.visible = false;
        return;
    }
    if (!floor) return;
    const hit = pick(e.clientX, e.clientY);
    const onArt = hit && (hit.object.userData.entry || hit.object.userData.switchFor);
    dom.style.cursor = hit ? 'pointer' : 'grab';
    controls.ring.visible = !!hit && !onArt;
    if (hit && !onArt) controls.ring.position.set(hit.point.x, 0.003, hit.point.z);
});
dom.addEventListener('pointerup', (e) => {
    const p = controls.pointer;
    controls.pointer = null;
    if (!p || p.moved > 8 || !floor) return;
    const hit = pick(e.clientX, e.clientY);
    if (!hit) return;
    if (hit.object.userData.switchFor) {
        toggleSpot(hit.object.userData.switchFor);
    } else if (hit.object.userData.entry) {
        focusOn(hit.object.userData.entry);
    } else {
        moveTo(new THREE.Vector3(hit.point.x, EYE, hit.point.z));
    }
});
dom.addEventListener('pointercancel', () => (controls.pointer = null));
dom.addEventListener('wheel', (e) => {
    e.preventDefault();
    controls.tween = null;
    const fwd = new THREE.Vector3(-Math.sin(controls.yaw), 0, -Math.cos(controls.yaw));
    camera.position.addScaledVector(fwd, -Math.sign(e.deltaY) * 0.35);
    clampPosition(camera.position);
}, { passive: false });

window.addEventListener('keydown', (e) => {
    if (['KeyW', 'KeyA', 'KeyS', 'KeyD', 'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.code)) {
        controls.keys.add(e.code);
        controls.tween = null;
        e.preventDefault();
    }
});
window.addEventListener('keyup', (e) => controls.keys.delete(e.code));
window.addEventListener('blur', () => controls.keys.clear());

window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
});

// ---------------------------------------------------------------- arayüz

document.getElementById('btn-focus').addEventListener('click', () => {
    if (!arts.length) return;
    if (!HALL) return focusOn(arts[0]);
    const cam = camera.position;
    const near = [...arts].sort((a, b) => a.center.distanceToSquared(cam) - b.center.distanceToSquared(cam))[0];
    focusOn(near);
});
document.getElementById('btn-lights').addEventListener('click', (e) => {
    setRoomLights(lighting.target < 0.5);
    e.currentTarget.blur();
});

document.querySelectorAll('.swatch').forEach((btn) => {
    btn.addEventListener('click', () => {
        wallMat.color.set(btn.dataset.wall);
        matchSwitchesToWall();
        document.querySelectorAll('.swatch').forEach((b) => b.classList.toggle('on', b === btn));
    });
});

const info = document.getElementById('info');
const field = (name) => info.querySelector(`[data-f="${name}"]`);
let shownEntry = null;

function showInfo(entry) {
    if (entry === shownEntry) return;
    shownEntry = entry;
    if (!entry) {
        info.classList.add('hidden');
        return;
    }
    const a = entry.art;
    field('artist').textContent = a.artist;
    field('title').textContent = a.title;
    field('meta').innerHTML = '';
    for (const line of [[a.technique, a.year].filter(Boolean).join(', '), a.dimensionsText]) {
        if (!line) continue;
        const div = document.createElement('div');
        div.textContent = line;
        field('meta').appendChild(div);
    }
    const price = field('price');
    price.innerHTML = '';
    price.className = a.sold || a.reserved ? '' : 'price';
    if (a.sold || a.reserved) {
        const b = document.createElement('span');
        b.className = 'badge';
        b.textContent = a.sold ? 'Satıldı' : 'Rezerve';
        price.appendChild(b);
    } else if (a.price) {
        price.textContent = a.price;
    }
    field('url').href = a.url;
    info.classList.remove('hidden');
}

const center = new THREE.Vector2(0, 0);
let infoTick = 0;
function updateInfo(now) {
    if (now - infoTick < 150) return;
    infoTick = now;
    raycaster.setFromCamera(center, camera);
    const hit = raycaster.intersectObjects([...clickables, ...occluders], false)[0];
    showInfo(hit && hit.distance < 5 ? hit.object.userData.entry : null);
}

// ---------------------------------------------------------------- döngü

const ease = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);
let last = performance.now();

function frame(now) {
    const dt = Math.min(0.05, (now - last) / 1000);
    last = now;

    const tw = controls.tween;
    if (tw) {
        const t = Math.min(1, (now - tw.t0) / tw.duration);
        const k = ease(t);
        camera.position.lerpVectors(tw.from.pos, tw.to.pos, k);
        controls.yaw = THREE.MathUtils.lerp(tw.from.yaw, tw.to.yaw, k);
        controls.pitch = THREE.MathUtils.lerp(tw.from.pitch, tw.to.pitch, k);
        setCameraRotation();
        if (t >= 1) controls.tween = null;
    }

    const k = controls.keys;
    if (k.size && room) {
        const fwd = (k.has('KeyW') || k.has('ArrowUp') ? 1 : 0) - (k.has('KeyS') || k.has('ArrowDown') ? 1 : 0);
        const strafe = (k.has('KeyD') ? 1 : 0) - (k.has('KeyA') ? 1 : 0);
        const turn = (k.has('ArrowLeft') ? 1 : 0) - (k.has('ArrowRight') ? 1 : 0);
        controls.yaw += turn * dt * 1.6;
        setCameraRotation();
        const sin = Math.sin(controls.yaw), cos = Math.cos(controls.yaw);
        camera.position.x += (-sin * fwd + cos * strafe) * dt * 2.2;
        camera.position.z += (-cos * fwd - sin * strafe) * dt * 2.2;
        clampPosition(camera.position);
    }

    if (room) updatePool(now);
    updateLights(dt);
    if (room) updateInfo(now);
    renderer.render(scene, camera);
    requestAnimationFrame(frame);
}

requestAnimationFrame(frame);
build();
