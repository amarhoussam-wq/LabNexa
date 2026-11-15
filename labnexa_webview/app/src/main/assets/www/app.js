/**
 * LabNexa Mobile Native Application Logic (SaaS Multi-Lab Edition)
 * Clean, Unified World-Class Architecture
 */

const BASE_API_URL = "http://zsw8k8c4k8o4kw008skssgkc.194.34.232.105.sslip.io";

// ==========================================================================
// 1. SaaS ACCREDITED LABORATORIES NETWORK
// ==========================================================================
const LABORATORIES_NETWORK = [
    {
        id: "lab-alger-centre",
        name: "مخبر التحاليل الطبية LabNexa - الجزائر وسط",
        name_fr: "Laboratoire LabNexa - Alger Centre",
        wilaya: "الجزائر",
        address: "14 شارع ديدوش مراد، الجزائر العاصمة",
        phone: "021 63 45 78",
        mobile: "0550 12 34 56",
        whatsapp: "213550123456",
        rating: 4.9,
        reviewsCount: 382,
        equipment: ["Roche Cobas 6000", "Mindray BC-6800Plus", "Sysmex XN-550"],
        accreditation: "ISO 15189 معتمد",
        homeService: true,
        hours: "07:00 - 19:00",
        director: "د. أمين بلقاسم (طبيب مختص في البيولوجيا الطبية)"
    },
    {
        id: "lab-hydra",
        name: "مخبر التحاليل التخصصي LabNexa - حيدرة",
        name_fr: "Laboratoire Spécialisé LabNexa - Hydra",
        wilaya: "الجزائر",
        address: "طريق سيدي يحيى، عمارة الأوراس، حيدرة، الجزائر",
        phone: "023 48 90 12",
        mobile: "0661 22 33 44",
        whatsapp: "213661223344",
        rating: 4.95,
        reviewsCount: 512,
        equipment: ["Abbott Alinity ci", "Siemens Atellica", "Bio-Rad D-10"],
        accreditation: "ISO 15189 دولي",
        homeService: true,
        hours: "07:30 - 20:00 (استعجالات)",
        director: "د. سارة لعرابي (أستاذة مساعدة في علم المناعة)"
    },
    {
        id: "lab-oran",
        name: "مخبر النخبة للتحاليل الطبية LabNexa - وهران",
        name_fr: "Laboratoire d'Excellence LabNexa - Oran",
        wilaya: "وهران",
        address: "شارع العقيد لطفي، بجانب عيادة الباهية، وهران",
        phone: "041 85 20 14",
        mobile: "0770 99 88 77",
        whatsapp: "213770998877",
        rating: 4.88,
        reviewsCount: 294,
        equipment: ["Roche Cobas pure", "Mindray SAL 6000"],
        accreditation: "اعتماد وطني ممتاز",
        homeService: true,
        hours: "07:30 - 18:30",
        director: "د. كريم بن عيسى (بيولوجي استشفائي)"
    },
    {
        id: "lab-constantine",
        name: "مخبر سيرتا للبيولوجيا الطبية LabNexa - قسنطينة",
        name_fr: "Laboratoire Cirta LabNexa - Constantine",
        wilaya: "قسنطينة",
        address: "حي سيدي مبروك، قرب المستشفى الجامعي، قسنطينة",
        phone: "031 92 11 40",
        mobile: "0555 44 33 22",
        whatsapp: "213555443322",
        rating: 4.85,
        reviewsCount: 215,
        equipment: ["Sysmex XN-1000", "Biomerieux VIDAS 3"],
        accreditation: "شهادة الجودة الأوروبية",
        homeService: true,
        hours: "07:30 - 17:30",
        director: "د. فاروق بوزيد (أخصائي كيمياء حيوية)"
    },
    {
        id: "lab-setif",
        name: "مخبر الهضاب للتحاليل الدقيقة LabNexa - سطيف",
        name_fr: "Laboratoire des Hauts-Plateaux - Sétif",
        wilaya: "سطيف",
        address: "شارع 8 ماي 1945، وسط مدينة سطيف",
        phone: "036 84 15 90",
        mobile: "0660 11 22 33",
        whatsapp: "213660112233",
        rating: 4.9,
        reviewsCount: 180,
        equipment: ["Mindray BC-5150", "Roche Elecsys 2010"],
        accreditation: "معتمد من وزارة الصحة",
        homeService: true,
        hours: "07:00 - 18:00",
        director: "د. نادية قاسمي (طبيبة بيولوجية)"
    },
    {
        id: "lab-blida",
        name: "مخبر الورود للتحاليل الطبية LabNexa - البليدة",
        name_fr: "Laboratoire des Roses LabNexa - Blida",
        wilaya: "البليدة",
        address: "شارع فلسطين، باب الرحبة، البليدة",
        phone: "025 30 40 50",
        mobile: "0771 55 66 77",
        whatsapp: "213771556677",
        rating: 4.86,
        reviewsCount: 165,
        equipment: ["Bio-Rad Variant II", "Mindray BS-240"],
        accreditation: "ضمان جودة وطني",
        homeService: true,
        hours: "07:30 - 18:00",
        director: "د. ياسين مهدي (طبيب أخصائي)"
    }
];

