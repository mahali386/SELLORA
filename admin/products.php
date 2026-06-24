<?php
// Sellora - Admin Products Catalogue Engine
require_once __DIR__ . '/../common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN PRODUCT CATALOG EDITOR -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-display font-black text-slate-850 dark:text-white">Admin Catalogue</h1>
            <p class="text-[10px] text-slate-450 dark:text-slate-500 font-bold uppercase tracking-wider">File Inventories & Uploads</p>
        </div>
        
        <button onclick="triggerNewProductWizard()" class="px-3.5 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-bold shadow-md active:scale-95 transition-all outline-none">
            <i class="fas fa-plus mr-1"></i>New Product
        </button>
    </div>

    <!-- ADD/EDIT LARGE WIZARD OVERLAY -->
    <div id="product-modal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md overflow-y-auto p-4 flex items-center justify-center text-slate-800 dark:text-slate-100">
        <div class="max-w-md w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-2xl relative my-8">
            <h3 id="wizard-title" class="text-xs font-black uppercase tracking-wider text-slate-550 dark:text-slate-400 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">Assign Product File Specifications</h3>
            
            <input type="hidden" id="edit-prod-id" value="">
            <form id="wizard-form" onsubmit="saveProductForm(event)" class="space-y-4 text-xs">
                
                <!-- ADVANCED: Gemini AI Copywriter Panel -->
                <div class="p-3.5 rounded-2xl bg-gradient-to-r from-sky-50 to-sky-100/30 dark:from-sky-950/20 dark:to-slate-900 border border-slate-150 dark:border-sky-950/60 text-xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-extrabold text-sky-600 dark:text-sky-400 flex items-center gap-1.5 uppercase text-[9px] tracking-wider">
                            <i class="fas fa-magic text-[10px] animate-bounce"></i>
                            <span>Gemini AI Copywriter</span>
                        </span>
                        <button type="button" id="btn-ai-generate" onclick="generateAICatCopy()" class="text-[9px] bg-sky-500 hover:bg-sky-600 text-white font-black px-2.5 py-1 rounded-lg active:scale-95 transition-all outline-none flex items-center gap-1">
                            <span>Assemble Specs</span>
                            <i class="fas fa-angles-right text-[8px]"></i>
                        </button>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" id="ai-keywords" placeholder="e.g. Master Python Cheatsheet Guide" class="flex-grow px-3 py-1.5 rounded-xl bg-white dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none text-[11px] shadow-sm">
                    </div>
                    <p class="text-[8px] text-slate-400 mt-1.5"><i class="fas fa-info-circle"></i> Enters optimal title, description, price, and SEO file names instantly.</p>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1">Product Title</label>
                    <input type="text" id="p-title" required placeholder="e.g. Chat GPT Master Syllabus" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1">MRP Value (₹)</label>
                        <input type="number" id="p-mrp" required placeholder="999" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1">Selling Price (₹)</label>
                        <input type="number" id="p-price" required placeholder="499" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1">Category Class</label>
                        <select id="p-category" required class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                            <!-- Populated in JS -->
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1">Status Active</label>
                        <select id="p-status" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                            <option value="active">Active Available</option>
                            <option value="inactive">Draft / Hidden</option>
                        </select>
                    </div>
                </div>

                <!-- PREMIUM DIGITAL DELIVERABLE UPLOAD SYSTEM -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1">Deliverable Product File (.zip, .pdf, .docx, etc.)</label>
                    <div class="border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl p-4 text-center cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-850 transition-all mb-2 relative flex flex-col items-center justify-center min-h-[90px]">
                        <div id="file-upload-prompt" class="flex flex-col items-center">
                            <i class="fas fa-file-zipper text-slate-300 dark:text-slate-700 text-2xl mb-1.5"></i>
                            <p id="file-upload-label" class="text-[10px] text-slate-400 leading-normal">Drag files here, or tab to browse</p>
                        </div>
                        <input type="file" id="p-file-upload" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    </div>
                    <input type="text" id="p-file" required placeholder="Or enter file location: products/main.zip" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>

                <!-- DRAG DROP FILE UPLOAD FOR IMAGE THUMBNAILS -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1">Display Thumbnail Image</label>
                    <div id="drag-drop-zone" class="border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl p-4 text-center cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-850 transition-all mb-2 relative flex flex-col items-center justify-center min-h-[90px]">
                        <img id="thumbnail-preview" class="hidden max-h-16 rounded mb-1.5 object-cover" src="">
                        <div id="upload-prompt" class="flex flex-col items-center">
                            <i class="fas fa-cloud-arrow-up text-slate-300 dark:text-slate-700 text-2xl mb-1.5"></i>
                            <p class="text-[10px] text-slate-400 leading-normal">Drag image here, or tab to browse</p>
                        </div>
                        <input type="file" id="p-image-file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    </div>
                    <input type="text" id="p-image" required placeholder="Or enter image URL: https://..." class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1">Live Preview Link (URL)</label>
                        <input type="url" id="p-preview-url" placeholder="e.g. https://demo.digitalmohan.com/preview" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1">Preview Mode / System Type</label>
                        <select id="p-preview-type" onchange="togglePreviewDataHelpText()" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none text-xs font-semibold">
                            <option value="link">🌐 External Link (New Tab)</option>
                            <option value="iframe">💻 Immersive Device Mockframe Embed</option>
                            <option value="pdf">📕 Sleek PDF Slideshow Viewer</option>
                            <option value="images">📸 High-Res Screenshots Gallery</option>
                            <option value="video">🎥 YouTube / Vimeo Player Embed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1">Preview Dataset / Extended Sources</label>
                    <textarea id="p-preview-data" rows="2" placeholder="e.g. Comma-separated image URLs, or extra parameter config..." class="w-full p-3 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none text-xs font-semibold"></textarea>
                    <p id="preview-data-help" class="text-[9px] text-emerald-600 dark:text-emerald-450 mt-1 leading-normal font-semibold"></p>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1">Product Description</label>
                    <textarea id="p-desc" rows="3" required placeholder="Explain digital resource asset benefits..." class="w-full p-3 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="dismissProductWizard()" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:brightness-110 rounded-xl text-xs font-bold text-slate-650 dark:text-slate-300">Discard</button>
                    <button type="submit" class="px-5 py-2 bg-sky-500 hover:bg-sky-600 rounded-xl text-xs font-bold text-white shadow-md">Compile Changes</button>
                </div>

            </form>
        </div>
    </div>

    <!-- CATALOG GRID LIST -->
    <div id="catalog-list" class="grid grid-cols-2 gap-4">
        <!-- Loaded dynamically -->
        <div class="h-56 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        <div class="h-56 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
    </div>

