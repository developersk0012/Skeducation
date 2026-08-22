// ============================================
// SK EDUCATION - CONFIGURATION
// FIREBASE PROJECT: simaji-ff970
// ============================================

// ============================================
// 1. FIREBASE CONFIGURATION
// ============================================
const firebaseConfig = {
    apiKey: "AIzaSyAzWlTFo9sIycAKkvV2tDSaMB3HqZXCV0U",
    authDomain: "simaji-ff970.firebaseapp.com",
    databaseURL: "https://simaji-ff970-default-rtdb.firebaseio.com",
    projectId: "simaji-ff970",
    storageBucket: "simaji-ff970.firebasestorage.app",
    messagingSenderId: "397843196773",
    appId: "1:397843196773:android:810280a4289abf84288f8c"
};

firebase.initializeApp(firebaseConfig);
const db = firebase.database();

// ============================================
// 2. ADMIN EMAIL (Sirf Reply ke liye)
// ============================================
const ADMIN_EMAIL = 'satendrakkushwaha12@gmail.com';

// ============================================
// 3. WHATSAPP CHANNEL LINK
// ============================================
const WHATSAPP_GROUP_LINK = 'https://whatsapp.com/channel/0029Vb8HmPi8V0tqX1Ojz61U';

// ============================================
// 4. DEFAULT BATCHES
// (Ye sirf tab use hote hain jab Firebase 'batches' node khaali ho —
//  pehli baar seed karne ke liye. Uske baad batches Firebase se
//  aate hain aur admin panel se add/edit/delete ho sakte hain.)
// ============================================
const DEFAULT_BATCHES = {
    '1': {
        name: 'Khazana Batch',
        icon: '🏆',
        image: 'https://i.ibb.co/7tJkXSRJ/IMG-20260820-221756-560.jpg',
        description: 'Morning Batch - Complete Study Material'
    },
    '2': {
        name: 'Disha Online Classes',
        icon: '🌟',
        image: 'https://i.ibb.co/pBGDQq8m/1771220242-10th-batch.webp',
        description: 'Evening Batch - Expert Guidance'
    },
    '3': {
        name: 'Target Board',
        icon: '🎯',
        image: 'https://i.ibb.co/KcDw1wwN/IMG-20260820-222646-987.jpg',
        description: 'Weekend Batch - Board Exam Preparation'
    }
};

// window.BATCHES rehta hai backward-compatibility ke liye (kuch purana
// code isko sync read karta tha). loadBatches() ise hamesha update
// karta rahega jab bhi call ho.
window.BATCHES = DEFAULT_BATCHES;

// ============================================
// 5. CATEGORIES
// ============================================
const CATEGORIES = {
    'science': 'Science',
    'math': 'Mathematics',
    'hindi': 'Hindi',
    'sanskrit': 'Sanskrit',
    'sst': 'Social Studies'
};

const CATEGORY_ICONS = {
    'science': '🔬',
    'math': '📐',
    'hindi': '📝',
    'sanskrit': '🕉️',
    'sst': '🌍'
};

// ============================================
// 6. SUBCATEGORIES
// ============================================
const SUBCATEGORIES = {
    'science': {
        'physics': 'Physics',
        'chemistry': 'Chemistry',
        'biology': 'Biology'
    },
    'math': {
        'objective': 'Objective',
        'subjective': 'Subjective',
        'notes': 'Notes'
    },
    'hindi': {
        'grammar': 'Grammar',
        'book_notes': 'Book Notes',
        'imp_question': 'Important Questions'
    },
    'sanskrit': {
        'grammar': 'Grammar',
        'book_notes': 'Book Notes',
        'imp_question': 'Important Questions'
    },
    'sst': {
        'history': 'History',
        'geography': 'Geography',
        'civics': 'Civics',
        'economics': 'Economics'
    }
};

// ============================================
// 7. UTILITY FUNCTIONS
// ============================================