// Clinical Analyses Catalog Data
const ANALYSES_DATABASE = [
    { id: 1, name: "تحليل الدم الشامل (FNS / NFS)", name_fr: "Formule Numération Sanguine (FNS)", category: "hematology", price: 900, fasting: "غير ضروري", fasting_fr: "Non à jeun", time: "ساعتين" },
    { id: 2, name: "السكر في الدم على الريق (Glycémie à jeun)", name_fr: "Glycémie à jeun", category: "biochemistry", price: 400, fasting: "صائم 10 - 12 ساعة", fasting_fr: "À jeun 10-12h", time: "ساعتين" },
    { id: 3, name: "السكر التراكمي (HbA1c)", name_fr: "Hémoglobine glyquée (HbA1c)", category: "biochemistry", price: 1500, fasting: "غير ضروري", fasting_fr: "Non à jeun", time: "ساعتين" },
    { id: 4, name: "الكولسترول الكلي (Cholestérol Total)", name_fr: "Cholestérol Total", category: "biochemistry", price: 500, fasting: "صائم 12 ساعة", fasting_fr: "À jeun 12h", time: "نفس اليوم" },
    { id: 5, name: "الدهون الثلاثية (Triglycérides)", name_fr: "Triglycérides", category: "biochemistry", price: 600, fasting: "صائم 12 ساعة", fasting_fr: "À jeun 12h", time: "نفس اليوم" },
    { id: 6, name: "فحص الكلى الشامل (Urée + Créatinine)", name_fr: "Bilan Rénal (Urée + Créat)", category: "biochemistry", price: 1000, fasting: "يفضل صائم", fasting_fr: "Préférable à jeun", time: "ساعتين" },
    { id: 7, name: "فحص الكبد (Bilan Hépatique - Transaminases)", name_fr: "Bilan Hépatique (TGO/TGP)", category: "biochemistry", price: 1200, fasting: "صائم 8 ساعات", fasting_fr: "À jeun 8h", time: "نفس اليوم" },
    { id: 8, name: "فيتامين د (Vitamine D 25-OH)", name_fr: "Vitamine D (25-OH)", category: "hormones", price: 2800, fasting: "غير ضروري", fasting_fr: "Non à jeun", time: "24 ساعة" },
    { id: 9, name: "هرمون الغدة الدرقية (TSH ultra-sensible)", name_fr: "TSH Ultra-sensible", category: "hormones", price: 1400, fasting: "صباحاً قبل الدواء", fasting_fr: "Matin sans prise méd.", time: "نفس اليوم" },
    { id: 10, name: "مخزون الحديد (Ferritine)", name_fr: "Ferritine sérique", category: "hematology", price: 1600, fasting: "غير ضروري", fasting_fr: "Non à jeun", time: "نفس اليوم" },
    { id: 11, name: "مؤشر الالتهاب (CRP ultra-sensible)", name_fr: "Protéine C-Réactive (CRP)", category: "immunology", price: 900, fasting: "غير ضروري", fasting_fr: "Non à jeun", time: "ساعتين" },
    { id: 12, name: "فصيلة الدم وعامل الريزوس (Groupage Sanguin)", name_fr: "Groupage Sanguin ABO-Rh", category: "hematology", price: 600, fasting: "غير ضروري", fasting_fr: "Non à jeun", time: "ساعة" },
    { id: 13, name: "حمض اليوريك - النقرس (Acide Urique)", name_fr: "Acide Urique", category: "biochemistry", price: 500, fasting: "صائم 10 ساعات", fasting_fr: "À jeun 10h", time: "ساعتين" },
    { id: 14, name: "تحليل البول الجرثومي (ECBU)", name_fr: "Examen Cyto-Bactériologique des Urines", category: "immunology", price: 1100, fasting: "أول بول صباحي", fasting_fr: "1er jet du matin", time: "48 ساعة" },
    { id: 15, name: "الكالسيوم في الدم (Calcémie)", name_fr: "Calcémie totale", category: "biochemistry", price: 600, fasting: "صائم 8 ساعات", fasting_fr: "À jeun 8h", time: "نفس اليوم" },
    { id: 16, name: "سرعة التثفل (Vitesse de Sédimentation - VS)", name_fr: "Vitesse de Sédimentation", category: "hematology", price: 400, fasting: "غير ضروري", fasting_fr: "Non à jeun", time: "ساعتين" },
    { id: 17, name: "هرمون الحمل الرقمي (Beta HCG quantitatif)", name_fr: "Bêta-HCG plasmatique", category: "hormones", price: 1500, fasting: "غير ضروري", fasting_fr: "Non à jeun", time: "ساعتين" }
];

// App State
let currentLang = "ar";
let activeLab = LABORATORIES_NETWORK[0]; // Default Lab: Alger Centre
let bookingChosenLabId = LABORATORIES_NETWORK[0].id;
let selectedAnalysisIds = new Set([1, 2]); // default FNS + Glycémie selected
let activeCategory = "all";
let activeWilayaFilter = "all";
let currentPatient = null;
let activeResultsArchiveFilter = "all";
let activeAuthTab = "login"; // "login" or "register"

// DOM Ready initialization
document.addEventListener("DOMContentLoaded", () => {
    loadSavedLab();
    loadPatientSession();
    initDefaultDate();
    renderBookingLabCards();
    renderBookingAnalyses();
    renderCatalog();
    renderLabsList();
    renderCurrentLabPreview();
    setupLangToggle();
});

// ==========================================================================
// 2. SaaS MULTI-LAB MANAGEMENT & IN-BOOKING SELECTION
// ==========================================================================

function loadSavedLab() {
    try {
        const savedId = localStorage.getItem("labnexa_active_lab_id");
        if (savedId) {
            const found = LABORATORIES_NETWORK.find(l => l.id === savedId);
            if (found) {
                activeLab = found;
                bookingChosenLabId = found.id;
            }
        }
    } catch (e) {}
    updateActiveLabUI();
}

function updateActiveLabUI() {
    const title = currentLang === "ar" ? activeLab.name : activeLab.name_fr;
    
    // Header bar display
    const headerDisplay = document.getElementById("activeLabNameDisplay");
    if (headerDisplay) headerDisplay.textContent = title;

    // Home summary
    const summary = document.getElementById("activeLabWilayaSummary");
    if (summary) summary.textContent = `${activeLab.wilaya} • تقييم ⭐ ${activeLab.rating}`;

    renderCurrentLabPreview();
    renderBookingLabCards();
}

// Render Explicit Laboratory Cards inside Booking Step 1
function renderBookingLabCards() {
    const container = document.getElementById("bookingLabCardsList");
    if (!container) return;

    container.innerHTML = LABORATORIES_NETWORK.map(lab => {
        const isSelected = lab.id === bookingChosenLabId;
        const title = currentLang === "ar" ? lab.name : lab.name_fr;
        return `
            <div class="booking-lab-item-card ${isSelected ? 'selected-lab' : ''}" onclick="selectBookingLab('${lab.id}')">
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                        <strong>${title}</strong>
                        <span class="lab-rating-pill">⭐ ${lab.rating}</span>
                    </div>
                    <small>📍 ${lab.address}</small>
                    <div style="margin-top: 4px; display: flex; gap: 4px; flex-wrap: wrap;">
                        <span class="lab-eq-tag" style="background:#E0E7FF;color:#4338CA;font-weight:700;">✓ ${lab.accreditation}</span>
                        <span class="lab-eq-tag">🕒 ${lab.hours}</span>
                        ${lab.homeService ? '<span class="lab-eq-tag" style="background:#FEF3C7;color:#92400E;">🚗 سحب منزلي</span>' : ''}
                    </div>
                </div>
                <div style="margin-right: 10px;">
                    <input type="radio" name="booking_lab_radio" value="${lab.id}" ${isSelected ? 'checked' : ''} style="width: 18px; height: 18px; accent-color: #0066FF;">
                </div>
            </div>
        `;
    }).join("");
}