</main>

<script>
let categoriesIndex = [];

document.addEventListener('DOMContentLoaded', () => {
    const admin = getSessionAdmin();
    if (!admin) { window.location.href="login.php"; return; }
    
    // Prepare categories selects 
    fetch('/api/categories')
        .then(res => res.json())
        .then(cats => {
            categoriesIndex = cats;
            const select = document.getElementById('p-category');
            select.innerHTML = cats.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            
            // Now fetch products list
            loadAdminProductsInventoryList();
        });
        
    // Bind real drag drop and browse events
    setupRealUploadHandlers();
});

function setupRealUploadHandlers() {
    // 1. Thumbnail Custom Upload
    const imageFileInput = document.getElementById('p-image-file');
    const imageTextInput = document.getElementById('p-image');
    const thumbnailPreview = document.getElementById('thumbnail-preview');
    const uploadPrompt = document.getElementById('upload-prompt');
    const dragDropZone = document.getElementById('drag-drop-zone');

    // Automatically compress heavy custom base64 images to safe sizes before DB write to maintain stellar DB speeds
    function compressBase64Image(base64Str, maxWidth = 480, maxHeight = 480, quality = 0.72) {
        return new Promise((resolve) => {
            const img = new Image();
            img.src = base64Str;
            img.onload = () => {
                let width = img.width;
                let height = img.height;
                if (width > maxWidth || height > maxHeight) {
                    if (width > height) {
                        height *= maxWidth / width;
                        width = maxWidth;
                    } else {
                        width *= maxHeight / height;
                        height = maxHeight;
                    }
                }
                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                resolve(canvas.toDataURL('image/jpeg', quality));
            };
            img.onerror = () => {
                resolve(base64Str);
            };
        });
    }

    imageFileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function(evt) {
            const rawBase = evt.target.result;
            // Compress the image first
            compressBase64Image(rawBase).then(compressedBase64 => {
                imageTextInput.value = compressedBase64;
                thumbnailPreview.src = compressedBase64;
                thumbnailPreview.classList.remove('hidden');
                uploadPrompt.classList.add('hidden');
                Toast.success("Custom thumbnail uploaded and compressed (Web-Ready)!");
            });
        };
        reader.readAsDataURL(file);
    });

    // 2. Deliverable Custom File Upload
    const productFileInput = document.getElementById('p-file-upload');
    const productFileTextInput = document.getElementById('p-file');
    const fileLabel = document.getElementById('file-upload-label');

    productFileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function(evt) {
            const base64Data = evt.target.result;
            productFileTextInput.value = base64Data + "|" + file.name;
            fileLabel.innerHTML = `<span class="text-emerald-500 font-bold"><i class="fas fa-circle-check"></i> ${file.name}</span><br><span class="text-[9px] text-slate-400">Ready for compilation</span>`;
            Toast.success("Deliverable product file uploaded successfully!");
        };
        reader.readAsDataURL(file);
    });

    // Thumbnail text box sync preview manually as backup
    imageTextInput.addEventListener('input', () => {
        const val = imageTextInput.value;
        if (val && val.startsWith('http')) {
            thumbnailPreview.src = val;
            thumbnailPreview.classList.remove('hidden');
            uploadPrompt.classList.add('hidden');
        } else if (!val) {
            thumbnailPreview.classList.add('hidden');
            uploadPrompt.classList.remove('hidden');
        }
    });

    // Styling highlights for drag drop dragging
    [dragDropZone, productFileInput.parentElement].forEach(el => {
        el.addEventListener('dragover', (e) => {
            e.preventDefault();
            el.classList.add('border-sky-500', 'bg-sky-50/5');
        });
        el.addEventListener('dragleave', () => {
            el.classList.remove('border-sky-500', 'bg-sky-50/5');
        });
        el.addEventListener('drop', () => {
            el.classList.remove('border-sky-500', 'bg-sky-50/5');
        });
    });
}