function getUrlParam(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

function getUserName() {
    return localStorage.getItem('sk_user_name') || null;
}

function setUserName(name) {
    localStorage.setItem('sk_user_name', name);
}

function clearUserName() {
    localStorage.removeItem('sk_user_name');
}

function isUserLoggedIn() {
    return getUserName() !== null;
}

// ============================================
// 8. BATCHES — Firebase se dynamic load
// ============================================
// Admin panel se naye batches add/edit/delete ho sakein, isliye
// batches ab config.js mein hardcoded nahi, balki Firebase ke
// 'batches' node mein store hote hain. Pehli baar (agar node khaali
// mile) DEFAULT_BATCHES se seed kar diya jaata hai taaki purana data
// (batch_id 1,2,3 wale pdfs) kaam karta rahe.
function loadBatches() {
    return db.ref('batches').once('value').then(function(snap) {
        let data = snap.val();
        if (!data) {
            data = DEFAULT_BATCHES;
            db.ref('batches').set(DEFAULT_BATCHES).catch(function(err) {
                console.error('Batch seeding failed:', err);
            });
        }
        window.BATCHES = data;
        return data;
    }).catch(function(err) {
        console.error('loadBatches failed, using defaults:', err);
        window.BATCHES = DEFAULT_BATCHES;
        return DEFAULT_BATCHES;
    });
}

// Naye batch ke liye agla numeric ID nikalta hai (existing IDs mein se max + 1)
function getNextBatchId(batches) {
    const ids = Object.keys(batches).map(Number).filter(function(n) { return !isNaN(n); });
    return ids.length ? Math.max.apply(null, ids) + 1 : 1;
}

// ============================================
// 9. ERROR / LOADING HELPERS
// ============================================
function showDbError(container, err) {
    console.error('🔥 Firebase Error:', err);
    let msg = '❌ Data load nahi ho paya.';
    let hint = 'Kuch der baad phir try karein.';

    if (err && err.code === 'PERMISSION_DENIED') {
        msg = '🔒 Permission Denied';
        hint = 'Firebase Database Rules mein read access allow nahi hai.';
    } else if (!navigator.onLine) {
        msg = '📡 Internet Connection Nahi Hai';
        hint = 'Apna internet connection check karke phir try karein.';
    }

    if (container) {
        container.innerHTML = `
            <div style="text-align:center;padding:40px 20px;color:#f72585;">
                <div style="font-size:42px;margin-bottom:10px;">⚠️</div>
                <div style="font-size:16px;font-weight:600;margin-bottom:6px;">${msg}</div>
                <div style="font-size:13px;color:#a5b4fc;max-width:420px;margin:0 auto;">${hint}</div>
            </div>
        `;
    }
}

function withLoadingTimeout(container, timeoutMs = 10000) {
    const timer = setTimeout(() => {
        if (container && container.innerHTML.includes('Loading')) {
            showDbError(container, { code: 'TIMEOUT' });
        }
    }, timeoutMs);
    return timer;
}

db.ref('.info/connected').on('value', function(snap) {
    if (snap.val() === true) {
        console.log('✅ Firebase Connected');
    } else {
        console.warn('⚠️ Firebase Disconnected / Connecting...');
    }
});

// ============================================
// 10. TOAST NOTIFICATIONS (replaces alert())
// ============================================
function showToast(msg, type) {
    type = type || 'success';
    const toast = document.createElement('div');
    toast.className = 'sk-toast sk-toast-' + type;
    toast.textContent = msg;
    document.body.appendChild(toast);
    requestAnimationFrame(function() { toast.classList.add('show'); });
    setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() { toast.remove(); }, 300);
    }, 2800);
}

// ============================================
// 11. WHATSAPP CHANNEL JOIN POPUP
// ============================================
function showWhatsAppPopup() {
    if (document.getElementById('waModalOverlay')) return; // already showing
    const overlay = document.createElement('div');
    overlay.className = 'wa-modal-overlay';
    overlay.id = 'waModalOverlay';
    overlay.innerHTML = `
        <div class="wa-modal">
            <div class="wa-icon">💬</div>
            <h3>Join Our WhatsApp Channel!</h3>
            <p>Latest updates, study materials aur announcements sabse pehle paane ke liye humara WhatsApp Channel abhi join karein.</p>
            <a href="${WHATSAPP_GROUP_LINK}" target="_blank" class="wa-join-btn">✅ Join Now</a>
            <button class="wa-skip-btn">Maybe Later</button>
        </div>
    `;
    document.body.appendChild(overlay);
    requestAnimationFrame(function() { overlay.classList.add('show'); });

    function closePopup() {
        overlay.classList.remove('show');
        setTimeout(function() { overlay.remove(); }, 300);
    }
    overlay.querySelector('.wa-join-btn').addEventListener('click', closePopup);
    overlay.querySelector('.wa-skip-btn').addEventListener('click', closePopup);
    overlay.addEventListener('click', function(e) { if (e.target === overlay) closePopup(); });
}