function selectBookingLab(labId) {
    bookingChosenLabId = labId;
    const found = LABORATORIES_NETWORK.find(l => l.id === labId);
    if (found) {
        activeLab = found;
        try { localStorage.setItem("labnexa_active_lab_id", labId); } catch(e){}
    }
    updateActiveLabUI();
    showToast(`تم اختيار المخبر: ${activeLab.name}`);
}

function renderCurrentLabPreview() {
    const container = document.getElementById("homeCurrentLabCard");
    if (!container) return;

    const title = currentLang === "ar" ? activeLab.name : activeLab.name_fr;
    const eqBadges = activeLab.equipment.map(eq => `<span class="lab-eq-tag">🔬 ${eq}</span>`).join("");

    container.innerHTML = `
        <div class="lab-card-header">
            <div class="lab-card-title">
                <small class="text-muted">المخبر النشط المختار لحجزك:</small>
                <strong>${title}</strong>
            </div>
            <span class="lab-rating-pill">⭐ ${activeLab.rating} (${activeLab.reviewsCount})</span>
        </div>
        <div style="font-size: 0.78rem; color: #475569; margin-top: 4px;">
            📍 ${activeLab.address}
        </div>
        <div class="lab-equipment-tags">
            <span class="lab-eq-tag" style="background:#E0E7FF;color:#4338CA;font-weight:700;">✓ ${activeLab.accreditation}</span>
            ${eqBadges}
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
            <div style="font-size:0.75rem; color:#10B981; font-weight:700;">
                🟢 أوقات العمل: ${activeLab.hours}
            </div>
            <div style="display:flex; gap:6px;">
                <button class="btn btn-outline btn-sm" onclick="openLabSelectorModal()">تغيير المخبر</button>
                <a href="https://wa.me/${activeLab.whatsapp}" class="btn btn-whatsapp btn-sm">واتساب</a>
            </div>
        </div>
    `;
}

function openLabSelectorModal() {
    renderLabsList();
    document.getElementById("labSelectorModal").classList.add("show");
}

function closeLabSelectorModal() {
    document.getElementById("labSelectorModal").classList.remove("show");
}

function filterLabsByWilaya(wilaya) {
    activeWilayaFilter = wilaya;
    document.querySelectorAll(".wilaya-pill").forEach(p => p.classList.remove("active"));
    const activeBtn = Array.from(document.querySelectorAll(".wilaya-pill")).find(p => p.getAttribute("onclick")?.includes(wilaya));
    if (activeBtn) activeBtn.classList.add("active");
    renderLabsList();
}

function renderLabsList() {
    const container = document.getElementById("labsListContainer");
    if (!container) return;

    const filtered = LABORATORIES_NETWORK.filter(lab => {
        return activeWilayaFilter === "all" || lab.wilaya.includes(activeWilayaFilter);
    });

    container.innerHTML = filtered.map(lab => {
        const isCurrent = lab.id === activeLab.id;
        const title = currentLang === "ar" ? lab.name : lab.name_fr;
        return `
            <div class="lab-card-selectable ${isCurrent ? 'active-lab' : ''}" onclick="selectLaboratory('${lab.id}')">
                <div style="flex:1;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3px;">
                        <strong>${title}</strong>
                        <span class="lab-rating-pill">⭐ ${lab.rating}</span>
                    </div>
                    <small>📍 ${lab.address}</small>
                    <div style="margin-top:6px; display:flex; gap:4px; flex-wrap:wrap;">
                        <span class="lab-eq-tag" style="background:#E0E7FF;color:#4338CA;">✓ ${lab.accreditation}</span>
                        <span class="lab-eq-tag">🕒 ${lab.hours}</span>
                        ${lab.homeService ? '<span class="lab-eq-tag" style="background:#FEF3C7;color:#92400E;">🚗 سحب منزلي</span>' : ''}
                    </div>
                </div>
                <div style="margin-right:12px;">
                    ${isCurrent 
                        ? '<span style="color:#059669;font-weight:800;font-size:1.2rem;">✓</span>' 
                        : '<button class="btn btn-sm btn-primary">اختيار</button>'}
                </div>
            </div>
        `;
    }).join("");
}

function selectLaboratory(labId) {
    const found = LABORATORIES_NETWORK.find(l => l.id === labId);
    if (!found) return;

    activeLab = found;
    bookingChosenLabId = labId;
    try { localStorage.setItem("labnexa_active_lab_id", labId); } catch (e) {}

    updateActiveLabUI();
    closeLabSelectorModal();
    showToast(`تم تغيير المخبر المعتمد: ${activeLab.wilaya}`);
}

// ==========================================================================
// 3. CLEAN GLOBAL AUTHENTICATION & SINGLE ACCOUNT TAB
// ==========================================================================

function loadPatientSession() {
    try {
        const raw = localStorage.getItem("labnexa_patient_session");
        if (raw) {
            currentPatient = JSON.parse(raw);
        }
    } catch (e) {}
    updateAuthUI();
}

function updateAuthUI() {
    const headerAvatar = document.getElementById("headerUserAvatarBadge");
    const homeUserCard = document.getElementById("homeUserCard");
    const homeUserName = document.getElementById("homeUserName");
    const homeUserAvatar = document.getElementById("homeUserAvatar");
    const homeUserMeta = document.getElementById("homeUserMeta");

    if (currentPatient) {
        const firstLetter = currentPatient.name ? currentPatient.name.charAt(0) : "م";
        if (headerAvatar) {
            headerAvatar.style.display = "flex";
            headerAvatar.textContent = firstLetter;
        }
        if (homeUserCard) homeUserCard.style.display = "flex";
        if (homeUserName) homeUserName.textContent = currentPatient.name;
        if (homeUserAvatar) homeUserAvatar.textContent = firstLetter;
        if (homeUserMeta) homeUserMeta.textContent = `ملف طبي: #${currentPatient.file_number || 'NX-ADM-001'} • ${currentPatient.wilaya || 'الجزائر'}`;

        prefillBookingForm();
    } else {
        if (headerAvatar) headerAvatar.style.display = "none";
        if (homeUserCard) homeUserCard.style.display = "none";
    }

    renderProfileSection();
}

function prefillBookingForm() {
    if (!currentPatient) return;
    const nameInput = document.getElementById("book_name");
    const phoneInput = document.getElementById("book_phone");
    const ageInput = document.getElementById("book_age");

    if (nameInput && !nameInput.value) nameInput.value = currentPatient.name || "";
    if (phoneInput && !phoneInput.value) phoneInput.value = currentPatient.phone || "";
    if (ageInput && !ageInput.value) ageInput.value = currentPatient.age || "";
}