function togglePreviewDataHelpText() {
    const mode = document.getElementById('p-preview-type').value;
    const help = document.getElementById('preview-data-help');
    if (!help) return;
    
    if (mode === 'link') {
        help.innerText = "ℹ️ Standard new tab redirect: Redirects users using 'Live Preview Link' above in a new browser window.";
    } else if (mode === 'iframe') {
        help.innerText = "ℹ️ Device Mockup Frame: Opens a stunning canvas with toggle buttons to preview this site as modern Desktop view, Tablet view, or Mobile view directly in-app!";
    } else if (mode === 'pdf') {
        help.innerText = "ℹ️ PDF Slideshow viewer: Renders a beautifully interactive booklet styled reading desk, displaying the given PDF file perfectly. (You can paste a PDF link in 'Live Preview Link' or here).";
    } else if (mode === 'images') {
        help.innerText = "ℹ️ Screenshots Grid: Provide comma-separated high-res image URLs in this textarea (e.g. https://site.com/img1.jpg, https://site.com/img2.jpg) to generate a premium sliding layout catalog.";
    } else if (mode === 'video') {
        help.innerText = "ℹ️ Embedded video player: Paste a YouTube Video URL, Vimeo URL or ID to overlay a sleek video playback workspace.";
    }
}

let adminCurrentPage = 1;
const adminPageSize = 10;
let adminTotalPages = 1;