// ============================================
// 12. GLOBAL INJECTED STYLES (toast + whatsapp modal)
// Har page mein alag se CSS likhne ke bajaye, ek hi jagah se
// inject karte hain taaki design consistent rahe.
// ============================================
(function injectGlobalStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .sk-toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(20px); background: rgba(26,26,62,0.97); backdrop-filter: blur(12px); color: white; padding: 14px 26px; border-radius: 12px; font-size: 14px; font-weight: 600; box-shadow: 0 10px 40px rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); z-index: 99999; opacity: 0; transition: all 0.3s ease; max-width: 90vw; text-align: center; }
        .sk-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
        .sk-toast-success { border-left: 4px solid #4cc9f0; }
        .sk-toast-error { border-left: 4px solid #f72585; }
        .wa-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 99998; padding: 20px; opacity: 0; transition: opacity 0.3s ease; }
        .wa-modal-overlay.show { opacity: 1; }
        .wa-modal { background: linear-gradient(145deg, #1a1a3e, #2d1b69); border-radius: 22px; padding: 35px 30px; max-width: 380px; width: 100%; text-align: center; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 25px 70px rgba(0,0,0,0.5); transform: scale(0.9); transition: transform 0.3s ease; }
        .wa-modal-overlay.show .wa-modal { transform: scale(1); }
        .wa-modal .wa-icon { font-size: 56px; margin-bottom: 12px; }
        .wa-modal h3 { font-size: 22px; margin-bottom: 8px; color: white; }
        .wa-modal p { color: #a5b4fc; font-size: 14px; margin-bottom: 22px; line-height: 1.5; }
        .wa-modal .wa-join-btn { display: block; width: 100%; padding: 14px; background: linear-gradient(90deg, #25d366, #128c7e); border: none; border-radius: 12px; color: white; font-size: 16px; font-weight: 700; cursor: pointer; text-decoration: none; margin-bottom: 10px; transition: transform 0.2s; box-sizing: border-box; }
        .wa-modal .wa-join-btn:hover { transform: scale(1.02); }
        .wa-modal .wa-skip-btn { display: block; width: 100%; padding: 10px; background: transparent; border: none; color: #6a7a9e; font-size: 13px; cursor: pointer; }
        .wa-modal .wa-skip-btn:hover { color: #a5b4fc; }
    `;
    document.head.appendChild(style);
})();

// ============================================
// 13. EXPOSE GLOBALLY
// ============================================
window.db = db;
window.getUserName = getUserName;
window.setUserName = setUserName;
window.clearUserName = clearUserName;
window.isUserLoggedIn = isUserLoggedIn;
window.getUrlParam = getUrlParam;
window.DEFAULT_BATCHES = DEFAULT_BATCHES;
window.loadBatches = loadBatches;
window.getNextBatchId = getNextBatchId;
window.CATEGORIES = CATEGORIES;
window.CATEGORY_ICONS = CATEGORY_ICONS;
window.SUBCATEGORIES = SUBCATEGORIES;
window.ADMIN_EMAIL = ADMIN_EMAIL;
window.WHATSAPP_GROUP_LINK = WHATSAPP_GROUP_LINK;
window.showDbError = showDbError;
window.withLoadingTimeout = withLoadingTimeout;
window.showToast = showToast;
window.showWhatsAppPopup = showWhatsAppPopup;

console.log('✅ SK Education Config Loaded!');
console.log('🔥 Firebase Project: simaji-ff970');