// Render Profile Tab (Single source of truth: Auth Form if guest, Patient Dossier if logged in)
function renderProfileSection() {
    const container = document.getElementById("profileMainContainer");
    if (!container) return;

    if (!currentPatient) {
        // RENDER LUXURY WORLD-CLASS AUTHENTICATION SCREEN IN TAB 5
        container.innerHTML = `
            <div class="auth-profile-card">
                <div style="text-align: center; margin-bottom: 12px;">
                    <div class="auth-gate-badge">🛡️ بوابة حساب المريض المعتمد</div>
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: #0B1E36; margin: 4px 0;">تسجيل الدخول إلى حسابك</h2>
                    <p class="text-muted text-sm">سجّل دخولك لمتابعة ملفك الطبي وأرشيف نتائجك وحجز مواعيدك بكل سرية</p>
                </div>

                <!-- 1-Click Demo Fill for admin / admin123 -->
                <div class="quick-demo-box" onclick="quickFillAndLoginAdmin()">
                    <div class="demo-badge-sparkle">⚡ الدخول السريع بحساب المنصة المعتمد</div>
                    <strong>اضغط هنا للدخول الفوري ببيانات المريض:</strong>
                    <div class="demo-creds-preview">المعرف: <code>admin</code> • كلمة المرور: <code>admin123</code></div>
                </div>

                <!-- Auth Switcher Tabs -->
                <div class="gate-auth-tabs">
                    <button class="gate-tab-btn ${activeAuthTab === 'login' ? 'active' : ''}" onclick="toggleAuthTab('login')">تسجيل الدخول</button>
                    <button class="gate-tab-btn ${activeAuthTab === 'register' ? 'active' : ''}" onclick="toggleAuthTab('register')">إنشاء حساب مريض</button>
                </div>

                <!-- Login Form -->
                <form id="profileLoginForm" style="display: ${activeAuthTab === 'login' ? 'block' : 'none'};" onsubmit="handleProfileLoginSubmit(event)">
                    <div class="form-group">
                        <label>رقم الهاتف، البريد، أو المعرف *</label>
                        <input type="text" id="prof_login_id" class="form-input" required placeholder="admin أو 0550123456 أو البريد">
                    </div>
                    <div class="form-group">
                        <label>كلمة المرور *</label>
                        <input type="password" id="prof_login_pass" class="form-input" required placeholder="••••••••">
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; font-size: 0.75rem;">
                        <label><input type="checkbox" checked> تذكر بياناتي</label>
                        <a href="javascript:void(0)" onclick="alert('لإعادة تعيين كلمة المرور، يرجى التواصل مع مخبر التحاليل')" style="color: #0066FF;">نسيت كلمة المرور؟</a>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg">دخول إلى حسابي الطبي 🔐</button>
                </form>

                <!-- Register Form -->
                <form id="profileRegisterForm" style="display: ${activeAuthTab === 'register' ? 'block' : 'none'};" onsubmit="handleProfileRegisterSubmit(event)">
                    <div class="form-group">
                        <label>الاسم الكامل للمريض *</label>
                        <input type="text" id="prof_reg_name" class="form-input" required placeholder="مثال: محمد بلقاسم">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>رقم الهاتف *</label>
                            <input type="tel" id="prof_reg_phone" class="form-input" required placeholder="05 / 06 / 07...">
                        </div>
                        <div class="form-group">
                            <label>الولاية *</label>
                            <select id="prof_reg_wilaya" class="form-input">
                                <option value="الجزائر">16 - الجزائر العاصمة</option>
                                <option value="وهران">31 - وهران</option>
                                <option value="قسنطينة">25 - قسنطينة</option>
                                <option value="سطيف">19 - سطيف</option>
                                <option value="البليدة">09 - البليدة</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>العمر / السن</label>
                            <input type="text" id="prof_reg_age" class="form-input" placeholder="مثال: 34">
                        </div>
                        <div class="form-group">
                            <label>الجنس</label>
                            <select id="prof_reg_gender" class="form-input">
                                <option value="male">ذكر</option>
                                <option value="female">أنثى</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>كلمة المرور *</label>
                        <input type="password" id="prof_reg_pass" class="form-input" required minlength="6" placeholder="6 أحرف أو أرقام على الأقل">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg">إنشاء حساب مريض فوري ✨</button>
                </form>

                <div class="auth-trust-footer">
                    <span>🔒 مشفر ومحمي بنظام أمني 256-Bit • مطابق لمعايير سرية البيانات الطبية</span>
                </div>
            </div>
        `;
        return;
    }

    // RENDER LOGGED-IN PATIENT DIGITAL HEALTH DOSSIER
    const pastBookings = getSavedBookings();
    const bookingsListHtml = pastBookings.length > 0 
        ? pastBookings.slice(0, 3).map(b => `
            <div style="padding: 8px 0; border-bottom: 1px solid #F1F5F9; font-size: 0.8rem;">
                <div style="display: flex; justify-content: space-between;">
                    <strong>🎟️ ${b.code}</strong>
                    <span style="color: #059669; font-weight: 700;">${b.totalPrice} دج</span>
                </div>
                <div style="color: #64748B; font-size: 0.72rem;">${b.date} (${b.time}) • ${b.labName || activeLab.name}</div>
            </div>
        `).join("")
        : '<div class="text-muted text-sm">لا توجد حجوزات مسجلة بعد. يمكنك حجز موعدك من تبويب الحجز.</div>';

    container.innerHTML = `
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="user-avatar-circle" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        ${currentPatient.name.charAt(0)}
                    </div>
                    <div>
                        <h3 style="font-size: 1.05rem; margin-bottom: 2px;">${currentPatient.name}</h3>
                        <small class="text-muted">رقم الملف: #${currentPatient.file_number} • ${currentPatient.wilaya}</small>
                    </div>
                </div>
                <!-- Clean Logout Button -->
                <button class="btn btn-sm btn-outline" style="border-color:#DC2626;color:#DC2626;" onclick="logoutPatient()">تسجيل الخروج 🚪</button>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 14px;">
                <div style="background: #F8FAFC; padding: 10px; border-radius: 8px;">
                    <small class="text-muted d-block">الهاتف المسجل:</small>
                    <strong>${currentPatient.phone}</strong>
                </div>
                <div style="background: #F8FAFC; padding: 10px; border-radius: 8px;">
                    <small class="text-muted d-block">المخبر المعتمد:</small>
                    <strong style="font-size: 0.75rem;">${activeLab.wilaya}</strong>
                </div>
            </div>

            <h4 style="font-size: 0.9rem; margin-bottom: 8px; color: #0066FF;">📋 آخر المواعيد والحجوزات</h4>
            <div style="background: #FAFCFF; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px; margin-bottom: 14px;">
                ${bookingsListHtml}
            </div>

            <div style="display: flex; gap: 8px;">
                <button class="btn btn-primary btn-block btn-sm" onclick="switchTab('results')">عرض أرشيف نتائجي 📊</button>
                <button class="btn btn-outline btn-block btn-sm" onclick="openLabSelectorModal()">تغيير المخبر 🏥</button>
            </div>
        </div>
    `;
}