function loadAdminProductsInventoryList(pageNum = 1) {
    adminCurrentPage = pageNum;
    const container = document.getElementById('catalog-list');
    container.innerHTML = `
        <div class="h-48 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        <div class="h-48 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
    `;
    
    // Performance Fix: Request paginated view to protect memory and layout rendering speed on 10,000+ items
    fetch(`/api/products?page=${adminCurrentPage}&limit=${adminPageSize}&admin=true`)
        .then(res => res.json())
        .then(result => {
            const data = result.products || result || [];
            adminTotalPages = result.totalPages || 1;
            
            if (data.length === 0) {
                container.innerHTML = `
                    <div class="col-span-2 text-center py-10 rounded-3xl border border-dashed border-slate-250 dark:border-slate-800">
                        <p class="text-xs text-slate-400">Inventory warehouse is empty. Open setup wizard above.</p>
                    </div>
                `;
                renderAdminPaginationControls(0);
                return;
            }
            
            container.innerHTML = data.map(p => {
                const catObj = categoriesIndex.find(c => c.id === p.category_id);
                const catLabel = catObj ? catObj.name : "Category File";
                
                return `
                    <div class="rounded-2xl bg-white dark:bg-slate-900/65 border border-slate-200/50 dark:border-white/5 overflow-hidden shadow-sm relative group">
                        
                        <div class="absolute top-2 right-2 z-10 flex gap-1">
                            <button onclick="triggerEditProductWizard(${p.id})" class="w-6 h-6 rounded-md bg-white/85 backdrop-blur-md text-slate-600 hover:text-sky-500 hover:scale-105 active:scale-95 transition-all outline-none flex items-center justify-center">
                                <i class="fas fa-pencil text-[9px]"></i>
                            </button>
                            <button onclick="deleteProductCatalogueRecord(${p.id})" class="w-6 h-6 rounded-md bg-white/85 backdrop-blur-md text-slate-600 hover:text-red-500 hover:scale-105 active:scale-95 transition-all outline-none flex items-center justify-center">
                                <i class="fas fa-trash-can text-[9px]"></i>
                            </button>
                        </div>

                        <img src="${window.getOptimizedImageUrl(p.image, 300)}" class="w-full h-24 object-cover" loading="lazy">
                        <div class="p-3">
                            <span class="text-[8px] uppercase font-black tracking-wider text-sky-500 mb-0.5 block">${catLabel}</span>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate leading-snug">${p.title}</h4>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                <span class="text-xs font-black text-slate-800 dark:text-slate-350 font-mono">₹${p.price}</span>
                                <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded-full ${(p.status === 'active' || !p.status) ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-450' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'}">${p.status || 'active'}</span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            renderAdminPaginationControls(adminTotalPages);
        });
}

function renderAdminPaginationControls(totalPages) {
    let paginationBar = document.getElementById('admin-pagination-bar');
    if (!paginationBar) {
        const container = document.getElementById('catalog-list');
        paginationBar = document.createElement('div');
        paginationBar.id = 'admin-pagination-bar';
        paginationBar.className = 'col-span-2 flex items-center justify-between mt-6 bg-slate-50 dark:bg-slate-850 p-2.5 rounded-2xl border border-slate-150/80 dark:border-white/5';
        container.insertAdjacentElement('afterend', paginationBar);
    }
    
    if (totalPages <= 1) {
        paginationBar.classList.add('hidden');
        return;
    } else {
        paginationBar.classList.remove('hidden');
    }
    
    paginationBar.innerHTML = `
        <button onclick="goToAdminPage(${adminCurrentPage - 1})" ${adminCurrentPage === 1 ? 'disabled class="opacity-40 cursor-not-allowed text-xs font-black text-slate-400 dark:text-slate-500 p-2"' : 'class="text-xs font-black text-sky-500 hover:text-sky-600 p-2 hover:bg-sky-50 rounded-xl transition-all cursor-pointer"'}>
            <i class="fas fa-chevron-left mr-1"></i> Prev
        </button>
        <span class="text-xs font-bold text-slate-650 dark:text-slate-350 bg-slate-100 dark:bg-slate-800 px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
            Page ${adminCurrentPage} of ${totalPages}
        </span>
        <button onclick="goToAdminPage(${adminCurrentPage + 1})" ${adminCurrentPage === totalPages ? 'disabled class="opacity-40 cursor-not-allowed text-xs font-black text-slate-400 dark:text-slate-500 p-2"' : 'class="text-xs font-black text-sky-500 hover:text-sky-600 p-2 hover:bg-sky-50 rounded-xl transition-all cursor-pointer"'}>
            Next <i class="fas fa-chevron-right ml-1"></i>
        </button>
    `;
}

function goToAdminPage(p) {
    if (p < 1 || p > adminTotalPages) return;
    triggerVibe(30);
    loadAdminProductsInventoryList(p);
}

function triggerNewProductWizard() {
    triggerVibe(30);
    document.getElementById('edit-prod-id').value = '';
    document.getElementById('wizard-form').reset();
    document.getElementById('wizard-title').textContent = "Assign Product File Specifications";
    
    // Reset file and image previews
    document.getElementById('thumbnail-preview').classList.add('hidden');
    document.getElementById('thumbnail-preview').src = '';
    document.getElementById('upload-prompt').classList.remove('hidden');
    document.getElementById('file-upload-label').innerHTML = 'Drag files here, or tab to browse';
    document.getElementById('p-preview-url').value = '';
    document.getElementById('p-preview-type').value = 'link';
    document.getElementById('p-preview-data').value = '';
    togglePreviewDataHelpText();
    
    document.getElementById('product-modal').classList.remove('hidden');
}

function triggerEditProductWizard(id) {
    triggerVibe(30);
    // Performance Fix: Fetch single product details directly by ID instead of pulling full inventory
    fetch(`/api/products/detail/${id}`)
        .then(res => {
            if (!res.ok) throw new Error();
            return res.json();
        })
        .then(p => {
            if (!p) return;
            
            document.getElementById('edit-prod-id').value = p.id;
            document.getElementById('p-title').value = p.title;
            document.getElementById('p-mrp').value = p.mrp;
            document.getElementById('p-price').value = p.price;
            document.getElementById('p-category').value = p.category_id;
            document.getElementById('p-status').value = p.status;
            document.getElementById('p-file').value = p.file;
            document.getElementById('p-image').value = p.image;
            document.getElementById('p-desc').value = p.description;
            document.getElementById('p-preview-url').value = p.preview_url || '';
            document.getElementById('p-preview-type').value = p.preview_type || 'link';
            document.getElementById('p-preview-data').value = p.preview_data || '';
            togglePreviewDataHelpText();
            
            // Set image preview
            const preview = document.getElementById('thumbnail-preview');
            const prompt = document.getElementById('upload-prompt');
            if (p.image) {
                preview.src = p.image;
                preview.classList.remove('hidden');
                prompt.classList.add('hidden');
            } else {
                preview.classList.add('hidden');
                prompt.classList.remove('hidden');
            }

            // Set file label
            const fileLabel = document.getElementById('file-upload-label');
            if (p.file && p.file.includes('|')) {
                const parts = p.file.split('|');
                fileLabel.innerHTML = `<span class="text-emerald-500 font-bold"><i class="fas fa-circle-check"></i> ${parts[1]}</span><br><span class="text-[9px] text-slate-400">Custom uploaded file</span>`;
            } else if (p.file) {
                const fname = p.file.substring(p.file.lastIndexOf('/') + 1);
                fileLabel.innerHTML = `<span class="text-sky-500 font-bold"><i class="fas fa-file"></i> ${fname}</span><br><span class="text-[9px] text-slate-400">File location path mapped</span>`;
            } else {
                fileLabel.innerHTML = 'Drag files here, or tab to browse';
            }
            
            document.getElementById('wizard-title').textContent = "Edit Product File Specifications";
            document.getElementById('product-modal').classList.remove('hidden');
        });
}

function dismissProductWizard() {
    triggerVibe(20);
    document.getElementById('product-modal').classList.add('hidden');
}

function saveProductForm(e) {
    e.preventDefault();
    triggerVibe(50);
    
    const id = document.getElementById('edit-prod-id').value;
    const title = document.getElementById('p-title').value;
    const mrp = parseFloat(document.getElementById('p-mrp').value);
    const price = parseFloat(document.getElementById('p-price').value);
    const category_id = parseInt(document.getElementById('p-category').value);
    const status = document.getElementById('p-status').value;
    const file = document.getElementById('p-file').value;
    const image = document.getElementById('p-image').value;
    const description = document.getElementById('p-desc').value;
    const preview_url = document.getElementById('p-preview-url').value;
    const preview_type = document.getElementById('p-preview-type').value;
    const preview_data = document.getElementById('p-preview-data').value;
    
    const endpoint = id ? '/api/products/update' : '/api/products/create';
    const body = {
        title,
        mrp,
        price,
        category_id,
        status,
        file,
        image,
        description,
        preview_url,
        preview_type,
        preview_data
    };
    if (id) body.id = parseInt(id);
    
    fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Catalogue specs saved successfully!");
            dismissProductWizard();
            loadAdminProductsInventoryList();
        } else {
            Toast.error(data.error || "Form validation reject exception.");
        }
    });
}

function generateAICatCopy() {
    const topic = document.getElementById('ai-keywords').value.trim();
    const catSelect = document.getElementById('p-category');
    const categoryName = catSelect.options[catSelect.selectedIndex]?.text || "General";
    
    if (!topic) {
        Toast.error("Provide a keyword topic first (e.g., Python Formula Sheets)");
        return;
    }
    
    const btn = document.getElementById('btn-ai-generate');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<i class="fas fa-spinner animate-spin text-[8px]"></i> Drafting...`;
    triggerVibe(30);
    
    fetch('/api/admin/generate-product-copy', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ topic, category: categoryName })
    })
    .then(res => {
        if (!res.ok) throw new Error();
        return res.json();
    })
    .then(data => {
        document.getElementById('p-title').value = data.title || '';
        document.getElementById('p-mrp').value = data.mrp || 499;
        document.getElementById('p-price').value = data.price || 99;
        document.getElementById('p-desc').value = data.description || '';
        document.getElementById('p-file').value = data.file || 'premium_bundle.zip';
        
        Toast.success("AI generated metadata specifications applied!");
        triggerVibe(50);
    })
    .catch(() => {
        Toast.error("Could not reach Gemini AI copy writer lines.");
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

function deleteProductCatalogueRecord(id) {
    if (!confirm("Are you sure you want to completely drop this item list? All active purchase logs remain bound but files dereference.")) {
        return;
    }
    
    triggerVibe(70);
    fetch('/api/products/delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Product scrubbed from database.");
            loadAdminProductsInventoryList();
        } else {
            Toast.error(data.error);
        }
    });
}
</script>

<!-- Custom Toast -->
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none max-w-sm w-full px-4 sm:px-0"></div>
<script>
const Toast = {
    show: function(m, type='success') {
        const container = document.getElementById('toast-container');
        const t = document.createElement('div');
        t.className = `p-4 rounded-xl shadow-lg border border-white/5 backdrop-blur-md text-xs font-bold ${type === 'error' ? 'bg-red-500' : 'bg-emerald-500'} text-white transition-all transform duration-300`;
        t.textContent = m;
        container.appendChild(t);
        setTimeout(() => t.remove(), 2200);
    },
    success: function(m) { this.show(m, 'success'); },
    error: function(m) { this.show(m, 'error'); }
};
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
