// ============================================
// SK EDUCATION - CONFIGURATION
// NEW FIREBASE PROJECT: simaji-ff970
// ============================================

// ============================================
// 1. FIREBASE CONFIGURATION (UPDATED)
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
// 7. EXPOSE GLOBALLY
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

console.log('✅ SK Education Config Loaded!');
console.log('🔥 Firebase Project: simaji-ff970');