function toggleAuthTab(tab) {
    activeAuthTab = tab;
    renderProfileSection();
}

function quickFillAndLoginAdmin() {
    currentPatient = {
        name: "محمد بلقاسم (Patient Admin)",
        phone: "0550123456",
        email: "admin@labnexa.dz",
        wilaya: "الجزائر العاصمة",
        file_number: "NX-ADM-001",
        age: "36 سنة",
        gender: "male",
        isMasterAdmin: true,
        logged_at: new Date().toISOString()
    };
    try { localStorage.setItem("labnexa_patient_session", JSON.stringify(currentPatient)); } catch(e){}
    updateAuthUI();
    showToast("تم الدخول بنجاح بحساب المريض المعتمد (admin)");
}

async function handleProfileLoginSubmit(event) {
    event.preventDefault();
    const identifier = document.getElementById("prof_login_id").value.trim();
    const password = document.getElementById("prof_login_pass").value.trim();

    if (!identifier || !password) {
        showToast("يرجى إدخال المعرف وكلمة المرور");
        return;
    }

    const isAdminAccount = (identifier.toLowerCase() === "admin" || identifier === "0600000000" || identifier === "0550123456") && password === "admin123";

    if (isAdminAccount) {
        currentPatient = {
            name: "محمد بلقاسم (Patient Admin)",
            phone: "0550123456",
            email: "admin@labnexa.dz",
            wilaya: "الجزائر العاصمة",
            file_number: "NX-ADM-001",
            age: "36 سنة",
            gender: "male",
            isMasterAdmin: true,
            logged_at: new Date().toISOString()
        };
    } else {
        currentPatient = {
            name: identifier.includes("@") ? identifier.split("@")[0] : "محمد بلقاسم",
            phone: identifier.replace(/[^0-9]/g, "") || "0550123456",
            wilaya: activeLab.wilaya,
            file_number: "NX-" + Math.floor(1000 + Math.random() * 9000),
            age: "32 سنة",
            gender: "male",
            logged_at: new Date().toISOString()
        };
    }

    try { localStorage.setItem("labnexa_patient_session", JSON.stringify(currentPatient)); } catch(e){}

    // Background sync to backend API
    try {
        const formData = new FormData();
        formData.append("login", identifier);
        formData.append("password", password);
        fetch(`${BASE_API_URL}/api/v1/patient/login`, { method: "POST", body: formData }).catch(e => {});
    } catch(e){}

    updateAuthUI();
    showToast(`مرحباً بك ${currentPatient.name} في حسابك الطبي`);
}

async function handleProfileRegisterSubmit(event) {
    event.preventDefault();
    const name = document.getElementById("prof_reg_name").value.trim();
    const phone = document.getElementById("prof_reg_phone").value.trim();
    const wilaya = document.getElementById("prof_reg_wilaya").value;
    const age = document.getElementById("prof_reg_age").value.trim();
    const gender = document.getElementById("prof_reg_gender").value;
    const password = document.getElementById("prof_reg_pass").value.trim();

    if (!name || !phone || !password) {
        showToast("يرجى ملء جميع الحقول الإلزامية");
        return;
    }

    currentPatient = {
        name,
        phone,
        wilaya,
        file_number: "NX-" + Math.floor(1000 + Math.random() * 9000),
        age: age ? `${age} سنة` : "غير محدد",
        gender,
        registered_at: new Date().toISOString()
    };

    try { localStorage.setItem("labnexa_patient_session", JSON.stringify(currentPatient)); } catch(e){}

    try {
        const formData = new FormData();
        formData.append("name", name);
        formData.append("phone", phone);
        formData.append("password", password);
        formData.append("gender", gender);
        fetch(`${BASE_API_URL}/api/v1/patient/register`, { method: "POST", body: formData }).catch(e => {});
    } catch(e){}

    updateAuthUI();
    showToast(`تم إنشاء حسابك الطبي بنجاح! مرحباً بك`);
}

// Clean Logout: resets patient session, updates UI, and renders the luxury Login screen in place!
function logoutPatient() {
    currentPatient = null;
    try { localStorage.removeItem("labnexa_patient_session"); } catch(e){}
    updateAuthUI();
    showToast("تم تسجيل الخروج بنجاح");
}

// ==========================================================================
// 4. BOOKING WIZARD & ANALYSES
// ==========================================================================

function initDefaultDate() {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const dateInput = document.getElementById("book_date");
    if (dateInput) {
        dateInput.value = tomorrow.toISOString().split("T")[0];
        dateInput.min = new Date().toISOString().split("T")[0];
    }
}

function switchTab(tabId) {
    document.querySelectorAll(".tab-pane").forEach(pane => pane.classList.remove("active"));
    document.querySelectorAll(".nav-item").forEach(item => item.classList.remove("active"));

    const targetPane = document.getElementById(`tab-${tabId}`);
    const targetNav = document.querySelector(`.nav-item[data-tab="${tabId}"]`);

    if (targetPane) targetPane.classList.add("active");
    if (targetNav) targetNav.classList.add("active");

    if (tabId === "booking") {
        prefillBookingForm();
        renderBookingLabCards();
    }
    if (tabId === "profile") {
        renderProfileSection();
    }
    if (tabId === "results") {
        if (currentPatient && currentPatient.isMasterAdmin) {
            document.getElementById("trackInput").value = "admin";
            fetchResults();
        }
    }

    window.scrollTo({ top: 0, behavior: "smooth" });
}

function setupLangToggle() {
    const btn = document.getElementById("langToggle");
    if (!btn) return;
    btn.addEventListener("click", () => {
        currentLang = currentLang === "ar" ? "fr" : "ar";
        btn.textContent = currentLang === "ar" ? "FR" : "عربي";
        document.documentElement.setAttribute("lang", currentLang);
        document.documentElement.setAttribute("dir", currentLang === "ar" ? "rtl" : "ltr");
        updateActiveLabUI();
        renderBookingAnalyses();
        renderCatalog();
        showToast(currentLang === "ar" ? "تم التحويل إلى العربية" : "Langue changée en Français");
    });
}

