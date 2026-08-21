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
// 3. BATCH DETAILS
// ============================================
const BATCHES = {
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

// ============================================
// 4. CATEGORIES
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
// 5. SUBCATEGORIES
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
// 6. UTILITY FUNCTIONS
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
// 7. ERROR / LOADING HELPERS  (NEW - fixes infinite loading bug)
// ============================================
// Firebase ke .on('value', cb) calls agar Database Rules permission
// deny kar dein to purane code mein koi error hi nahi dikhta tha —
// page hamesha "Loading..." pe atka reh jaata tha. Ye helper function
// har jagah use hoga taaki asli error user ko dikhe.
function showDbError(container, err) {
    console.error('🔥 Firebase Error:', err);
    let msg = '❌ Data load nahi ho paya.';
    let hint = 'Kuch der baad phir try karein.';

    if (err && err.code === 'PERMISSION_DENIED') {
        msg = '🔒 Permission Denied';
        hint = 'Firebase Database Rules mein read access allow nahi hai. Firebase Console → Realtime Database → Rules mein jaakar ".read": true set karein.';
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

// Agar 10 second ke andar data na aaye (na success, na error) to
// bhi user ko batado ki kuch gadbad hai, taaki "Loading..." hamesha
// ke liye na atka rahe.
function withLoadingTimeout(container, timeoutMs = 10000) {
    const timer = setTimeout(() => {
        if (container && container.innerHTML.includes('Loading')) {
            showDbError(container, { code: 'TIMEOUT' });
        }
    }, timeoutMs);
    return timer;
}

// Firebase connection state ko console mein log karna, debugging ke liye
db.ref('.info/connected').on('value', function(snap) {
    if (snap.val() === true) {
        console.log('✅ Firebase Connected');
    } else {
        console.warn('⚠️ Firebase Disconnected / Connecting...');
    }
});

// ============================================
// 8. EXPOSE GLOBALLY
// ============================================
window.db = db;
window.getUserName = getUserName;
window.setUserName = setUserName;
window.clearUserName = clearUserName;
window.isUserLoggedIn = isUserLoggedIn;
window.getUrlParam = getUrlParam;
window.BATCHES = BATCHES;
window.CATEGORIES = CATEGORIES;
window.CATEGORY_ICONS = CATEGORY_ICONS;
window.SUBCATEGORIES = SUBCATEGORIES;
window.ADMIN_EMAIL = ADMIN_EMAIL;
window.showDbError = showDbError;
window.withLoadingTimeout = withLoadingTimeout;

console.log('✅ SK Education Config Loaded!');
console.log('🔥 Firebase Project: simaji-ff970');
