// Firebase Configuration
const firebaseConfig = {
    apiKey: "AIzaSyAEA_jznrLsOcIJ5jR0qmBQwigStJ4RiAw",
    authDomain: "my-last-education.firebaseapp.com",
    databaseURL: "https://my-last-education-default-rtdb.firebaseio.com",
    projectId: "my-last-education",
    storageBucket: "my-last-education.firebasestorage.app",
    messagingSenderId: "916561394351",
    appId: "1:916561394351:android:06445fc6d1de531e99f25a"
};

firebase.initializeApp(firebaseConfig);
const db = firebase.database();

// Telegram Configuration
const TELEGRAM_BOT_TOKEN = '7543926628:AAH8gY72WZDw-q1F4eIFYu13GaTnRQY-EMc';
const TELEGRAM_CHAT_ID = '6416284194';
const ADMIN_EMAIL = 'satendrakkushwaha12@gmail.com';

// Batch Details
const BATCHES = {
    1: {
        name: 'Khazana Batch',
        image: 'https://i.ibb.co/7tJkXSRJ/IMG-20260820-221756-560.jpg',
        description: 'Morning Batch - Complete Study Material'
    },
    2: {
        name: 'Disha Online Classes',
        image: 'https://i.ibb.co/pBGDQq8m/1771220242-10th-batch.webp',
        description: 'Evening Batch - Expert Guidance'
    },
    3: {
        name: 'Target Board',
        image: 'https://i.ibb.co/KcDw1wwN/IMG-20260820-222646-987.jpg',
        description: 'Weekend Batch - Board Exam Preparation'
    }
};

// Categories
const CATEGORIES = {
    'science': '🔬 Science',
    'math': '📐 Mathematics', 
    'hindi': '📝 Hindi',
    'sanskrit': '🕉️ Sanskrit',
    'sst': '🌍 Social Studies'
};

// Subcategories
const SUBCATEGORIES = {
    'science': {
        'physics': '⚡ Physics',
        'chemistry': '🧪 Chemistry',
        'biology': '🧬 Biology'
    },
    'math': {
        'objective': 'x² Objective',
        'subjective': '📐 Subjective',
        'notes': '📓 Notes'
    },
    'hindi': {
        'grammar': '📝 Grammar',
        'book_notes': '📖 Book Notes',
        'imp_question': '✍️ Important Questions'
    },
    'sanskrit': {
        'grammar': '📝 Grammar',
        'book_notes': '📖 Book Notes',
        'imp_question': '✍️ Important Questions'
    },
    'sst': {
        'history': '🏛️ History',
        'geography': '🗺️ Geography',
        'civics': '⚖️ Civics',
        'economics': '📊 Economics'
    }
};

// Category Icons
const CATEGORY_ICONS = {
    'science': '🔬',
    'math': '📐',
    'hindi': '📝',
    'sanskrit': '🕉️',
    'sst': '🌍'
};

// Subcategory Icons
const SUBCATEGORY_ICONS = {
    'physics': '⚡',
    'chemistry': '🧪',
    'biology': '🧬',
    'objective': 'x²',
    'subjective': '📐',
    'notes': '📓',
    'grammar': '📝',
    'book_notes': '📖',
    'imp_question': '✍️',
    'history': '🏛️',
    'geography': '🗺️',
    'civics': '⚖️',
    'economics': '📊'
};

// Send Telegram Notification
function sendTelegramNotification(userName) {
    const message = `🆕 New User Joined!\n\n👤 Name: ${userName}\n📅 Time: ${new Date().toLocaleString()}\n🌐 IP: ${window.location.hostname}`;
    
    fetch(`https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            chat_id: TELEGRAM_CHAT_ID,
            text: message,
            parse_mode: 'HTML'
        })
    }).catch(err => console.error('Telegram error:', err));
}

// Send Announcement to Telegram
function sendTelegramAnnouncement(message, userName) {
    const text = `📢 New Announcement\n\n📝 ${message}\n\n👤 Sent by: ${userName || 'Admin'}\n📅 ${new Date().toLocaleString()}`;
    
    fetch(`https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            chat_id: TELEGRAM_CHAT_ID,
            text: text,
            parse_mode: 'HTML'
        })
    }).catch(err => console.error('Telegram error:', err));
}

// Send Seen Notification
function sendSeenNotification(announcementId, userName) {
    const text = `👀 Announcement Seen\n\n📢 ID: #${announcementId}\n👤 Seen by: ${userName}\n⏰ ${new Date().toLocaleString()}`;
    
    fetch(`https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            chat_id: TELEGRAM_CHAT_ID,
            text: text,
            parse_mode: 'HTML'
        })
    }).catch(err => console.error('Telegram error:', err));
}

// Get URL parameters
function getUrlParam(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}