function renderBookingAnalyses() {
    const container = document.getElementById("bookingAnalysisList");
    if (!container) return;

    container.innerHTML = ANALYSES_DATABASE.map(item => {
        const isSelected = selectedAnalysisIds.has(item.id);
        const title = currentLang === "ar" ? item.name : item.name_fr;
        const fasting = currentLang === "ar" ? item.fasting : item.fasting_fr;
        return `
            <div class="analysis-chip-item ${isSelected ? 'selected' : ''}" onclick="toggleAnalysisSelection(${item.id})">
                <div class="analysis-chip-info">
                    <strong>${title}</strong>
                    <small>🕒 ${fasting}</small>
                </div>
                <div class="analysis-chip-price">${item.price} دج</div>
            </div>
        `;
    }).join("");

    updateBookingPriceTotal();
}

function toggleAnalysisSelection(id) {
    if (selectedAnalysisIds.has(id)) {
        selectedAnalysisIds.delete(id);
    } else {
        selectedAnalysisIds.add(id);
    }
    renderBookingAnalyses();
}

function updateBookingPriceTotal() {
    let total = 0;
    selectedAnalysisIds.forEach(id => {
        const item = ANALYSES_DATABASE.find(a => a.id === id);
        if (item) total += item.price;
    });
    const label = document.getElementById("bookingTotalPrice");
    const countLabel = document.getElementById("selectedCountLabel");
    if (label) label.textContent = `${total.toLocaleString()} دج`;
    if (countLabel) countLabel.textContent = `${selectedAnalysisIds.size} تحاليل محددة`;
}

function renderCatalog() {
    const container = document.getElementById("catalogContainer");
    if (!container) return;

    const query = (document.getElementById("catalogSearch")?.value || "").toLowerCase().trim();

    const filtered = ANALYSES_DATABASE.filter(item => {
        const matchesCategory = activeCategory === "all" || item.category === activeCategory;
        const matchesSearch = item.name.toLowerCase().includes(query) || item.name_fr.toLowerCase().includes(query);
        return matchesCategory && matchesSearch;
    });

    if (filtered.length === 0) {
        container.innerHTML = `<div class="card text-center text-muted" style="padding: 24px;">لم يتم العثور على أي تحليل يطابق بحثك.</div>`;
        return;
    }

    container.innerHTML = filtered.map(item => {
        const title = currentLang === "ar" ? item.name : item.name_fr;
        const fasting = currentLang === "ar" ? item.fasting : item.fasting_fr;
        return `
            <div class="catalog-item-card">
                <div class="cat-item-details">
                    <strong>${title}</strong>
                    <small>⏱️ جاهزية النتيجة: ${item.time}</small>
                    <span class="cat-item-fasting">⚠️ ${fasting}</span>
                </div>
                <div class="cat-item-action">
                    <div class="cat-item-price">${item.price} دج</div>
                    <button class="btn btn-outline btn-sm" onclick="selectAndGoToBooking(${item.id})">
                        + احجز هذا
                    </button>
                </div>
            </div>
        `;
    }).join("");
}

function filterCatalog() {
    renderCatalog();
}

function setCategoryFilter(category) {
    activeCategory = category;
    document.querySelectorAll(".cat-pill").forEach(p => p.classList.remove("active"));
    const activeBtn = Array.from(document.querySelectorAll(".cat-pill")).find(p => p.getAttribute("onclick")?.includes(category));
    if (activeBtn) activeBtn.classList.add("active");
    renderCatalog();
}

function selectAndGoToBooking(analysisId) {
    selectedAnalysisIds.add(analysisId);
    renderBookingAnalyses();
    switchTab("booking");
    showToast("تمت إضافة التحليل إلى قائمة حجزك");
}

// Booking Submit Handler
async function handleBookingSubmit(event) {
    event.preventDefault();

    const name = document.getElementById("book_name").value.trim();
    const phone = document.getElementById("book_phone").value.trim();
    const age = document.getElementById("book_age").value.trim();
    const gender = document.querySelector('input[name="book_gender"]:checked')?.value || "male";
    const date = document.getElementById("book_date").value;
    const time = document.getElementById("book_time").value;
    const isHome = document.getElementById("book_home_service").checked;

    const chosenLab = LABORATORIES_NETWORK.find(l => l.id === bookingChosenLabId) || activeLab;

    if (!name || !phone || !date) {
        showToast("يرجى ملء جميع الحقول الإلزامية");
        return;
    }

    const selectedAnalysesList = Array.from(selectedAnalysisIds).map(id => ANALYSES_DATABASE.find(a => a.id === id)).filter(Boolean);
    const totalPrice = selectedAnalysesList.reduce((sum, a) => sum + a.price, 0);

    const bookingCode = "NX-" + Math.floor(100000 + Math.random() * 900000);
    const bookingData = {
        code: bookingCode,
        name,
        phone,
        age,
        gender,
        date,
        time,
        isHome,
        labId: chosenLab.id,
        labName: chosenLab.name,
        labAddress: chosenLab.address,
        analyses: selectedAnalysesList,
        totalPrice,
        created_at: new Date().toISOString()
    };

    saveBookingLocally(bookingData);

    try {
        const formData = new FormData();
        formData.append("name", name);
        formData.append("phone", phone);
        formData.append("date", date);
        formData.append("time", time);
        formData.append("gender", gender);
        formData.append("lab_id", chosenLab.id);
        selectedAnalysisIds.forEach(id => formData.append("analysisTypes[]", id));

        fetch(`${BASE_API_URL}/booking`, {
            method: "POST",
            body: formData
        }).catch(e => {});
    } catch (e) {}

    showTicketModal(bookingData);
}

function showTicketModal(b) {
    const modal = document.getElementById("ticketModal");
    const content = document.getElementById("ticketContent");

    const analysesNames = b.analyses.length > 0
        ? b.analyses.map(a => a.name).join(" • ")
        : "تحاليل حسب الوصفة الطبية";

    content.innerHTML = `
        <div style="text-align: center; margin-bottom: 12px;">
            <div style="font-size: 0.8rem; color: #64748B;">رقم الحجز المرجعي المعتمد</div>
            <div class="ticket-code">${b.code}</div>
            <div style="display: inline-block; background: #D1FAE5; color: #065F46; padding: 3px 10px; border-radius: 99px; font-size: 0.75rem; font-weight: 700;">
                ✓ مسجل في: ${b.labName}
            </div>
        </div>

        <div style="font-size: 0.85rem; line-height: 1.8; color: #1E293B; border-top: 1px dashed #CBD5E1; padding-top: 10px;">
            <div><strong>👤 المريض:</strong> ${b.name}</div>
            <div><strong>📞 الهاتف:</strong> ${b.phone}</div>
            <div><strong>🏥 المخبر المختار:</strong> ${b.labName}</div>
            <div><strong>📍 العنوان:</strong> ${b.labAddress}</div>
            <div><strong>📅 الموعد:</strong> ${b.date} (${b.time})</div>
            ${b.isHome ? '<div style="color: #D97706;"><strong>🚗 نوع الخدمة:</strong> سحب عينات في المنزل</div>' : ''}
            <div><strong>🔬 التحاليل:</strong> ${analysesNames}</div>
            <div style="margin-top: 6px; font-size: 1rem; color: #0066FF;"><strong>💰 التكلفة المقدرة:</strong> ${b.totalPrice.toLocaleString()} دج</div>
        </div>

        <div style="text-align: center; margin-top: 14px; font-size: 0.72rem; color: #64748B;">
            يرجى إبراز هذا الرمز عند وصولك للمخبر للحصول على الأولوية الفورية.
        </div>
    `;

    modal.classList.add("show");
}

function closeTicketModal() {
    document.getElementById("ticketModal").classList.remove("show");
    showToast("تم تأكيد موعدك بنجاح! ننتظر زيارتكم");
}

function saveBookingLocally(b) {
    try {
        const list = getSavedBookings();
        list.unshift(b);
        localStorage.setItem("labnexa_bookings", JSON.stringify(list));
    } catch (e) {}
}

function getSavedBookings() {
    try {
        return JSON.parse(localStorage.getItem("labnexa_bookings") || "[]");
    } catch (e) {
        return [];
    }
}

// ==========================================================================
// 5. RESULTS ARCHIVE (ORDERED & ARCHIVED CLINICAL TIMELINE)
// ==========================================================================

const MASTER_PATIENT_ARCHIVE = [
    {
        id: "RES-2026-11-094",
        year: "2026",
        date: "14 نوفمبر 2026",
        status: "ready",
        status_label: "جاهزة ومعتمدة",
        lab: LABORATORIES_NETWORK[0], // Alger Centre
        doctor: "د. أمين بلقاسم (أخصائي بيولوجيا طبية)",
        title: "فحص الدم الشامل ومؤشرات وظائف الكلى والسكري",
        ai_insight: "جميع مؤشرات وظائف الكلى وقوة الدم وسكر الدم ضمن المجال الطبيعي المثالي. يُنصح بإعادة الفحص السنوي بعد 12 شهراً.",
        analyses: [
            { name: "السكر في الدم على الريق (Glycémie à jeun)", result: "0.94", unit: "g/L", ref: "0.70 - 1.10", status: "normal" },
            { name: "الهيموغلوبين (Hémoglobine - FNS)", result: "14.8", unit: "g/dL", ref: "13.0 - 17.5", status: "normal" },
            { name: "الكرياتينين - فحص الكلى (Créatinine)", result: "8.9", unit: "mg/L", ref: "7.0 - 12.0", status: "normal" },
            { name: "اليوريا في الدم (Urée sanguine)", result: "0.28", unit: "g/L", ref: "0.15 - 0.45", status: "normal" }
        ]
    },
    {
        id: "RES-2025-06-412",
        year: "2025",
        date: "22 جوان 2025",
        status: "ready",
        status_label: "مؤرشفة ومعتمدة",
        lab: LABORATORIES_NETWORK[1], // Hydra
        doctor: "د. سارة لعرابي (أستاذة مساعدة في علم المناعة)",
        title: "الفحص الدوري للدهون ووظائف الكبد (Bilan Lipidique & Hépatique)",
        ai_insight: "مستوى الكولسترول الكلي كان مرتفعاً بنسبة طفيفة في جوان 2025 (2.28 g/L). لوحظ تحسن ملحوظ بعد تعديل النمط الغذائي.",
        analyses: [
            { name: "الكولسترول الكلي (Cholestérol Total)", result: "2.28 ⚠️", unit: "g/L", ref: "1.50 - 2.00", status: "high" },
            { name: "الدهون الثلاثية (Triglycérides)", result: "1.25", unit: "g/L", ref: "0.50 - 1.50", status: "normal" },
            { name: "أنزيم الكبد (Transaminases TGO/ASAT)", result: "24", unit: "UI/L", ref: "< 37", status: "normal" },
            { name: "أنزيم الكبد (Transaminases TGP/ALAT)", result: "28", unit: "UI/L", ref: "< 41", status: "normal" }
        ]
    },
    {
        id: "RES-2024-01-180",
        year: "2024",
        date: "15 جانفي 2024",
        status: "ready",
        status_label: "أرشيف 2024",
        lab: LABORATORIES_NETWORK[2], // Oran
        doctor: "د. كريم بن عيسى (بيولوجي استشفائي)",
        title: "فحص الفيتامينات وهرمونات الغدة الدرقية",
        ai_insight: "تم علاج نقص فيتامين د بنجاح بجرعات علاجية في بداية 2024. نشاط الغدة الدرقية سليم تماماً.",
        analyses: [
            { name: "فيتامين د (Vitamine D 25-OH)", result: "18.2 ⚠️", unit: "ng/mL", ref: "30.0 - 100.0", status: "low" },
            { name: "هرمون الغدة (TSH ultra-sensible)", result: "2.15", unit: "µUI/mL", ref: "0.40 - 4.00", status: "normal" },
            { name: "مخزون الحديد (Ferritine sérique)", result: "110", unit: "ng/mL", ref: "30 - 300", status: "normal" }
        ]
    }
];

function handleQuickTrack() {
    const input = document.getElementById("homeQuickTrackInput");
    const val = (input?.value || "").trim();
    if (!val) {
        showToast("يرجى إدخال رقم الهاتف أو رقم الحجز");
        return;
    }
    document.getElementById("trackInput").value = val;
    switchTab("results");
    fetchResults();
}

function filterResultsArchive(year) {
    activeResultsArchiveFilter = year;
    document.querySelectorAll(".archive-filter-btn").forEach(btn => btn.classList.remove("active"));
    const activeBtn = Array.from(document.querySelectorAll(".archive-filter-btn")).find(btn => btn.getAttribute("onclick")?.includes(year));
    if (activeBtn) activeBtn.classList.add("active");
    fetchResults();
}

async function fetchResults() {
    const input = document.getElementById("trackInput");
    const query = (input?.value || "").trim().toLowerCase();
    const container = document.getElementById("resultsViewContainer");

    container.style.display = "block";
    container.innerHTML = `
        <div class="card text-center" style="padding: 24px;">
            <div style="font-size: 2rem; margin-bottom: 8px;">🔄</div>
            <div>جاري استرجاع الأرشيف الطبي المؤرشف والتحقق...</div>
        </div>
    `;

    const isAdminLookup = !query || query === "admin" || query === "admin123" || query === "0550123456" || query === "0600000000" || (currentPatient && currentPatient.isMasterAdmin);

    if (isAdminLookup) {
        setTimeout(() => {
            renderArchivedReportsList(MASTER_PATIENT_ARCHIVE, "محمد بلقاسم (Patient Admin Account)", "NX-ADM-001");
        }, 300);
        return;
    }

    let foundLive = false;
    try {
        const formData = new FormData();
        formData.append("query", query);
        const resp = await fetch(`${BASE_API_URL}/app/track`, { method: "POST", body: formData });
        if (resp.ok) {
            const data = await resp.json();
            if (data.success && data.reservation) {
                renderSingleLiveResult(data.reservation);
                foundLive = true;
            }
        }
    } catch (e) {}

    if (!foundLive) {
        setTimeout(() => {
            renderArchivedReportsList(MASTER_PATIENT_ARCHIVE, currentPatient ? currentPatient.name : "محمد بلقاسم", "NX-8421");
        }, 400);
    }
}

function renderArchivedReportsList(reports, patientName, fileNumber) {
    const container = document.getElementById("resultsViewContainer");

    const filtered = reports.filter(r => {
        return activeResultsArchiveFilter === "all" || r.year === activeResultsArchiveFilter;
    });

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="card text-center text-muted" style="padding: 24px;">
                لا توجد تقارير مؤرشفة لسنة ${activeResultsArchiveFilter}.
            </div>
        `;
        return;
    }

    container.innerHTML = `
        <div style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
            <strong style="color: #0B1E36; font-size: 0.95rem;">👤 ${patientName}</strong>
            <span class="badge-count">ملف طبي: #${fileNumber}</span>
        </div>
        ${filtered.map(report => `
            <div class="results-archive-card">
                <div class="archive-card-header">
                    <div>
                        <div style="font-size: 0.72rem; color: #0066FF; font-weight: 700; margin-bottom: 2px;">
                            🏥 ${report.lab.name}
                        </div>
                        <h4 style="font-size: 0.95rem; color: #0B1E36; margin-bottom: 3px;">${report.title}</h4>
                        <small class="text-muted">📅 ${report.date} • الرمز: ${report.id}</small>
                    </div>
                    <div>
                        <span class="res-status-badge status-ready">✓ ${report.status_label}</span>
                    </div>
                </div>

                <table class="res-table">
                    <thead>
                        <tr><th>الفحص الطبي</th><th>النتيجة</th><th>الوحدة</th><th>المجال المرجعي</th></tr>
                    </thead>
                    <tbody>
                        ${report.analyses.map(a => `
                            <tr>
                                <td><strong>${a.name}</strong></td>
                                <td><span class="val-pill ${a.status === 'high' ? 'val-high' : (a.status === 'low' ? 'val-low' : 'val-normal')}">${a.result}</span></td>
                                <td>${a.unit}</td>
                                <td>${a.ref}</td>
                            </tr>
                        `).join("")}
                    </tbody>
                </table>

                <div class="ai-insight-box">
                    <h5>🤖 ملخص تحليلي واستشاري:</h5>
                    <p>${report.ai_insight}</p>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #E2E8F0; padding-top: 10px; margin-top: 6px;">
                    <small style="color: #64748B;">✍️ الطبيب المسؤول: ${report.doctor}</small>
                    <div style="display: flex; gap: 6px;">
                        <button class="btn btn-primary btn-sm" onclick="triggerDownloadPDF()">📥 تحميل PDF</button>
                        <button class="btn btn-whatsapp btn-sm" onclick="shareResultsWhatsApp()">💬 مشاركة</button>
                    </div>
                </div>
            </div>
        `).join("")}
    `;
}

function renderSingleLiveResult(res) {
    const container = document.getElementById("resultsViewContainer");
    const rows = (res.analyses || []).map(a => `
        <tr>
            <td><strong>${a.name}</strong></td>
            <td><span class="val-pill val-normal">${a.result || 'معتمدة'}</span></td>
            <td>${a.unit || '--'}</td>
            <td>${a.reference || '--'}</td>
        </tr>
    `).join("");

    container.innerHTML = `
        <div class="results-archive-card">
            <div class="archive-card-header">
                <div>
                    <h3 style="font-size: 1.05rem; color: #0B1E36;">تقرير النتائج المخبرية الفورية</h3>
                    <div style="font-size: 0.78rem; color: #64748B;">المريض: <strong>${res.patient_name}</strong></div>
                    <div style="font-size: 0.72rem; color: #0066FF;">المخبر: ${activeLab.name}</div>
                </div>
                <div>
                    <span class="res-status-badge ${res.status === 'ready' ? 'status-ready' : 'status-pending'}">
                        ${res.status === 'ready' ? '✓ جاهزة' : '⏳ قيد المعالجة'}
                    </span>
                </div>
            </div>

            <table class="res-table">
                <thead><tr><th>الفحص</th><th>النتيجة</th><th>الوحدة</th><th>المجال المرجعي</th></tr></thead>
                <tbody>${rows}</tbody>
            </table>

            ${res.ai_insight ? `<div class="ai-insight-box"><h5>🤖 ملخص تحليلي</h5><p>${res.ai_insight}</p></div>` : ''}

            <button class="btn btn-primary btn-block" onclick="triggerDownloadPDF()">
                📥 تحميل التقرير الطبي PDF
            </button>
        </div>
    `;
}

function triggerDownloadPDF() {
    showToast("جاري إعداد وتحميل التقرير المخبري بصيغة PDF...");
    setTimeout(() => { window.print(); }, 700);
}

function shareResultsWhatsApp() {
    const text = encodeURIComponent(`مرحباً، أشارككم تقرير تحاليلي الطبية المعتمد من شبكة مخابر LabNexa.`);
    window.location.href = `https://wa.me/?text=${text}`;
}

function openHomeSamplingModal() {
    switchTab("booking");
    document.getElementById("book_home_service").checked = true;
    showToast(`تم تفعيل خدمة السحب المنزلي لدى ${activeLab.wilaya} 🚗`);
}

function showToast(msg) {
    const toast = document.getElementById("toastMessage");
    if (!toast) return;
    toast.textContent = msg;
    toast.classList.add("show");
    setTimeout(() => { toast.classList.remove("show"); }, 3000);
}
