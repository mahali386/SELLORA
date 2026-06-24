import express from "express";
import path from "path";
import fs from "fs/promises";
import { createServer as createViteServer } from "vite";
import { GoogleGenAI } from "@google/genai";

// Initialize Gemini SDK client with lazy safeguards for missing API keys
const aiClient = process.env.GEMINI_API_KEY ? new GoogleGenAI({
  apiKey: process.env.GEMINI_API_KEY,
  httpOptions: {
    headers: {
      "User-Agent": "aistudio-build"
    }
  }
}) : null;

const app = express();
const PORT = 3000;

app.use(express.json({ limit: "50mb" }));
app.use(express.urlencoded({ extended: true, limit: "50mb" }));

// Serve custom file and image uploads directory cleanly
app.use("/uploads", express.static(path.join(process.cwd(), "uploads")));

// Translate php-routing fallback URL structure back to native Express API routes
app.use((req, res, next) => {
  if (req.url.includes("api.php")) {
    const routeParam = req.query.route as string;
    let targetUrl = "";
    if (routeParam) {
      targetUrl = routeParam;
    } else {
      const idx = req.url.indexOf("api.php");
      if (idx !== -1) {
        targetUrl = req.url.substring(idx + 7);
      }
    }

    if (targetUrl) {
      req.url = targetUrl;
      
      // Parse query parameters from the new rewritten URL and update req.query
      const urlParts = targetUrl.split("?");
      const queryString = urlParts[1] || "";
      
      // Rebuild query object
      const newQuery: Record<string, any> = {};
      const pairs = queryString.split("&");
      for (const pair of pairs) {
        if (!pair) continue;
        const [key, val] = pair.split("=");
        newQuery[decodeURIComponent(key)] = decodeURIComponent(val || "");
      }
      
      // Merge with any existing queries (just in case)
      req.query = { ...req.query, ...newQuery };
      
      // Explicitly clear Express URL parse caches to force matching on the updated req.url pathname
      // @ts-ignore
      req._parsedUrl = undefined;
      // @ts-ignore
      req._parsedUrlSelf = undefined;
    }
  }
  next();
});

// Local JSON Database simulation directory and file
const DB_FILE = path.join(process.cwd(), "database.json");

interface DBStore {
  users: any[];
  admin: any[];
  categories: any[];
  products: any[];
  orders: any[];
  coupons: any[];
  settings: any;
  wishlist: any[];
  reviews: any[];
  download_logs: any[];
  notifications: any[];
  recently_viewed: any[];
  queries: any[];
  subscribers: any[];
  campaigns: any[];
  sent_emails: any[];
  banners?: any[];
  blogs?: any[];
  affiliates?: any[];
  affiliate_commissions?: any[];
  affiliate_payouts?: any[];
}

const DEFAULT_DB: DBStore = {
  users: [
    { id: 1, name: "John Doe", phone: "9876543210", email: "user@example.com", password: "password123", status: "active", created_at: new Date().toISOString() }
  ],
  admin: [
    { id: 1, username: "admin", password: "password123" }
  ],
  categories: [
    { id: 1, name: "ChatGPT Prompts" },
    { id: 2, name: "Resume Templates" },
    { id: 3, name: "Exam Notes" },
    { id: 4, name: "Canva Designs" }
  ],
  products: [
    {
      id: 1,
      category_id: 1,
      title: "ChatGPT Mega Prompt Pack (10,000+ Master Prompts)",
      mrp: 999,
      price: 299,
      description: "Supercharge your business, writing, coding, and marketing with our masterfully compiled, 100% tested prompts folder directory. Instantly boost your productivity by 10x.",
      file: "chatgpt_mega_pack.zip",
      image: "https://images.unsplash.com/photo-1677442136019-21780efad99a?w=600&q=80",
      status: "active",
      created_at: new Date(Date.now() - 4 * 24 * 3600 * 1000).toISOString()
    },
    {
      id: 2,
      category_id: 2,
      title: "Premium ATS-Friendly Resume Template",
      mrp: 499,
      price: 99,
      description: "Get hired faster with this minimalist, professionally formatted template optimized for resume screening algorithms. Comes in fully editable DOCX and PDF formats.",
      file: "resume_ats_premium.zip",
      image: "https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=600&q=80",
      status: "active",
      created_at: new Date(Date.now() - 2 * 24 * 3600 * 1000).toISOString()
    },
    {
      id: 3,
      category_id: 3,
      title: "IIT-JEE Physics Ultimate Quick Revision Formula Sheets",
      mrp: 299,
      price: 0, // Free product to attract leads and run ads
      description: "Compiled list of critical formulae, key theories, and solved diagrammatic examples for rapid JEE Main & Advanced revision. Authored by top IITian mentors.",
      file: "jeep_physics_ref.pdf",
      image: "https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=600&q=80",
      status: "active",
      created_at: new Date().toISOString()
    },
    {
      id: 4,
      category_id: 4,
      title: "150+ Ready-To-Host Instagram Carousel Templates (Canva Link Included)",
      mrp: 1499,
      price: 499,
      description: "A gorgeous collection of grid layouts, gradient backdrops, and modern typography carousels. Stop wasting time figuring out design coordinates.",
      file: "canva_bundle_insta.zip",
      image: "https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=600&q=80",
      status: "active",
      created_at: new Date().toISOString()
    }
  ],
  orders: [],
  coupons: [
    { id: 1, code: "SAVE50", discount: 50, expiry: "2030-12-31", usage_limit: 100, used_count: 5 },
    { id: 2, code: "FREE100", discount: 100, expiry: "2030-12-31", usage_limit: 10, used_count: 0 }
  ],
  settings: {
    id: 1,
    app_name: "DigitalMohan",
    razorpay_key: "rzp_test_S5bDUB1XnvePGT",
    razorpay_secret: "wksVp8etGWelSTTrCzN3VMd2",
    support_email: "support@digitalmohan.com",
    support_phone: "+91 98765 43210",
    theme_color: "#0284C7",
    maintenance_mode: 0,
    whatsapp_group_enabled: 1,
    whatsapp_group_title: "Join Our Premium WhatsApp Community! 🚀",
    whatsapp_group_link: "https://chat.whatsapp.com/GjMockGrpLnk2026Sellora",
    whatsapp_group_description: "Get instant high-quality templates, free resume tools, and direct support updates daily. Join 10,000+ members!",
    whatsapp_group_delay: 5000,
    whatsapp_group_autoclose: 10000
  },
  wishlist: [],
  reviews: [
    { id: 1, user_id: 1, product_id: 1, rating: 5, comment: "Absolutely marvelous file structure! Instantly replaced my tedious prompts.", created_at: new Date().toISOString() }
  ],
  download_logs: [],
  notifications: [
    { id: 1, user_id: 1, title: "Welcome to DigitalMohan!", message: "Explore and purchase top-tier templates and revision files instantly.", is_read: 0, created_at: new Date().toISOString() }
  ],
  recently_viewed: [],
  queries: [],
  subscribers: [],
  campaigns: [],
  sent_emails: [],
  blogs: [],
  affiliates: [],
  affiliate_commissions: [],
  affiliate_payouts: []
};

let cachedDb: DBStore | null = null;

// Ensure JSON DB exists and read it
async function loadDB(): Promise<DBStore> {
  if (cachedDb) {
    return cachedDb;
  }
  try {
    const data = await fs.readFile(DB_FILE, "utf-8");
    const db = JSON.parse(data);
    if (!db.queries) db.queries = [];
    if (!db.subscribers) db.subscribers = [];
    if (!db.campaigns) db.campaigns = [];
    if (!db.sent_emails) db.sent_emails = [];
    if (!db.blogs) db.blogs = [];
    if (!db.affiliates) db.affiliates = [];
    if (!db.affiliate_commissions) db.affiliate_commissions = [];
    if (!db.affiliate_payouts) db.affiliate_payouts = [];
    if (!db.notifications) db.notifications = [];
    if (!db.users) db.users = [];
    if (!db.products) db.products = [];
    if (!db.orders) db.orders = [];
    if (!db.coupons) db.coupons = [];
    if (!db.wishlist) db.wishlist = [];
    if (!db.reviews) db.reviews = [];
    if (!db.download_logs) db.download_logs = [];
    if (!db.recently_viewed) db.recently_viewed = [];
    cachedDb = db;
    return db;
  } catch (error) {
    await fs.writeFile(DB_FILE, JSON.stringify(DEFAULT_DB, null, 2), "utf-8");
    cachedDb = JSON.parse(JSON.stringify(DEFAULT_DB));
    return cachedDb!;
  }
}

// Performance Caching: Keep an in-memory product queries cache to avoid expensive operations on 10,000+ records
const productsCache = new Map<string, any>();

function clearProductsCache() {
  productsCache.clear();
}

async function handleBase64Upload(fieldValue: string, prefix: string): Promise<string> {
  if (!fieldValue || !fieldValue.startsWith("data:")) {
    return fieldValue;
  }
  try {
    const parts = fieldValue.split(";base64,");
    if (parts.length < 2) return fieldValue;

    const mimePart = parts[0];
    const mime = mimePart.substring(5);
    
    let base64Data = parts[1];
    let originalName = "";
    if (base64Data.includes("|")) {
      const bParts = base64Data.split("|");
      base64Data = bParts[0];
      originalName = bParts[1];
    }

    const buffer = Buffer.from(base64Data, "base64");
    
    let ext = "bin";
    if (mime.includes("jpeg") || mime.includes("jpg")) ext = "jpg";
    else if (mime.includes("png")) ext = "png";
    else if (mime.includes("gif")) ext = "gif";
    else if (mime.includes("webp")) ext = "webp";
    else if (mime.includes("pdf")) ext = "pdf";
    else if (mime.includes("zip")) ext = "zip";
    else if (mime.includes("vnd.openxmlformats-officedocument.wordprocessingml.document")) ext = "docx";
    else if (mime.includes("word") || mime.includes("msword")) ext = "doc";
    
    if (originalName) {
      const match = originalName.match(/\.([a-zA-Z0-9]+)$/);
      if (match) ext = match[1].toLowerCase();
    }
    
    const uploadDir = path.join(process.cwd(), "uploads");
    try {
      await fs.access(uploadDir);
    } catch {
      await fs.mkdir(uploadDir, { recursive: true });
    }

    const uniqueId = Date.now() + "_" + Math.floor(Math.random() * 100000);
    const fileName = `${prefix}_${uniqueId}.${ext}`;
    const filePath = path.join(uploadDir, fileName);
    
    await fs.writeFile(filePath, buffer);
    return `/uploads/${fileName}`;
  } catch (err: any) {
    console.error("Base64 upload saving failed:", err);
    return fieldValue;
  }
}

async function saveDB(db: DBStore) {
  cachedDb = db;
  clearProductsCache(); // automatically burst cache on updates
  await fs.writeFile(DB_FILE, JSON.stringify(db, null, 2), "utf-8");
}

// APIs

// 1. Auth & Users
app.post("/api/auth/register", async (req, res) => {
  const { name, phone, email, password } = req.body;
  const db = await loadDB();
  const existing = db.users.find(u => u.email === email);
  if (existing) {
    return res.status(400).json({ error: "Email already registered" });
  }
  const newUser = {
    id: db.users.length > 0 ? Math.max(...db.users.map(u => u.id)) + 1 : 1,
    name,
    phone,
    email,
    password, // simplified hash or string for preview
    status: "active",
    created_at: new Date().toISOString()
  };
  db.users.push(newUser);
  await saveDB(db);
  res.json({ success: true, user: { id: newUser.id, name: newUser.name, email: newUser.email, phone: newUser.phone } });
});

app.post("/api/auth/login", async (req, res) => {
  const { email, password } = req.body;
  const db = await loadDB();
  const user = db.users.find(u => u.email === email && u.password === password);
  if (!user) {
    return res.status(400).json({ error: "Invalid email or password" });
  }
  if (user.status === "blocked") {
    return res.status(403).json({ error: "Your account is suspended." });
  }
  res.json({ success: true, user: { id: user.id, name: user.name, email: user.email, phone: user.phone } });
});

app.post("/api/auth/admin-login", async (req, res) => {
  const { username, password } = req.body;
  const db = await loadDB();
  const admin = db.admin.find(a => a.username === username && a.password === password);
  if (!admin) {
    return res.status(400).json({ error: "Invalid credentials" });
  }
  res.json({ success: true, admin: { id: admin.id, username: admin.username } });
});

app.get("/api/users", async (req, res) => {
  const db = await loadDB();
  res.json(db.users);
});

app.post("/api/users/toggle", async (req, res) => {
  const { userId } = req.body;
  const db = await loadDB();
  const user = db.users.find(u => u.id === userId);
  if (user) {
    user.status = user.status === "active" ? "blocked" : "active";
    await saveDB(db);
    res.json({ success: true, status: user.status });
  } else {
    res.status(404).json({ error: "User not found" });
  }
});

// Update Profile
app.post("/api/users/update", async (req, res) => {
  const { id, name, phone, password } = req.body;
  const db = await loadDB();
  const user = db.users.find(u => u.id === id);
  if (user) {
    if (name) user.name = name;
    if (phone) user.phone = phone;
    if (password) user.password = password;
    await saveDB(db);
    res.json({ success: true, user: { id: user.id, name: user.name, email: user.email, phone: user.phone } });
  } else {
    res.status(404).json({ error: "User not found" });
  }
});

// 2. Categories
app.get("/api/categories", async (req, res) => {
  const db = await loadDB();
  res.json(db.categories);
});

app.post("/api/categories", async (req, res) => {
  const { name } = req.body;
  const db = await loadDB();
  const newCat = {
    id: db.categories.length > 0 ? Math.max(...db.categories.map(c => c.id)) + 1 : 1,
    name
  };
  db.categories.push(newCat);
  await saveDB(db);
  res.json({ success: true, category: newCat });
});

// Category aliases
app.post("/api/categories/create", async (req, res) => {
  const { name } = req.body;
  const db = await loadDB();
  const newCat = {
    id: db.categories.length > 0 ? Math.max(...db.categories.map(c => c.id)) + 1 : 1,
    name
  };
  db.categories.push(newCat);
  await saveDB(db);
  res.json({ success: true, category: newCat });
});

app.post("/api/categories/update", async (req, res) => {
  const { id, name } = req.body;
  const db = await loadDB();
  const cat = db.categories.find(c => c.id === id);
  if (cat) {
    cat.name = name;
    await saveDB(db);
    res.json({ success: true, category: cat });
  } else {
    res.status(404).json({ error: "Category not found" });
  }
});

app.post("/api/categories/delete", async (req, res) => {
  const { id } = req.body;
  const db = await loadDB();
  db.categories = db.categories.filter(c => c.id !== id);
  await saveDB(db);
  res.json({ success: true });
});

app.delete("/api/categories/:id", async (req, res) => {
  const id = parseInt(req.params.id);
  const db = await loadDB();
  db.categories = db.categories.filter(c => c.id !== id);
  await saveDB(db);
  res.json({ success: true });
});

// 3. Products
app.get("/api/products", async (req, res) => {
  const db = await loadDB();

  const page = req.query.page ? parseInt(req.query.page as string) : null;
  const limit = req.query.limit ? parseInt(req.query.limit as string) : null;
  const search = req.query.search ? (req.query.search as string).toLowerCase().trim() : null;
  const category_id = req.query.category_id ? parseInt(req.query.category_id as string) : null;
  const price = req.query.price as string; // 'free', 'paid'
  const sort = req.query.sort as string; // 'newest', 'low-high', 'high-low', 'popular'
  const admin = req.query.admin === "true";

  // Cache Lookup
  const cacheKey = JSON.stringify({ page, limit, search, category_id, price, sort, admin });
  if (productsCache.has(cacheKey)) {
    return res.json(productsCache.get(cacheKey));
  }

  // Admin page gets all status; public only sees active
  let products = db.products || [];
  if (!admin) {
    products = products.filter(p => !p.status || p.status === 'active');
  }

  // Filter Category_ID
  if (category_id && !isNaN(category_id)) {
    products = products.filter(p => p.category_id === category_id);
  }

  // Filter free/paid price
  if (price === 'free') {
    products = products.filter(p => Number(p.price) === 0);
  } else if (price === 'paid') {
    products = products.filter(p => Number(p.price) > 0);
  }

  // Search keyword match (lowered case)
  if (search) {
    products = products.filter(p => 
      (p.title && p.title.toLowerCase().includes(search)) || 
      (p.description && p.description.toLowerCase().includes(search))
    );
  }

  // Sorting
  if (sort === 'low-high') {
    products = [...products].sort((a, b) => (Number(a.price) || 0) - (Number(b.price) || 0));
  } else if (sort === 'high-low') {
    products = [...products].sort((a, b) => (Number(b.price) || 0) - (Number(a.price) || 0));
  } else if (sort === 'popular') {
    products = [...products].sort((a, b) => (Number(b.mrp) || 0) - (Number(a.mrp) || 0));
  } else {
    // default: newest
    products = [...products].sort((a, b) => new Date(b.created_at || 0).getTime() - new Date(a.created_at || 0).getTime());
  }

  // Paginated return structure
  if (limit) {
    const activePage = page || 1;
    const startIndex = (activePage - 1) * limit;
    const paginatedProducts = products.slice(startIndex, startIndex + limit);
    const result = {
      products: paginatedProducts,
      total: products.length,
      page: activePage,
      limit,
      totalPages: Math.ceil(products.length / limit)
    };
    productsCache.set(cacheKey, result);
    return res.json(result);
  }

  productsCache.set(cacheKey, products);
  res.json(products);
});

// Optimized: Get single product by id endpoint
app.get("/api/products/detail/:id", async (req, res) => {
  const pId = parseInt(req.params.id || "0");
  const db = await loadDB();
  const product = (db.products || []).find(p => p.id === pId);
  if (!product) {
    return res.status(404).json({ error: "Product specifications not found." });
  }
  res.json(product);
});

// Dedicated Server-side computed analytics endpoint for Admin Bento Room
app.get("/api/admin/metrics", async (req, res) => {
  const db = await loadDB();
  
  const users_count = db.users ? db.users.length : 0;
  const products_count = db.products ? db.products.length : 0;
  
  const successfulOrders = (db.orders || []).filter(o => o.status === 'successful');
  const sales_count = successfulOrders.length;
  const revenue = successfulOrders.reduce((sum, o) => sum + (Number(o.amount) || 0), 0);
  
  // Compute last 7 days sales matrix on the server with pre-indexing to avoid high iteration fees
  const days: string[] = [];
  const salesData: number[] = [];
  
  const orderDateMap = new Map<string, number>();
  successfulOrders.forEach(o => {
    try {
      const dateStr = new Date(o.created_at).toDateString();
      orderDateMap.set(dateStr, (orderDateMap.get(dateStr) || 0) + (Number(o.amount) || 0));
    } catch(e) {}
  });

  for (let i = 6; i >= 0; i--) {
    const d = new Date();
    d.setDate(d.getDate() - i);
    const dayStr = d.toLocaleDateString(undefined, { weekday: 'short' });
    days.push(dayStr);
    
    const matchedRev = orderDateMap.get(d.toDateString()) || 0;
    salesData.push(matchedRev);
  }
  
  // Maps lookup to implement super fast O(N+M) live feed join
  const userMap = new Map();
  if (db.users) {
    db.users.forEach(u => userMap.set(u.id, u));
  }
  
  const productMap = new Map();
  if (db.products) {
    db.products.forEach(p => productMap.set(p.id, p));
  }
  
  const sortedOrders = [...(db.orders || [])]
    .sort((a, b) => new Date(b.created_at || 0).getTime() - new Date(a.created_at || 0).getTime())
    .slice(0, 10);
    
  const live_orders = sortedOrders.map(o => {
    const u = userMap.get(o.user_id) || { email: "deleted@user.com" };
    const p = productMap.get(o.product_id) || { title: "Deleted Product Specifications" };
    return {
      id: o.id,
      amount: o.amount,
      status: o.status,
      created_at: o.created_at,
      user_email: u.email,
      product_title: p.title
    };
  });
  
  res.json({
    users_count,
    products_count,
    sales_count,
    revenue,
    salesData,
    days,
    live_orders
  });
});

app.post("/api/products", async (req, res) => {
  const { category_id, title, mrp, price, description, file, image, status, preview_url, preview_type, preview_data } = req.body;
  const db = await loadDB();
  
  const savedFile = await handleBase64Upload(file, "product_file");
  const savedImage = await handleBase64Upload(image, "product_image");

  const newProd = {
    id: db.products.length > 0 ? Math.max(...db.products.map(p => p.id)) + 1 : 1,
    category_id: parseInt(category_id),
    title,
    mrp: Number(mrp),
    price: Number(price),
    description,
    file: savedFile || "default_file.zip",
    image: savedImage || "https://images.unsplash.com/photo-1541963463532-d68292c34b19?w=600&q=80",
    status: status || "active",
    created_at: new Date().toISOString(),
    preview_url: preview_url || "",
    preview_type: preview_type || "link",
    preview_data: preview_data || ""
  };
  db.products.push(newProd);

  // Broad push notification to users
  let nextNotificationId = db.notifications.length > 0 ? Math.max(...db.notifications.map(n => n.id)) + 1 : 1;
  db.notifications.push({
    id: nextNotificationId,
    user_id: 0, // 0 signifies a global announcement for all accounts
    title: "🔥 New Product Launched!",
    message: `We've just published a hot-seller: "${title}" for only ₹${price}. Unlock exclusive digital files now!`,
    is_read: 0,
    created_at: new Date().toISOString()
  });

  await saveDB(db);
  res.json({ success: true, product: newProd });
});

// Product Aliases
app.post("/api/products/create", async (req, res) => {
  const { category_id, title, mrp, price, description, file, image, status, preview_url, preview_type, preview_data } = req.body;
  const db = await loadDB();

  const savedFile = await handleBase64Upload(file, "product_file");
  const savedImage = await handleBase64Upload(image, "product_image");

  const newProd = {
    id: db.products.length > 0 ? Math.max(...db.products.map(p => p.id)) + 1 : 1,
    category_id: parseInt(category_id),
    title,
    mrp: Number(mrp),
    price: Number(price),
    description,
    file: savedFile || "default_file.zip",
    image: savedImage || "https://images.unsplash.com/photo-1541963463532-d68292c34b19?w=600&q=80",
    status: status || "active",
    created_at: new Date().toISOString(),
    preview_url: preview_url || "",
    preview_type: preview_type || "link",
    preview_data: preview_data || ""
  };
  db.products.push(newProd);

  // Broad push notification to users
  let nextNotificationIdCreate = db.notifications.length > 0 ? Math.max(...db.notifications.map(n => n.id)) + 1 : 1;
  db.notifications.push({
    id: nextNotificationIdCreate,
    user_id: 0, // 0 signifies a global announcement for all accounts
    title: "🔥 New Product Launched!",
    message: `We've just published a hot-seller: "${title}" for only ₹${price}. Unlock exclusive digital files now!`,
    is_read: 0,
    created_at: new Date().toISOString()
  });

  await saveDB(db);
  res.json({ success: true, product: newProd });
});

app.post("/api/products/update", async (req, res) => {
  const { id, category_id, title, mrp, price, description, file, image, status, preview_url, preview_type, preview_data } = req.body;
  const db = await loadDB();
  const idx = db.products.findIndex(p => p.id === id);
  if (idx !== -1) {
    const savedFile = await handleBase64Upload(file, "product_file");
    const savedImage = await handleBase64Upload(image, "product_image");

    db.products[idx] = {
      ...db.products[idx],
      category_id: parseInt(category_id),
      title,
      mrp: Number(mrp),
      price: Number(price),
      description,
      file: savedFile || db.products[idx].file,
      image: savedImage || db.products[idx].image,
      status: status || db.products[idx].status,
      preview_url: preview_url !== undefined ? preview_url : (db.products[idx].preview_url || ""),
      preview_type: preview_type !== undefined ? preview_type : (db.products[idx].preview_type || "link"),
      preview_data: preview_data !== undefined ? preview_data : (db.products[idx].preview_data || "")
    };
    await saveDB(db);
    res.json({ success: true, product: db.products[idx] });
  } else {
    res.status(404).json({ error: "Product not found" });
  }
});

app.post("/api/products/delete", async (req, res) => {
  const { id } = req.body;
  const db = await loadDB();
  db.products = db.products.filter(p => p.id !== id);
  await saveDB(db);
  res.json({ success: true });
});

app.put("/api/products/:id", async (req, res) => {
  const id = parseInt(req.params.id);
  const { category_id, title, mrp, price, description, file, image, status, preview_url, preview_type, preview_data } = req.body;
  const db = await loadDB();
  const idx = db.products.findIndex(p => p.id === id);
  if (idx !== -1) {
    const savedFile = await handleBase64Upload(file, "product_file");
    const savedImage = await handleBase64Upload(image, "product_image");

    db.products[idx] = {
      ...db.products[idx],
      category_id: parseInt(category_id),
      title,
      mrp: Number(mrp),
      price: Number(price),
      description,
      file: savedFile || db.products[idx].file,
      image: savedImage || db.products[idx].image,
      status: status || db.products[idx].status,
      preview_url: preview_url !== undefined ? preview_url : (db.products[idx].preview_url || ""),
      preview_type: preview_type !== undefined ? preview_type : (db.products[idx].preview_type || "link"),
      preview_data: preview_data !== undefined ? preview_data : (db.products[idx].preview_data || "")
    };
    await saveDB(db);
    res.json({ success: true, product: db.products[idx] });
  } else {
    res.status(404).json({ error: "Product not found" });
  }
});

app.post("/api/products/bulk-toggle", async (req, res) => {
  const { ids, status } = req.body;
  const db = await loadDB();
  db.products.forEach(p => {
    if (ids.includes(p.id)) {
      p.status = status;
    }
  });
  await saveDB(db);
  res.json({ success: true });
});

app.delete("/api/products/:id", async (req, res) => {
  const id = parseInt(req.params.id);
  const db = await loadDB();
  db.products = db.products.filter(p => p.id !== id);
  await saveDB(db);
  res.json({ success: true });
});

// 4. Coupons
app.get("/api/coupons", async (req, res) => {
  const db = await loadDB();
  res.json(db.coupons);
});

app.post("/api/coupons", async (req, res) => {
  const { code, discount, expiry, usage_limit } = req.body;
  const db = await loadDB();
  const newCoupon = {
    id: db.coupons.length > 0 ? Math.max(...db.coupons.map(c => c.id)) + 1 : 1,
    code: code.toUpperCase(),
    discount: Number(discount),
    expiry,
    usage_limit: Number(usage_limit),
    used_count: 0
  };
  db.coupons.push(newCoupon);
  await saveDB(db);
  res.json({ success: true, coupon: newCoupon });
});

// Coupons Aliases
app.post("/api/coupons/create", async (req, res) => {
  const { code, discount, expiry, usage_limit } = req.body;
  const db = await loadDB();
  const newCoupon = {
    id: db.coupons.length > 0 ? Math.max(...db.coupons.map(c => c.id)) + 1 : 1,
    code: code.toUpperCase(),
    discount: Number(discount),
    expiry,
    usage_limit: Number(usage_limit),
    used_count: 0
  };
  db.coupons.push(newCoupon);
  await saveDB(db);
  res.json({ success: true, coupon: newCoupon });
});

app.post("/api/coupons/delete", async (req, res) => {
  const { id } = req.body;
  const db = await loadDB();
  db.coupons = db.coupons.filter(c => c.id !== id);
  await saveDB(db);
  res.json({ success: true });
});

app.delete("/api/coupons/:id", async (req, res) => {
  const id = parseInt(req.params.id);
  const db = await loadDB();
  db.coupons = db.coupons.filter(c => c.id !== id);
  await saveDB(db);
  res.json({ success: true });
});

app.post("/api/coupons/validate", async (req, res) => {
  const { code } = req.body;
  const db = await loadDB();
  const coupon = db.coupons.find(c => c.code.toUpperCase() === code.toUpperCase());
  if (!coupon) {
    return res.status(404).json({ error: "Invalid coupon code" });
  }
  const expiryDate = new Date(coupon.expiry);
  if (expiryDate < new Date()) {
    return res.status(400).json({ error: "Coupon code has expired" });
  }
  if (coupon.used_count >= coupon.usage_limit) {
    return res.status(400).json({ error: "Coupon usage limit reached" });
  }
  res.json({ success: true, discount: coupon.discount });
});

// 5. Orders & Purchases
app.get("/api/orders", async (req, res) => {
  const db = await loadDB();
  res.json(db.orders);
});

// Optimized: Get a single user's orders with pre-joined products to save client load
app.get("/api/orders/user/:userId", async (req, res) => {
  const userId = parseInt(req.params.userId || "0");
  const db = await loadDB();
  
  const userOrders = (db.orders || []).filter(o => o.user_id === userId);
  
  // Fast cache map lookup
  const productMap = new Map();
  if (db.products) {
    db.products.forEach(p => productMap.set(p.id, p));
  }
  
  const joinedOrders = userOrders.map(o => {
    const p = productMap.get(o.product_id) || {};
    return {
      ...o,
      product_title: p.title || "Deleted Document Product Specs",
      product_image: p.image || "",
      product_price: p.price ?? 0
    };
  });
  
  res.json(joinedOrders);
});

// Optimized: Get a single order detail with pre-joined product details for receipt.php
app.get("/api/orders/detail/:orderId", async (req, res) => {
  const orderId = parseInt(req.params.orderId || "0");
  const db = await loadDB();
  const order = (db.orders || []).find(o => o.id === orderId);
  if (!order) {
    return res.status(404).json({ error: "Order details not found." });
  }
  
  const product = (db.products || []).find(p => p.id === order.product_id) || {};
  res.json({
    order,
    product: {
      id: product.id,
      title: product.title || "Deleted Document Product Specs",
      image: product.image || "",
      price: product.price ?? 0,
      mrp: product.mrp ?? 0,
      description: product.description || ""
    }
  });
});

// Optimized: Lightweight check for purchase status on product detail loading to protect memory
app.get("/api/orders/check-purchase/:userId/:productId", async (req, res) => {
  const userId = parseInt(req.params.userId || "0");
  const productId = parseInt(req.params.productId || "0");
  const db = await loadDB();
  
  const bought = (db.orders || []).some(o => 
    o.user_id === userId && 
    o.product_id === productId && 
    o.status === "successful"
  );
  
  res.json({ bought });
});

app.post("/api/orders/create", async (req, res) => {
  const { user_id, product_id, discountCode, referral } = req.body;
  const db = await loadDB();
  
  // Parse comma-separated IDs or numbers
  const productIds = typeof product_id === "string"
    ? product_id.split(",").map(id => parseInt(id.trim()))
    : (Array.isArray(product_id) ? product_id.map(id => parseInt(id)) : [parseInt(product_id)]);

  const products = db.products.filter(p => productIds.includes(p.id));
  if (products.length === 0) {
    return res.status(404).json({ error: "Product not found" });
  }

  // Create Razorpay Order simulator ID
  const razorpay_order_id = "order_rzp_mock_" + Math.random().toString(36).substring(2, 11);
  const createdOrders = [];
  const isBundle = products.length > 1;

  let baseId = db.orders.length > 0 ? Math.max(...db.orders.map(o => o.id)) + 1 : 10001;

  for (const product of products) {
    let finalPrice = product.price;
    if (isBundle) {
      // Apply 35% bundle discount (keep 65% of base price)
      finalPrice = Math.round(finalPrice * 0.65);
    }

    if (discountCode) {
      const coupon = db.coupons.find(c => c.code.toUpperCase() === discountCode.toUpperCase());
      if (coupon) {
        finalPrice = Math.max(0, finalPrice - (finalPrice * coupon.discount / 100));
      }
    }

    const newOrder = {
      id: baseId++,
      user_id: parseInt(user_id),
      product_id: product.id,
      amount: finalPrice,
      status: "pending",
      razorpay_order_id,
      referral: referral || null,
      created_at: new Date().toISOString()
    };

    db.orders.push(newOrder);
    createdOrders.push(newOrder);
  }

  await saveDB(db);
  res.json({ success: true, order: createdOrders[0], orders: createdOrders });
});

app.post("/api/orders/verify", async (req, res) => {
  const { razorpay_order_id, success } = req.body;
  const db = await loadDB();
  
  const matchingOrders = (db.orders || []).filter(o => o.razorpay_order_id === razorpay_order_id);
  if (matchingOrders.length === 0) {
    return res.status(404).json({ error: "Order session not found" });
  }

  for (const order of matchingOrders) {
    if (success) {
      order.status = "successful";
      // Send dynamic notification to user
      const uId = order.user_id;
      const p = db.products.find(prod => prod.id === order.product_id);
      const mockNotify = {
        id: db.notifications.length > 0 ? Math.max(...db.notifications.map(n => n.id)) + 1 : 1,
        user_id: uId,
        title: "Purchase Successful! 🎉",
        message: `You successfully unlocked "${p?.title}". Secure download and PDF invoice receipt have been sent to your email.`,
        is_read: 0,
        created_at: new Date().toISOString()
      };
      db.notifications.push(mockNotify);

      // Email PDF Receipt simulation log
      const user = db.users.find(u => u.id === uId);
      if (user && p) {
        if (!db.sent_emails) db.sent_emails = [];
        const receiptEmail = {
          id: db.sent_emails.length > 0 ? Math.max(...db.sent_emails.map(e => e.id)) + 1 : 1,
          user_id: user.id,
          email: user.email,
          subject: `Your Receipt & PDF Invoice for Order #${order.id} | DigitalMohan`,
          body: `Hi ${user.name},\n\nThank you for ordering "${p.title}" on DigitalMohan!\n\nYour layout sheets & files are now unlocked. You can download them instantly from your dashboard.\n\nYour PDF Invoice and printable receipt has been generated automatically:\nhttps://${req.get("host")}/receipt.php?id=${order.id}\n\nInvoice Details:\n- Order ID: #${order.id}\n- Product: ${p.title}\n- Amount Paid: ₹${order.amount}\n- Status: Paid / Successful\n\nKind regards,\nBilling Department, DigitalMohan`,
          created_at: new Date().toISOString()
        };
        db.sent_emails.push(receiptEmail);
      }

      // Process affiliate commission if referral exists
      if (order.referral) {
        if (!db.affiliates) db.affiliates = [];
        if (!db.affiliate_commissions) db.affiliate_commissions = [];
        
        const parsedRef = parseInt(order.referral);
        const affiliate = db.affiliates.find(a => 
           a.user_id === parsedRef || 
           a.code === order.referral || 
           (db.users.find(u => u.id === a.user_id && u.email.split('@')[0] === order.referral))
        );
        
        // Prevent self-referral and award only to active affiliates
        if (affiliate && affiliate.user_id !== order.user_id) {
          const commissionPercentage = 20; // 20% commission
          const commissionAmount = Math.round(order.amount * (commissionPercentage / 100));
          
          const newCommId = db.affiliate_commissions.length > 0 ? Math.max(...db.affiliate_commissions.map(c => c.id)) + 1 : 1;
          const newCommission = {
            id: newCommId,
            affiliate_id: affiliate.user_id,
            order_id: order.id,
            product_title: p?.title || "Digital Asset",
            order_amount: order.amount,
            amount: commissionAmount,
            percentage: commissionPercentage,
            status: "active",
            created_at: new Date().toISOString()
          };
          db.affiliate_commissions.push(newCommission);
          
          affiliate.balance = (affiliate.balance || 0) + commissionAmount;
          affiliate.total_earned = (affiliate.total_earned || 0) + commissionAmount;
          
          // Notify the affiliate
          const affNotifyId = db.notifications.length > 0 ? Math.max(...db.notifications.map(n => n.id)) + 1 : 1;
          const affiliateNotify = {
            id: affNotifyId,
            user_id: affiliate.user_id,
            title: "New Affiliate Sale Unlocked! 💰",
            message: `Congratulations! A customer purchased via your referral link. You earned ₹${commissionAmount} commission!`,
            is_read: 0,
            created_at: new Date().toISOString()
          };
          db.notifications.push(affiliateNotify);
        }
      }
    } else {
      order.status = "failed";
    }
  }

  await saveDB(db);
  res.json({ success: true, order: matchingOrders[0], orders: matchingOrders });
});

// Blogs API Endpoints
app.get("/api/blogs", async (req, res) => {
  const db = await loadDB();
  const search = req.query.search ? (req.query.search as string).toLowerCase().trim() : null;
  const category = req.query.category ? (req.query.category as string).toLowerCase().trim() : null;
  
  let blogs = db.blogs || [];
  
  if (category) {
    blogs = blogs.filter(b => b.category && b.category.toLowerCase() === category);
  }
  
  if (search) {
    blogs = blogs.filter(b => 
      (b.title && b.title.toLowerCase().includes(search)) || 
      (b.summary && b.summary.toLowerCase().includes(search)) ||
      (b.content && b.content.toLowerCase().includes(search))
    );
  }
  
  // Return blogs
  res.json(blogs);
});

app.get("/api/blogs/detail/:id", async (req, res) => {
  const db = await loadDB();
  const id = parseInt(req.params.id);
  const blog = (db.blogs || []).find(b => b.id === id);
  if (!blog) {
    return res.status(404).json({ error: "Blog not found" });
  }
  res.json(blog);
});

app.post("/api/blogs/create", async (req, res) => {
  const { title, summary, content, author, image, category, read_time } = req.body;
  const db = await loadDB();
  if (!db.blogs) db.blogs = [];
  
  const savedImage = await handleBase64Upload(image, "blog_image");
  const id = db.blogs.length > 0 ? Math.max(...db.blogs.map(b => b.id)) + 1 : 1;
  const newBlog = {
    id,
    title,
    summary,
    content,
    author: author || "Mohan Mahali",
    image: savedImage || "https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=800&q=80",
    category: category || "General",
    read_time: read_time || "5 min read",
    created_at: new Date().toISOString()
  };
  
  db.blogs.push(newBlog);
  await saveDB(db);
  res.json({ success: true, blog: newBlog });
});

app.post("/api/blogs/update", async (req, res) => {
  const { id, title, summary, content, author, image, category, read_time } = req.body;
  const db = await loadDB();
  if (!db.blogs) db.blogs = [];
  
  const index = db.blogs.findIndex(b => b.id === parseInt(id));
  if (index === -1) {
    return res.status(404).json({ error: "Blog not found" });
  }
  
  const savedImage = await handleBase64Upload(image, "blog_image");

  db.blogs[index] = {
    ...db.blogs[index],
    title,
    summary,
    content,
    author: author || db.blogs[index].author,
    image: savedImage || db.blogs[index].image,
    category: category || db.blogs[index].category,
    read_time: read_time || db.blogs[index].read_time
  };
  
  await saveDB(db);
  res.json({ success: true, blog: db.blogs[index] });
});

app.post("/api/blogs/delete", async (req, res) => {
  const { id } = req.body;
  const db = await loadDB();
  if (!db.blogs) db.blogs = [];
  
  db.blogs = db.blogs.filter(b => b.id !== parseInt(id));
  await saveDB(db);
  res.json({ success: true });
});

// 6. Settings
app.get("/api/settings", async (req, res) => {
  const db = await loadDB();
  res.json(db.settings);
});

app.get("/api/settings/admin-info", async (req, res) => {
  const db = await loadDB();
  if (db.admin && db.admin.length > 0) {
    res.json({ username: db.admin[0].username, password: db.admin[0].password });
  } else {
    res.json({ username: "admin", password: "password123" });
  }
});

app.post("/api/settings", async (req, res) => {
  const { app_name, razorpay_key, razorpay_secret, support_email, support_phone, theme_color, maintenance_mode, admin_username, admin_password, viral_popup_enabled, viral_popup_title, viral_popup_mrp, viral_popup_description, whatsapp_group_enabled, whatsapp_group_title, whatsapp_group_link, whatsapp_group_description, whatsapp_group_delay, whatsapp_group_autoclose } = req.body;
  const db = await loadDB();
  db.settings = {
    ...db.settings,
    app_name,
    razorpay_key,
    razorpay_secret,
    support_email,
    support_phone,
    theme_color,
    maintenance_mode: Number(maintenance_mode),
    viral_popup_enabled: viral_popup_enabled !== undefined ? Number(viral_popup_enabled) : db.settings.viral_popup_enabled,
    viral_popup_title: viral_popup_title !== undefined ? viral_popup_title : db.settings.viral_popup_title,
    viral_popup_mrp: viral_popup_mrp !== undefined ? Number(viral_popup_mrp) : db.settings.viral_popup_mrp,
    viral_popup_description: viral_popup_description !== undefined ? viral_popup_description : db.settings.viral_popup_description,
    whatsapp_group_enabled: whatsapp_group_enabled !== undefined ? Number(whatsapp_group_enabled) : db.settings.whatsapp_group_enabled,
    whatsapp_group_title: whatsapp_group_title !== undefined ? whatsapp_group_title : db.settings.whatsapp_group_title,
    whatsapp_group_link: whatsapp_group_link !== undefined ? whatsapp_group_link : db.settings.whatsapp_group_link,
    whatsapp_group_description: whatsapp_group_description !== undefined ? whatsapp_group_description : db.settings.whatsapp_group_description,
    whatsapp_group_delay: whatsapp_group_delay !== undefined ? Number(whatsapp_group_delay) : db.settings.whatsapp_group_delay,
    whatsapp_group_autoclose: whatsapp_group_autoclose !== undefined ? Number(whatsapp_group_autoclose) : db.settings.whatsapp_group_autoclose
  };
  
  if (admin_username) {
    if (!db.admin) db.admin = [];
    if (db.admin.length === 0) {
      db.admin.push({ id: 1, username: "admin", password: "password123" });
    }
    db.admin[0].username = admin_username;
  }
  if (admin_password) {
    if (!db.admin) db.admin = [];
    if (db.admin.length === 0) {
      db.admin.push({ id: 1, username: "admin", password: "password123" });
    }
    db.admin[0].password = admin_password;
  }

  await saveDB(db);
  res.json({ success: true, settings: db.settings });
});

app.post("/api/settings/update", async (req, res) => {
  const { app_name, razorpay_key, razorpay_secret, support_email, support_phone, theme_color, maintenance_mode, admin_username, admin_password, viral_popup_enabled, viral_popup_title, viral_popup_mrp, viral_popup_description, whatsapp_group_enabled, whatsapp_group_title, whatsapp_group_link, whatsapp_group_description, whatsapp_group_delay, whatsapp_group_autoclose } = req.body;
  const db = await loadDB();
  db.settings = {
    ...db.settings,
    app_name,
    razorpay_key,
    razorpay_secret,
    support_email,
    support_phone,
    theme_color,
    maintenance_mode: Number(maintenance_mode),
    viral_popup_enabled: viral_popup_enabled !== undefined ? Number(viral_popup_enabled) : db.settings.viral_popup_enabled,
    viral_popup_title: viral_popup_title !== undefined ? viral_popup_title : db.settings.viral_popup_title,
    viral_popup_mrp: viral_popup_mrp !== undefined ? Number(viral_popup_mrp) : db.settings.viral_popup_mrp,
    viral_popup_description: viral_popup_description !== undefined ? viral_popup_description : db.settings.viral_popup_description,
    whatsapp_group_enabled: whatsapp_group_enabled !== undefined ? Number(whatsapp_group_enabled) : db.settings.whatsapp_group_enabled,
    whatsapp_group_title: whatsapp_group_title !== undefined ? whatsapp_group_title : db.settings.whatsapp_group_title,
    whatsapp_group_link: whatsapp_group_link !== undefined ? whatsapp_group_link : db.settings.whatsapp_group_link,
    whatsapp_group_description: whatsapp_group_description !== undefined ? whatsapp_group_description : db.settings.whatsapp_group_description,
    whatsapp_group_delay: whatsapp_group_delay !== undefined ? Number(whatsapp_group_delay) : db.settings.whatsapp_group_delay,
    whatsapp_group_autoclose: whatsapp_group_autoclose !== undefined ? Number(whatsapp_group_autoclose) : db.settings.whatsapp_group_autoclose
  };

  if (admin_username) {
    if (!db.admin) db.admin = [];
    if (db.admin.length === 0) {
      db.admin.push({ id: 1, username: "admin", password: "password123" });
    }
    db.admin[0].username = admin_username;
  }
  if (admin_password) {
    if (!db.admin) db.admin = [];
    if (db.admin.length === 0) {
      db.admin.push({ id: 1, username: "admin", password: "password123" });
    }
    db.admin[0].password = admin_password;
  }

  await saveDB(db);
  res.json({ success: true, settings: db.settings });
});

// 6.5 Banners Management
app.get("/api/banners", async (req, res) => {
  const db = await loadDB();
  if (!db.banners) {
    db.banners = [
      {
        id: 1,
        badge: "HOT SALE",
        title: "All Prompt Packs of Chat GPT",
        subtitle: "Boost production by 10x instantly. 100% Tested copy-paste directories.",
        link_url: "products.php?cat=1",
        bg_gradient: "from-indigo-900 to-sky-900"
      },
      {
        id: 2,
        badge: "NEW RELEASE",
        title: "ATS-Friendly Resumes",
        subtitle: "Pass screening tests. Recruiter-approved formatting sheets.",
        link_url: "products.php?cat=2",
        bg_gradient: "from-emerald-950 to-teal-850"
      }
    ];
    await saveDB(db);
  }
  res.json(db.banners);
});

app.post("/api/banners", async (req, res) => {
  const { badge, title, subtitle, link_url, bg_gradient } = req.body;
  const db = await loadDB();
  if (!db.banners) db.banners = [];
  const newId = db.banners.length > 0 ? Math.max(...db.banners.map(b => b.id)) + 1 : 1;
  const newB = {
    id: newId,
    badge: badge || "PROMO",
    title: title || "",
    subtitle: subtitle || "",
    link_url: link_url || "products.php",
    bg_gradient: bg_gradient || "from-slate-900 to-indigo-900"
  };
  db.banners.push(newB);
  await saveDB(db);
  res.json({ success: true, banner: newB });
});

app.post("/api/banners/create", async (req, res) => {
  const { badge, title, subtitle, link_url, bg_gradient } = req.body;
  const db = await loadDB();
  if (!db.banners) db.banners = [];
  const newId = db.banners.length > 0 ? Math.max(...db.banners.map(b => b.id)) + 1 : 1;
  const newB = {
    id: newId,
    badge: badge || "PROMO",
    title: title || "",
    subtitle: subtitle || "",
    link_url: link_url || "products.php",
    bg_gradient: bg_gradient || "from-slate-900 to-indigo-900"
  };
  db.banners.push(newB);
  await saveDB(db);
  res.json({ success: true, banner: newB });
});

app.post("/api/banners/update", async (req, res) => {
  const { id, badge, title, subtitle, link_url, bg_gradient } = req.body;
  const db = await loadDB();
  if (!db.banners) db.banners = [];
  const b = db.banners.find(item => item.id === parseInt(id));
  if (b) {
    if (badge !== undefined) b.badge = badge;
    if (title !== undefined) b.title = title;
    if (subtitle !== undefined) b.subtitle = subtitle;
    if (link_url !== undefined) b.link_url = link_url;
    if (bg_gradient !== undefined) b.bg_gradient = bg_gradient;
    await saveDB(db);
    res.json({ success: true, banner: b });
  } else {
    res.status(404).json({ error: "Banner not found" });
  }
});

app.post("/api/banners/delete", async (req, res) => {
  const { id } = req.body;
  const db = await loadDB();
  if (!db.banners) db.banners = [];
  db.banners = db.banners.filter(b => b.id !== parseInt(id));
  await saveDB(db);
  res.json({ success: true });
});

// 7. Wishlist
app.get("/api/wishlist/:userId", async (req, res) => {
  const userId = parseInt(req.params.userId);
  const db = await loadDB();
  const userWishlist = db.wishlist.filter(w => w.user_id === userId);
  
  // Fast map lookup
  const productMap = new Map();
  if (db.products) {
    db.products.forEach(p => productMap.set(p.id, p));
  }
  
  const joinedWishlist = userWishlist.map(w => {
    const p = productMap.get(w.product_id) || {};
    return {
      ...w,
      product_title: p.title || "Deleted Product Specs",
      product_image: p.image || "",
      product_mrp: p.mrp || 0,
      product_price: p.price || 0
    };
  });
  
  res.json(joinedWishlist);
});

app.post("/api/wishlist/toggle", async (req, res) => {
  const { user_id, product_id } = req.body;
  const db = await loadDB();
  const uId = parseInt(user_id);
  const pId = parseInt(product_id);
  const index = db.wishlist.findIndex(w => w.user_id === uId && w.product_id === pId);
  if (index !== -1) {
    db.wishlist.splice(index, 1);
    await saveDB(db);
    res.json({ success: true, state: "removed" });
  } else {
    db.wishlist.push({
      id: db.wishlist.length > 0 ? Math.max(...db.wishlist.map(w => w.id)) + 1 : 1,
      user_id: uId,
      product_id: pId,
      created_at: new Date().toISOString()
    });
    await saveDB(db);
    res.json({ success: true, state: "added" });
  }
});

// 8. Reviews
app.get("/api/reviews/:productId", async (req, res) => {
  const productId = parseInt(req.params.productId);
  const db = await loadDB();
  const filtered = db.reviews.filter(r => r.product_id === productId).map(r => {
    const user = db.users.find(u => u.id === r.user_id);
    return {
      ...r,
      user_name: user ? user.name : "Anonymous Buyer"
    };
  });
  res.json(filtered);
});

app.post("/api/reviews", async (req, res) => {
  const { user_id, product_id, rating, comment } = req.body;
  const db = await loadDB();
  const newReview = {
    id: db.reviews.length > 0 ? Math.max(...db.reviews.map(r => r.id)) + 1 : 1,
    user_id: parseInt(user_id),
    product_id: parseInt(product_id),
    rating: Number(rating),
    comment,
    created_at: new Date().toISOString()
  };
  db.reviews.push(newReview);
  await saveDB(db);
  res.json({ success: true, review: newReview });
});

// 9. Downloads logs & tokens
app.post("/api/downloads/log", async (req, res) => {
  const { user_id, product_id, ip_address, user_agent } = req.body;
  const db = await loadDB();
  const log = {
    id: db.download_logs.length > 0 ? Math.max(...db.download_logs.map(l => l.id)) + 1 : 1,
    user_id: parseInt(user_id),
    product_id: parseInt(product_id),
    ip_address: ip_address || "127.0.0.1",
    user_agent: user_agent || "Browser Simulator",
    downloaded_at: new Date().toISOString()
  };
  db.download_logs.push(log);
  await saveDB(db);
  res.json({ success: true });
});

app.get("/api/downloads/logs", async (req, res) => {
  const db = await loadDB();
  res.json(db.download_logs);
});

app.get("/api/downloads/file", async (req, res) => {
  const pId = parseInt(req.query.id as string || "0");
  const db = await loadDB();
  let product = db.products.find(p => p.id === pId);
  if (pId === 999) {
    product = {
      id: 999,
      category_id: 1,
      title: db.settings.viral_popup_title || "Free Premium Ebook Secrets",
      mrp: Number(db.settings.viral_popup_mrp || 999),
      price: 0,
      description: db.settings.viral_popup_description || "Free Premium Ebook Secrets Unlock successfully!",
      file: "Free_Premium_Ebook_Secrets.pdf",
      image: "",
      status: "active",
      created_at: new Date().toISOString(),
      preview_url: ""
    };
  }
  if (!product) {
    return res.status(404).send("Product not found");
  }

  const filename = product.file || "digital_product.zip";
  
  let downloadName = "digital_product.zip";
  let downloadUrl = "";
  if (filename.includes('|')) {
    const parts = filename.split('|');
    if (parts[0].startsWith('data:')) {
      downloadUrl = parts[0];
      downloadName = parts[1];
    } else if (parts[1].startsWith('data:')) {
      downloadUrl = parts[1];
      downloadName = parts[0];
    } else {
      downloadName = parts[1];
    }
  } else if (filename.startsWith('data:')) {
    downloadUrl = filename;
    downloadName = "digital_product.zip";
  } else {
    downloadName = filename;
  }

  const ext = downloadName.split('.').pop()?.toLowerCase() || '';

  let contentType = 'application/octet-stream';
  if (ext === 'pdf') {
    contentType = 'application/pdf';
  } else if (ext === 'xlsx') {
    contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
  } else if (ext === 'docx') {
    contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
  } else if (ext === 'zip') {
    contentType = 'application/zip';
  }

  res.setHeader('Content-Type', contentType);
  res.setHeader('Content-Disposition', `attachment; filename="${downloadName}"`);
  
  if (downloadUrl.startsWith('data:')) {
    const commaPos = downloadUrl.indexOf(',');
    if (commaPos !== -1) {
      const base64Data = downloadUrl.substring(commaPos + 1);
      return res.send(Buffer.from(base64Data, "base64"));
    }
  }

  let base64Data = "UEsFBgAAAAAAAAAAAAAAAAAAAAAAAA=="; // default empty zip
  if (ext === 'pdf') {
    base64Data = "JVBERi0xLjEKMSAwIG9iajw8L1R5cGUvQ2F0YWxvZy9QYWdlcyAyIDAgUj4+ZW5kb2JqMiAwIG9iajw8L1R5cGUvUGFnZXMvS2lkc1szIDAgUl0vQ291bnQgMT4+ZW5kb2JqMyAwIG9iajw8L1R5cGUvUGFnZS9QYXJlbnQgMiAwIFIvTWVkaWFCb3hbMCAwIDU5NSA4NDJdL0NvbnRlbnRzIDQgMCBSPj5lbmRvYmo0IDAgb2JqPDwvTGVuZ3RoIDU5Pj5zdHJlYW0KQlQgL0YxIDEyIFRmIDcwIDcwMCBUZCAoRGlnaXRhbE1vaGFuIERvY3VtZW50IERvd25sb2FkKSBUaiBFVAplbmRzdHJlYW0lJUVPRg==";
  }
  res.send(Buffer.from(base64Data, "base64"));
});

// Payout summary details
app.get("/api/payouts/summary", async (req, res) => {
  const db = await loadDB();
  const successfulOrders = db.orders.filter(o => o.status === "successful");
  const totalRevenue = successfulOrders.reduce((sum, o) => sum + o.amount, 0);
  const razorpaySettled = totalRevenue * 0.98; // simulated 2% Gateway deduction
  res.json({
    revenue: totalRevenue,
    settlement: razorpaySettled,
    transactions: successfulOrders.length
  });
});

// --- AFFILIATE SYSTEM API ENDPOINTS ---

// 1. Log affiliate link clicks
app.post("/api/affiliate/click", async (req, res) => {
  const { ref } = req.body;
  if (!ref) return res.status(400).json({ error: "Referral code required" });
  
  const db = await loadDB();
  if (!db.affiliates) db.affiliates = [];
  
  const parsedRef = parseInt(ref);
  const affiliate = db.affiliates.find(a => 
    a.user_id === parsedRef || 
    a.code === ref ||
    (db.users.find(u => u.id === a.user_id && u.email.split('@')[0] === ref))
  );
  
  if (affiliate) {
    affiliate.clicks = (affiliate.clicks || 0) + 1;
    await saveDB(db);
    return res.json({ success: true, clicks: affiliate.clicks });
  }
  
  res.json({ success: false, message: "No active affiliate linked under this code" });
});

// 2. Register/Join as an affiliate
app.post("/api/affiliate/join", async (req, res) => {
  const { user_id, upi_id, name, email, phone } = req.body;
  if (!user_id) return res.status(400).json({ error: "User ID required" });
  
  const db = await loadDB();
  if (!db.users) db.users = [];
  if (!db.affiliates) db.affiliates = [];
  
  const uId = parseInt(user_id);
  let user = db.users.find(u => u.id === uId);
  
  if (!user && email) {
    const emailUser = db.users.find(u => u.email.toLowerCase() === email.toLowerCase());
    if (emailUser) {
      user = emailUser;
    }
  }

  if (!user && email) {
    user = {
      id: uId,
      name: name || "Affiliate Partner",
      email: email,
      phone: phone || "",
      password: "password123",
      status: "active",
      created_at: new Date().toISOString()
    };
    db.users.push(user);
    await saveDB(db);
  }

  let affiliate = db.affiliates.find(a => a.user_id === uId);
  
  if (affiliate) {
    if (upi_id) {
      affiliate.uupi = upi_id;
      await saveDB(db);
    }
    return res.json({ success: true, message: "Already an affiliate partner", affiliate });
  }
  
  if (!user) return res.status(404).json({ error: "User not found" });
  
  const affCode = user.email.split('@')[0].toLowerCase() + "_" + uId;
  const newAffiliate = {
    id: db.affiliates.length > 0 ? Math.max(...db.affiliates.map(a => a.id)) + 1 : 1,
    user_id: uId,
    code: affCode,
    balance: 0,
    total_earned: 0,
    total_withdrawn: 0,
    clicks: 1, // Start with 1 setup click
    uupi: upi_id || "",
    status: "active",
    created_at: new Date().toISOString()
  };
  
  db.affiliates.push(newAffiliate);
  
  // Send congratulations notification
  const affNotifyId = db.notifications.length > 0 ? Math.max(...db.notifications.map(n => n.id)) + 1 : 1;
  db.notifications.push({
    id: affNotifyId,
    user_id: uId,
    title: "Affiliate Activated! 🤝",
    message: `Congratulations! Your brand affiliate dashboard is ready. Share links to earn 20% commission on every successful template lock order sale!`,
    is_read: 0,
    created_at: new Date().toISOString()
  });
  
  await saveDB(db);
  res.json({ success: true, affiliate: newAffiliate });
});

// 3. User stats, commission list, and payout orders
app.get("/api/affiliate/stats/:userId", async (req, res) => {
  const userId = parseInt(req.params.userId);
  const db = await loadDB();
  
  if (!db.affiliates) db.affiliates = [];
  if (!db.affiliate_commissions) db.affiliate_commissions = [];
  if (!db.affiliate_payouts) db.affiliate_payouts = [];
  
  const affiliate = db.affiliates.find(a => a.user_id === userId);
  if (!affiliate) {
    return res.json({ active: false });
  }
  
  const commissions = db.affiliate_commissions.filter(c => c.affiliate_id === userId);
  const payouts = db.affiliate_payouts.filter(p => p.affiliate_id === userId);
  
  res.json({
    active: true,
    affiliate,
    commissions,
    payouts
  });
});

// 4. Request Payout
app.post("/api/affiliate/payout/request", async (req, res) => {
  const { user_id, amount, payment_method, details } = req.body;
  if (!user_id || !amount) return res.status(400).json({ error: "User ID and amount required" });
  
  const db = await loadDB();
  if (!db.affiliates) db.affiliates = [];
  if (!db.affiliate_payouts) db.affiliate_payouts = [];
  
  const uId = parseInt(user_id);
  const affiliate = db.affiliates.find(a => a.user_id === uId);
  if (!affiliate) return res.status(404).json({ error: "Affiliate account not activated" });
  
  const payoutAmount = parseInt(amount);
  if (payoutAmount <= 0) return res.status(400).json({ error: "Invalid payout amount" });
  if (affiliate.balance < payoutAmount) return res.status(400).json({ error: "Insufficient available balance" });
  
  // Book payout
  const newPayoutId = db.affiliate_payouts.length > 0 ? Math.max(...db.affiliate_payouts.map(p => p.id)) + 1 : 1;
  const newPayout = {
    id: newPayoutId,
    affiliate_id: uId,
    amount: payoutAmount,
    payment_method: payment_method || "UPI",
    details: details || affiliate.uupi,
    status: "pending",
    created_at: new Date().toISOString()
  };
  
  db.affiliate_payouts.push(newPayout);
  
  // Deduct from affiliate balance immediately
  affiliate.balance -= payoutAmount;
  
  await saveDB(db);
  res.json({ success: true, payout: newPayout, current_balance: affiliate.balance });
});

// 5. Admin: Get all affiliates
app.get("/api/admin/affiliates/all", async (req, res) => {
  const db = await loadDB();
  if (!db.affiliates) db.affiliates = [];
  
  // Add user name/email to results
  const list = db.affiliates.map(a => {
    const u = db.users.find(user => user.id === a.user_id);
    return {
      ...a,
      user_name: u ? u.name : "Unknown Partner",
      user_email: u ? u.email : "deleted@user.com"
    };
  });
  
  res.json(list);
});

// 6. Admin: Get all payouts
app.get("/api/admin/affiliates/payouts", async (req, res) => {
  const db = await loadDB();
  if (!db.affiliate_payouts) db.affiliate_payouts = [];
  
  const list = db.affiliate_payouts.map(p => {
    const u = db.users.find(user => user.id === p.affiliate_id);
    return {
      ...p,
      user_name: u ? u.name : "Unknown Partner",
      user_email: u ? u.email : "deleted@user.com"
    };
  });
  
  res.json(list);
});

// 7. Admin: Update payout status
app.post("/api/admin/affiliates/payouts/process", async (req, res) => {
  const { payout_id, status } = req.body; // status: "completed" or "rejected"
  if (!payout_id || !status) return res.status(400).json({ error: "Payout ID and status required" });
  
  const db = await loadDB();
  if (!db.affiliate_payouts) db.affiliate_payouts = [];
  if (!db.affiliates) db.affiliates = [];
  
  const payout = db.affiliate_payouts.find(p => p.id === parseInt(payout_id));
  if (!payout) return res.status(404).json({ error: "Payout request not found" });
  
  if (payout.status !== "pending") {
    return res.status(400).json({ error: "Payout has already been processed" });
  }
  
  const affiliate = db.affiliates.find(a => a.user_id === payout.affiliate_id);
  
  if (status === "completed") {
    payout.status = "completed";
    if (affiliate) {
      affiliate.total_withdrawn = (affiliate.total_withdrawn || 0) + payout.amount;
    }
    
    // Notify user
    const pNotifyId = db.notifications.length > 0 ? Math.max(...db.notifications.map(n => n.id)) + 1 : 1;
    db.notifications.push({
      id: pNotifyId,
      user_id: payout.affiliate_id,
      title: "Payout Disbursed! 💳",
      message: `Your requested payout of ₹${payout.amount} has been successfully processed and transferred to your bank coordinates.`,
      is_read: 0,
      created_at: new Date().toISOString()
    });
  } else if (status === "rejected") {
    payout.status = "rejected";
    // Refund balance to affiliate
    if (affiliate) {
      affiliate.balance += payout.amount;
    }
    
    // Notify user
    const pNotifyId = db.notifications.length > 0 ? Math.max(...db.notifications.map(n => n.id)) + 1 : 1;
    db.notifications.push({
      id: pNotifyId,
      user_id: payout.affiliate_id,
      title: "Payout Request Declined ❌",
      message: `Your requested payout of ₹${payout.amount} was returned to your balance. Please check your UPI / banking details.`,
      is_read: 0,
      created_at: new Date().toISOString()
    });
  }
  
  await saveDB(db);
  res.json({ success: true, payout });
});

// 10. Recently viewed
app.get("/api/recently-viewed/:userId", async (req, res) => {
  const userId = parseInt(req.params.userId);
  const db = await loadDB();
  const list = db.recently_viewed.filter(rv => rv.user_id === userId);
  res.json(list);
});

app.post("/api/recently-viewed", async (req, res) => {
  const { user_id, product_id } = req.body;
  const db = await loadDB();
  const uId = parseInt(user_id);
  const pId = parseInt(product_id);
  const index = db.recently_viewed.findIndex(rv => rv.user_id === uId && rv.product_id === pId);
  if (index !== -1) {
    db.recently_viewed.splice(index, 1);
  }
  db.recently_viewed.unshift({
    id: db.recently_viewed.length > 0 ? Math.max(...db.recently_viewed.map(rv => rv.id)) + 1 : 1,
    user_id: uId,
    product_id: pId,
    viewed_at: new Date().toISOString()
  });
  if (db.recently_viewed.length > 20) {
    db.recently_viewed.pop();
  }
  await saveDB(db);
  res.json({ success: true });
});

// 11. Notifications
app.get("/api/notifications/:userId", async (req, res) => {
  const userId = parseInt(req.params.userId || "0");
  const db = await loadDB();
  const list = db.notifications.filter(n => n.user_id === userId || n.user_id === 0);
  res.json(list);
});

app.post("/api/notifications/read", async (req, res) => {
  const { userId } = req.body;
  const db = await loadDB();
  db.notifications.forEach(n => {
    if (n.user_id === userId) {
      n.is_read = 1;
    }
  });
  await saveDB(db);
  res.json({ success: true });
});

// Bulk notification creation via Admin
app.post("/api/notifications/bulk", async (req, res) => {
  const { user_id, title, message } = req.body;
  const db = await loadDB();
  const targetUsers = user_id === "all" ? db.users : db.users.filter(u => u.id === parseInt(user_id));
  
  let nextNotificationIdBulk = db.notifications.length > 0 ? Math.max(...db.notifications.map(n => n.id)) + 1 : 1;
  targetUsers.forEach(u => {
    db.notifications.push({
      id: nextNotificationIdBulk++,
      user_id: u.id,
      title,
      message,
      is_read: 0,
      created_at: new Date().toISOString()
    });
  });
  if (user_id === "all") {
    db.notifications.push({
      id: nextNotificationIdBulk++,
      user_id: 0,
      title,
      message,
      is_read: 0,
      created_at: new Date().toISOString()
    });
  }
  await saveDB(db);
  res.json({ success: true });
});

app.post("/api/notifications/create", async (req, res) => {
  const { userId, title, message } = req.body;
  const db = await loadDB();
  const targetId = parseInt(userId);
  db.notifications.push({
    id: db.notifications.length > 0 ? Math.max(...db.notifications.map(n => n.id)) + 1 : 1,
    user_id: targetId,
    title,
    message,
    is_read: 0,
    created_at: new Date().toISOString()
  });
  await saveDB(db);
  res.json({ success: true });
});

// 12. Support Queries / Customer Message Board
app.post("/api/support/query", async (req, res) => {
  const { name, email, subject, message } = req.body;
  const db = await loadDB();
  if (!db.queries) db.queries = [];
  const newQuery = {
    id: db.queries.length > 0 ? Math.max(...db.queries.map(q => q.id)) + 1 : 1,
    name,
    email,
    subject,
    message,
    reply_text: "",
    is_responded: false,
    created_at: new Date().toISOString()
  };
  db.queries.push(newQuery);
  await saveDB(db);
  res.json({ success: true, query: newQuery });
});

app.get("/api/support/queries", async (req, res) => {
  const db = await loadDB();
  if (!db.queries) db.queries = [];
  res.json(db.queries);
});

app.post("/api/support/reply", async (req, res) => {
  const { id, reply_text } = req.body;
  const db = await loadDB();
  if (!db.queries) db.queries = [];
  const queryObj = db.queries.find(q => q.id === parseInt(id));
  if (queryObj) {
    queryObj.reply_text = reply_text;
    queryObj.is_responded = true;
    
    // Send simulated email response
    if (!db.sent_emails) db.sent_emails = [];
    db.sent_emails.push({
      id: db.sent_emails.length > 0 ? Math.max(...db.sent_emails.map(e => e.id)) + 1 : 1,
      user_id: 0, // Admin response
      email: queryObj.email,
      subject: `RE: ${queryObj.subject} | DigitalMohan Help Desk`,
      body: `Hi ${queryObj.name},\n\nOur support team has reviewed your query:\n"${queryObj.message}"\n\nResponse:\n${reply_text}\n\nHope this helps! Let us know if you have further concerns.\n\nWarm regards,\nDigitalMohan Customer Relations`,
      created_at: new Date().toISOString()
    });
    
    await saveDB(db);
    res.json({ success: true, query: queryObj });
  } else {
    res.status(404).json({ error: "Query not found" });
  }
});

// 13. Email Newsletters & Subscribers
app.post("/api/newsletter/subscribe", async (req, res) => {
  const { email } = req.body;
  const db = await loadDB();
  if (!db.subscribers) db.subscribers = [];
  const exists = db.subscribers.find(s => s.email.toLowerCase() === email.toLowerCase());
  if (exists) {
    return res.json({ success: true, message: "You are already a newsletter subscriber!" });
  }
  const newSub = {
    id: db.subscribers.length > 0 ? Math.max(...db.subscribers.map(s => s.id)) + 1 : 1,
    email,
    subscribed_at: new Date().toISOString()
  };
  db.subscribers.push(newSub);
  
  // Log confirmation simulated email
  if (!db.sent_emails) db.sent_emails = [];
  db.sent_emails.push({
    id: db.sent_emails.length > 0 ? Math.max(...db.sent_emails.map(e => e.id)) + 1 : 1,
    user_id: 0,
    email,
    subject: `Welcome to DigitalMohan Newsletters! 🎁`,
    body: `Hi there,\n\nWe're thrilled to have you subscribe to our launches & promotional deals center!\n\nYou'll be the first to know when a new Canva design, prompt directory, or revising JEE formulae is added.\n\nAs a welcome offer, use code SAVE50 to get 50% discount on any purchase!\n\nDigitalMohan Marketing Team`,
    created_at: new Date().toISOString()
  });

  await saveDB(db);
  res.json({ success: true, message: "Subscribed successfully! Check email logs for welcome offer." });
});

app.get("/api/newsletter/subscribers", async (req, res) => {
  const db = await loadDB();
  if (!db.subscribers) db.subscribers = [];
  res.json(db.subscribers);
});

app.post("/api/newsletter/send-campaign", async (req, res) => {
  const { subject, body } = req.body;
  const db = await loadDB();
  if (!db.subscribers) db.subscribers = [];
  if (!db.campaigns) db.campaigns = [];
  if (!db.sent_emails) db.sent_emails = [];

  const newCampaign = {
    id: db.campaigns.length > 0 ? Math.max(...db.campaigns.map(c => c.id)) + 1 : 1,
    subject,
    body,
    sent_at: new Date().toISOString()
  };
  db.campaigns.push(newCampaign);

  // Broadcast automated emails & mock alerts to all subscribers (optimized)
  let nextEmailId = db.sent_emails.length > 0 ? Math.max(...db.sent_emails.map(e => e.id)) + 1 : 1;
  db.subscribers.forEach(sub => {
    db.sent_emails.push({
      id: nextEmailId++,
      user_id: 0,
      email: sub.email,
      subject,
      body,
      created_at: new Date().toISOString()
    });
  });

  // Also push simulated notifications to registered portal users
  let nextCampaignNotifyId = db.notifications.length > 0 ? Math.max(...db.notifications.map(n => n.id)) + 1 : 1;
  db.users.forEach(u => {
    db.notifications.push({
      id: nextCampaignNotifyId++,
      user_id: u.id,
      title: `📢 New Campaign: ${subject}`,
      message: `We've dispatched an email newsletter with exclusive offers! Check your subscription logs.`,
      is_read: 0,
      created_at: new Date().toISOString()
    });
  });

  await saveDB(db);
  res.json({ success: true, sent_count: db.subscribers.length });
});

// 13.b AI Product Wizard Writer Endpoint
app.post("/api/admin/generate-product-copy", async (req, res) => {
  const { topic, category } = req.body;
  try {
    if (!aiClient) {
      // High-quality mock defaults is extremely smart for local testing when key is absent
      const title = `${topic || "Premium Digital Product"} - Master Guide Pack`;
      return res.json({
        title,
        mrp: 999,
        price: 199,
        description: `Unlock immediate, full career acceleration with this beautifully structured "${topic || "premium digital bundle"}". Built on recruiter standards with fully responsive layout guidelines, tested folders, and clean copy-paste templates to save over 100+ working hours. Download instantly after safe checkout.`,
        file: `${(topic || "digital_product").toLowerCase().replace(/[^a-z0-9]/g, "_")}_premium_bundle.zip`,
        simulated: true
      });
    }

    const promptText = `Write a high-converting digital product catalog entry for:
    Topic/Keyword: "${topic}"
    Category Name: "${category || "General Digital Products"}"

    Respond strictly with valid JSON only, using this exact format without markdown envelopes:
    {
      "title": "A super catchy title under 65 chars",
      "mrp": ${Math.floor(Math.random() * 500) + 400},
      "price": ${Math.floor(Math.random() * 150) + 99},
      "description": "A beautiful 2-3 sentence high-converting marketing description that sells the document instantly",
      "file": "a_clean_seo_zip_file_name.zip"
    }`;

    const response = await aiClient.models.generateContent({
      model: "gemini-3.5-flash",
      contents: promptText,
      config: {
        temperature: 0.7,
        responseMimeType: "application/json"
      }
    });

    const parsedJson = JSON.parse(response.text.replace(/```json|```/gi, "").trim());
    res.json({ ...parsedJson, simulated: false });
  } catch (err: any) {
    console.error("AI Product Copy Generator error:", err.message);
    res.json({
      title: `${topic || "Digital Master Pack"}`,
      mrp: 499,
      price: 99,
      description: `Unlock immediate, full career acceleration with this beautifully structured "${topic || "premium digital bundle"}". Built on developer standards, tested files, and clean templates to save over 100+ working hours. Download instantly.`,
      file: "premium_template_download.zip",
      simulated: true
    });
  }
});

// 13.c AI Blog Guide Writer Endpoint using Gemini
app.post("/api/admin/generate-blog-assistant", async (req, res) => {
  const { topic, category, author } = req.body;
  const safeAuthor = author || "Mohan Mahali";
  try {
    if (!aiClient) {
      // High-quality mock defaults for local/offline developer simulation
      return res.json({
        title: `The Ultimate Guide to ${topic || "Advanced Knowledge"}`,
        summary: `Master ${topic || "advanced configurations"} with this battle-tested blueprint & cheat sheets compiled by senior subject matter pros.`,
        content: `# The Ultimate Guide to ${topic || "Advanced Knowledge"}\n\nWelcome to the pro masterclass edition. Achieving 10x output speed requires optimizing every configuration value. Here's how to structure it:\n\n### 1. The Core Infrastructure\nAlways keep modules modular instead of pasting all files into one. This avoids massive build errors and secures easy linting properties.\n\n### 2. Concrete Code Pattern\n\`\`\`typescript\n// Optimizing local performance caches\nconst localCache = new Map<string, any>();\nexport function getCacheItem(key: string) {\n  return localCache.get(key);\n}\n\`\`\`\n\n### 3. Progressive Implementation Checklist\n- Remove all unnecessary external scripts immediately.\n- Implement local asynchronous loading for large image bundles.`,
        read_time: "5 min read",
        simulated: true
      });
    }

    const promptText = `Generate a highly professional, ready-to-publish educational blog post/tutorial on the following topic:
    Topic/Keyword: "${topic}"
    Category Name: "${category || "General"}"
    Author Name: "${safeAuthor}"

    Respond strictly with valid JSON only, using this exact format without markdown envelopes:
    {
      "title": "A highly premium catchy article title under 65 chars",
      "summary": "A highly engaging 1-sentence abstract summary under 140 chars that fits perfectly in a dense grid card",
      "content": "A detailed, robust, comprehensive article guide body written in markdown. Use '#', '###', '####', and bullet lists. Write at least 4-5 long, professional paragraphs, including helpful explanations and mock code examples or actionable frameworks if relevant.",
      "read_time": "5 min read"
    }`;

    const response = await aiClient.models.generateContent({
      model: "gemini-3.5-flash",
      contents: promptText,
      config: {
        temperature: 0.8,
        responseMimeType: "application/json"
      }
    });

    const parsedJson = JSON.parse(response.text.replace(/```json|```/gi, "").trim());
    res.json({ ...parsedJson, simulated: false });
  } catch (err: any) {
    console.error("AI Blog Copy Generator error:", err.message);
    res.json({
      title: `The Ultimate Guide to ${topic || "Advanced Systems"}`,
      summary: `Master ${topic || "actionable configurations"} with this battle-tested blueprint & cheat sheets compiled by senior subject matter pros.`,
      content: `# The Ultimate Guide to ${topic || "Advanced Systems"}\n\nWelcome to the pro masterclass edition. Achieving 10x output speed requires optimizing every configuration value. Here's how to structure it:\n\n### 1. The Core Infrastructure\nAlways keep modules modular instead of pasting all files into one. This avoids massive build errors and secures easy linting properties.`,
      read_time: "5 min read",
      simulated: true
    });
  }
});

// 14. Interactive Gemini AI Client & Support Chatbot
app.post("/api/support-chat", async (req, res) => {
  const { message, history } = req.body;
  try {
    if (!aiClient) {
      return res.json({ response: "AI Support Chat is running in simulation mode. How can we help you today with your DigitalMohan products, receipts, or refund regulations? (Tip: Set GEMINI_API_KEY in Secrets for live AI responses!)" });
    }

    const systemInstruction = `You are "DigitalMohan Support Agent AI", a smart customer support assistant for DigitalMohan. 
    DigitalMohan is a premium digital product hub selling ChatGPT Prompt directories, Resume templates, study notes, and Canva layouts.
    Answer customer questions with clarity:
    - Order access: Files are unlocked instantly upon payment. View PDF receipt or find downloads in Profile > Saved Library.
    - Refund Policy: 7-day refund guarantee if files fail to download or are corrupted. Replacement is instant.
    - Payments: Secure simulated Razorpay gate channel.
    Be helpful and extremely concise. Respond in simple formatted markdown (lists/bold text are fine, keep spacing tight).`;

    const contents = [];
    if (history && Array.isArray(history)) {
      contents.push(...history.map(item => ({
        role: item.role === "user" ? "user" : "model",
        parts: [{ text: item.text }]
      })));
    }
    contents.push({ role: "user", parts: [{ text: message }] });

    const response = await aiClient.models.generateContent({
      model: "gemini-3.5-flash",
      contents: contents as any,
      config: {
        systemInstruction,
        temperature: 0.6
      }
    });

    res.json({ response: response.text || "I apologize, my prompt processing is congested. You can double check our FAQ accordions or start a WhatsApp chat." });
  } catch (err: any) {
    console.error("Gemini support chat error:", err.message);
    res.json({ response: "Hello! Our live chat lines are experiencing high volume. You can submit a query ticket above or use instant WhatsApp connect!" });
  }
});

// 15. Abandoned Wishlist/Cart Automated Trigger 
app.get("/api/wishlist/check-abandoned/:userId", async (req, res) => {
  const userId = parseInt(req.params.userId);
  const db = await loadDB();
  
  if (!userId) {
    return res.json({ has_abandoned: false });
  }

  const user = db.users.find(u => u.id === userId);
  if (!user) {
    return res.json({ has_abandoned: false });
  }

  // Find wishlist items for this user 
  const userWish = db.wishlist.filter(w => w.user_id === userId);
  if (userWish.length === 0) {
    return res.json({ has_abandoned: false });
  }

  // Find products that have NOT been ordered successfully 
  const orderedProductIds = db.orders
    .filter(o => o.user_id === userId && o.status === "successful")
    .map(o => o.product_id);

  const abandonedWish = userWish.filter(w => !orderedProductIds.includes(w.product_id));
  if (abandonedWish.length === 0) {
    return res.json({ has_abandoned: false });
  }

  // Pick the most recent abandoned item
  const recentAbandon = abandonedWish[abandonedWish.length - 1];
  const prod = db.products.find(p => p.id === recentAbandon.product_id);

  if (!prod) {
    return res.json({ has_abandoned: false });
  }

  // Trigger simulated automatic newsletter/reminder email if not already done recently 
  const emailExists = db.sent_emails.some(e => e.user_id === userId && e.subject.includes("wishlist"));
  if (!emailExists) {
    // Add custom notification 
    db.notifications.push({
      id: db.notifications.length > 0 ? Math.max(...db.notifications.map(n => n.id)) + 1 : 1,
      user_id: userId,
      title: "🛒 Items waiting in Cart!",
      message: `Don't forget! "${prod.title}" is in your wishlist. Order today with code SAVE50 to get 50% discount!`,
      is_read: 0,
      created_at: new Date().toISOString()
    });

    // Send email log 
    db.sent_emails.push({
      id: db.sent_emails.length > 0 ? Math.max(...db.sent_emails.map(e => e.id)) + 1 : 1,
      user_id: userId,
      email: user.email,
      subject: "You left something special in your DigitalMohan wishlist! 🛍️",
      body: `Hi ${user.name},\n\nWe noticed you left "${prod.title}" in your secure wishlist.\n\nDon't let it slip away! We have active promotions on formula sheets, resume packs, and Canva layouts today.\n\nApply code SAVE50 on the secure checkout link to claim an instant 50% Discount:\nhttps://${req.get("host")}/buy.php?id=${prod.id}\n\nDigitalMohan Support`,
      created_at: new Date().toISOString()
    });

    await saveDB(db);
  }

  res.json({
    has_abandoned: true,
    product: {
      id: prod.id,
      title: prod.title,
      price: prod.price,
      image: prod.image
    }
  });
});

// Get user's simulated received email inbox (for receipt/campaign review in app)
app.get("/api/emails/:email", async (req, res) => {
  const email = req.params.email;
  const db = await loadDB();
  if (!db.sent_emails) db.sent_emails = [];
  const filtered = db.sent_emails.filter(e => e.email.toLowerCase() === email.toLowerCase());
  res.json(filtered);
});

// Helper functions for template-compiling PHP files as HTML in Node.js
const fileContentCache = new Map<string, { content: string; mtime: number; lastChecked: number }>();

async function preprocessPhp(filePath: string): Promise<string> {
  try {
    const now = Date.now();
    const cached = fileContentCache.get(filePath);
    
    // Low latency RAM cache: Bypass file system checks completely if checked within last 2.5 seconds
    if (cached && (now - cached.lastChecked < 2500)) {
      return cached.content;
    }

    const stats = await fs.stat(filePath);
    
    // If stats match, return cached content and update checked timestamp
    if (cached && cached.mtime === stats.mtimeMs) {
      cached.lastChecked = now;
      return cached.content;
    }
    
    let content = await fs.readFile(filePath, "utf-8");
    
    // Regex to match: <?php include __DIR__ . '/path'; ?> or <?php require_once ... ?>
    const includeRegex = /<\?php\s*(?:include|require|include_once|require_once)\s+(?:__DIR__\s*\.\s*)?['"]([^'"]+)['"]\s*;\s*\?>/gi;
    
    let match;
    let iterations = 0;
    const maxIterations = 200;
    while (iterations < maxIterations) {
      iterations++;
      const rx = new RegExp(includeRegex);
      match = rx.exec(content);
      if (!match) break;
      
      const matchStr = match[0];
      const includePathRaw = match[1];
      const dir = path.dirname(filePath);
      const includePathRawClean = includePathRaw.startsWith("/") ? includePathRaw.substring(1) : includePathRaw;
      const includePath = path.resolve(dir, includePathRawClean);
      
      let includeContent = "";
      try {
        includeContent = await preprocessPhp(includePath);
      } catch (err: any) {
        console.error(`Error including file ${includePath} in ${filePath}:`, err.message);
        includeContent = `<!-- Error including ${includePathRaw} -->`;
      }
      
      // Use replacement function () => includeContent to bypass special '$' processing in JS string replace
      const originalLength = content.length;
      content = content.replace(matchStr, () => includeContent);
      
      // If replacement didn't change the content, break to avoid infinite loop
      if (content.length === originalLength && content.indexOf(matchStr) !== -1) {
        console.warn(`preprocessPhp: Infinite loop prevention triggered on ${filePath} for match: ${matchStr}`);
        break;
      }
    }
    if (iterations >= maxIterations) {
      console.warn(`preprocessPhp: Exceeded maximum iterations on ${filePath}`);
    }
    
    fileContentCache.set(filePath, { content, mtime: stats.mtimeMs, lastChecked: now });
    return content;
  } catch (err: any) {
    let content = await fs.readFile(filePath, "utf-8");
    return content;
  }
}

async function renderPhpFile(filePath: string, req: express.Request): Promise<string> {
  let content = await preprocessPhp(filePath);

  const cookies = req.headers.cookie || "";
  const isDark = cookies.includes("theme=dark");
  const currentTheme = isDark ? "dark" : "";
  const currentEyeTheme = isDark ? "fa-sun" : "fa-moon";
  
  const idQuery = req.query.id;
  const id = idQuery ? parseInt(idQuery as string) : 1;
  const pId = req.query.id ? parseInt(req.query.id as string) : 0;
  const uId = req.query.user ? parseInt(req.query.user as string) : 0;

  // Load dynamic data for PHP rendering
  const db = await loadDB();
  let bannersList = db.banners || [];
  if (bannersList.length === 0) {
    bannersList = [
      {
        id: 1,
        badge: "HOT SALE",
        title: "All Prompt Packs of Chat GPT",
        subtitle: "Boost production by 10x instantly. 100% Tested copy-paste directories.",
        link_url: "products.php?cat=1",
        bg_gradient: "from-indigo-900 to-sky-900"
      },
      {
        id: 2,
        badge: "NEW RELEASE",
        title: "ATS-Friendly Resumes",
        subtitle: "Pass screening tests. Recruiter-approved formatting sheets.",
        link_url: "products.php?cat=2",
        bg_gradient: "from-emerald-950 to-teal-850"
      }
    ];
  }
  const serverBanners = JSON.stringify(bannersList);
  const serverCategories = JSON.stringify(db.categories || []);
  
  // Performance Optimization: Return only first 12 active products inside embedded serverProducts block.
  // This avoids multi-megabyte HTML page sizes on 10,000+ products catalogues for fast initial under-2s load.
  const activeProducts = (db.products || []).filter((p: any) => !p.status || p.status === 'active');
  const serverProducts = JSON.stringify(activeProducts.slice(0, 12));

  const currentProduct = (db.products || []).find((p: any) => p.id === id) || (db.products && db.products[0]);
  const categoryName = currentProduct ? ((db.categories || []).find((c: any) => c.id === currentProduct.category_id)?.name || "Category") : "Category";
  const ssrTitle = currentProduct ? currentProduct.title : "Loading Product Specifications...";
  const ssrImage = currentProduct ? currentProduct.image : "https://images.unsplash.com/photo-1541963463532-d68292c34b19?w=600&q=80";
  const ssrDesc = currentProduct ? currentProduct.description : "File compilation outline details...";
  const ssrPrice = currentProduct ? currentProduct.price : 0;
  const ssrMrp = currentProduct ? currentProduct.mrp : 0;
  let ssrDiscount = (ssrMrp > 0) ? Math.round(((ssrMrp - ssrPrice) / ssrMrp) * 105) : 0;
  if (ssrDiscount > 99) ssrDiscount = 99;
  if (ssrDiscount <= 0) ssrDiscount = 0;

  // Substitute variables in <?= ... ?>
  content = content.replace(/<\?=\s*htmlspecialchars\(\$categoryName\)\s*\?>/g, () => categoryName);
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ssrTitle\)\s*\?>/g, () => ssrTitle);
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ssrImage\)\s*\?>/g, () => ssrImage);
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ssrDesc\)\s*\?>/g, () => ssrDesc);
  content = content.replace(/<\?=\s*\$currentProduct\s*\?\s*number_format\(\$ssrPrice,\s*2\)\s*:\s*['"]----['"]\s*\?>/g, () => ssrPrice.toFixed(2));
  content = content.replace(/<\?=\s*\$currentProduct\s*\?\s*number_format\(\$ssrMrp,\s*2\)\s*:\s*['"]----['"]\s*\?>/g, () => ssrMrp.toFixed(2));
  content = content.replace(/<\?=\s*\$ssrDiscount\s*\?>/g, () => ssrDiscount.toString());

  // Conditional logic and classes handling
  content = content.replace(/<\?php\s*echo\s*\$currentProduct\s*\?\s*['"]([^'"]*)['"]\s*:\s*['"]([^'"]*)['"]\s*;\s*\?>/g, (match, trueVal, falseVal) => {
    return currentProduct ? trueVal : falseVal;
  });
  content = content.replace(/<\?php\s*if\s*\(\s*!\$currentProduct\s*\)\s*:\s*\?>([\s\S]*?)<\?php\s*endif;\s*\?>/gi, (match, innerHtml) => {
    return !currentProduct ? innerHtml : "";
  });
  content = content.replace(/<\?php\s*if\s*\(\s*\$currentProduct\s*\)\s*:\s*\?>([\s\S]*?)<\?php\s*endif;\s*\?>/gi, (match, innerHtml) => {
    return currentProduct ? innerHtml : "";
  });

  // Host header replacement for structured schema JSON-LD
  content = content.replace(/<\?=\s*\$_SERVER\['HTTP_HOST'\]\s*\?\?\s*['"]([^'"]+)['"]\s*\?>/g, (match, defVal) => {
    return req.get("host") || defVal;
  });

  // Splash screen replacement
  const splashBg = isDark ? "#0b1329" : "linear-gradient(to bottom, #f8fafc, #edf2f7)";
  const splashTitle = isDark ? "#f8fafc" : "#0f172a";
  const splashSub = isDark ? "#64748b" : "#64748b";
  content = content.replace(/<\?=\s*\$splashBg\s*\?>/g, () => splashBg);
  content = content.replace(/<\?=\s*\$splashTitle\s*\?>/g, () => splashTitle);
  content = content.replace(/<\?=\s*\$splashSub\s*\?>/g, () => splashSub);

  content = content.replace(/<\?=\s*\$theme\s*===\s*['"]dark['"]\s*\?\s*['"]([^'"]+)['"]\s*:\s*['"]([^'"]*)['"]\s*\?>/gi, (match, darkVal, lightVal) => {
    return isDark ? darkVal : lightVal;
  });

  content = content.replace(/<\?=\s*\$theme\s*\?>/g, () => isDark ? "dark" : "light");
  content = content.replace(/<\?=\s*\$csrfToken\s*\?>/g, () => "mock_csrf_token_12345");
  content = content.replace(/<\?=\s*\$id\s*\?>/g, () => id.toString());
  content = content.replace(/<\?=\s*\$pId\s*\?>/g, () => pId.toString());
  content = content.replace(/<\?=\s*\$uId\s*\?>/g, () => uId.toString());
  content = content.replace(/<\?=\s*\$log\s*\?>/g, () => ""); // for install.php logs
  
  content = content.replace(/<\?=\s*\$serverBanners\s*\?>/g, () => serverBanners);
  content = content.replace(/<\?=\s*\$serverCategories\s*\?>/g, () => serverCategories);
  content = content.replace(/<\?=\s*\$serverProducts\s*\?>/g, () => serverProducts);

  // Load blog variables if we need to compile blogs
  const currentBlog = (db.blogs || []).find((b: any) => b.id === id) || (db.blogs && db.blogs[0]);
  const ssrBlogTitle = currentBlog ? currentBlog.title : "Advanced Guide Specs...";
  const ssrBlogImage = currentBlog ? currentBlog.image : "https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=800&q=80";
  const ssrBlogSummary = currentBlog ? currentBlog.summary : "";
  const ssrBlogContent = currentBlog ? currentBlog.content : "";
  const ssrBlogCategory = currentBlog ? currentBlog.category : "General";
  const ssrBlogAuthor = currentBlog ? currentBlog.author : "Mohan Mahali";
  const ssrBlogReadTime = currentBlog ? currentBlog.read_time : "5 min read";
  const ssrBlogDate = currentBlog ? new Date(currentBlog.created_at || Date.now()).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" }) : new Date().toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
  const ssrBlogDifficulty = ssrBlogTitle.toLowerCase().includes("ultimate") ? "Masterclass" : (ssrBlogTitle.toLowerCase().includes("hacks") ? "Beginner Friendly" : "Intermediate");

  let ctaBlogProductLink = "products.php";
  let ctaBlogHeadline = "Unlock Premium Resources";
  let ctaBlogText = "Get top-tier templates, prompt directories, and research sheets designed by industry experts.";
  let ctaBlogButtonText = "Browse Asset Store";
  if (currentBlog) {
    const catLower = ssrBlogCategory.toLowerCase();
    if (catLower.includes("career") || catLower.includes("resume")) {
      ctaBlogProductLink = "product_detail.php?id=2";
      ctaBlogHeadline = "Premium ATS-Friendly Resume Template";
      ctaBlogText = "Score 3x more recruiters response clicks. Download our clean resume layouts that pass modern automated scanner filters instantly.";
      ctaBlogButtonText = "Download Template (₹99)";
    } else if (catLower.includes("ai") || catLower.includes("prompt")) {
      ctaBlogProductLink = "product_detail.php?id=1";
      ctaBlogHeadline = "ChatGPT Mega Prompt Pack (10,000+ Master Prompts)";
      ctaBlogText = "Unlock our certified system templates. Effortlessly automate writing, marketing campaigns, and python engineering streams.";
      ctaBlogButtonText = "Unlock Master Pack (₹299)";
    } else if (catLower.includes("education") || catLower.includes("jee") || catLower.includes("notes")) {
      ctaBlogProductLink = "product_detail.php?id=3";
      ctaBlogHeadline = "IIT-JEE Physics Revision Formulas Guide";
      ctaBlogText = "Quick-retention guides designed by top IITian mentors to maximize performance under exam stress.";
      ctaBlogButtonText = "Download revision sheets FREE";
    }
  }

  // Blog replacements
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ssrCategory\)\s*\?>/g, () => ssrBlogCategory);
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ssrTitle\)\s*\?>/g, () => ssrBlogTitle);
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ssrImage\)\s*\?>/g, () => ssrBlogImage);
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ssrSummary\)\s*\?>/g, () => ssrBlogSummary);
  content = content.replace(/<\?=\s*nl2br\(htmlspecialchars\(\$ssrContent\)\)\s*\?>/g, () => ssrBlogContent.replace(/\n/g, "<br>"));
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ssrAuthor\)\s*\?>/g, () => ssrBlogAuthor);
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ssrReadTime\)\s*\?>/g, () => ssrBlogReadTime);
  content = content.replace(/<\?=\s*strtoupper\(substr\(\$ssrAuthor,\s*0,\s*1\)\)\s*\?>/g, () => ssrBlogAuthor.charAt(0).toUpperCase());
  content = content.replace(/<\?=\s*\$ssrDifficulty\s*\?>/g, () => ssrBlogDifficulty);
  content = content.replace(/<\?=\s*\$ssrDate\s*\?>/g, () => ssrBlogDate);
  
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ctaHeadline\)\s*\?>/g, () => ctaBlogHeadline);
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ctaText\)\s*\?>/g, () => ctaBlogText);
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ctaProductLink\)\s*\?>/g, () => ctaBlogProductLink);
  content = content.replace(/<\?=\s*htmlspecialchars\(\$ctaButtonText\)\s*\?>/g, () => ctaBlogButtonText);
  
  // Replace blog details json_encode blocks inside Javascript
  content = content.replace(/<\?=\s*json_encode\(\$ssrTitle\)\s*\?>/g, () => JSON.stringify(ssrBlogTitle));

  // Strip or replace any remaining php short echo blocks with valid safe literals to prevent JS script tag breaks
  content = content.replace(/<\?=[\s\S]*?\?>/g, '""');

  // Strip any remaining php blocks that might leak raw code to the browser
  content = content.replace(/<\?php[\s\S]*?\?>/g, "");
  content = content.replace(/<\?[\s\S]*?\?>/g, "");

  return content;
}

// Fallback for any unmatched API requests to prevent returning HTML, avoiding browser JSON parse errors
app.all("/api/*", (req, res) => {
  res.status(404).json({ error: `API route ${req.method} ${req.path} not found`, success: false });
});

// Vite & Static file configurations
async function startServer() {
  // Direct client request intercepts for root and simple PHP pages in Express
  app.get("*", async (req, res, next) => {
    const urlPath = req.path;
    
    // Ignore API routes
    if (urlPath.startsWith("/api/") || urlPath.includes("api.php")) {
      return next();
    }

    const isPhp = urlPath.endsWith(".php");
    const isRoot = urlPath === "/" || urlPath === "/index.html";

    if (!isPhp && !isRoot) {
      return next();
    }

    let filePath = "";
    if (isRoot) {
      filePath = path.join(process.cwd(), "index.php");
    } else {
      filePath = path.join(process.cwd(), urlPath);
    }

    try {
      const stats = await fs.stat(filePath);
      if (stats.isFile()) {
        const compiledHtml = await renderPhpFile(filePath, req);
        res.setHeader("Content-Type", "text/html; charset=utf-8");
        return res.send(compiledHtml);
      }
    } catch (err: any) {
      // PHP file does not exist, fall through to Vite static/SPA fallback
    }
    
    next();
  });

  if (process.env.NODE_ENV !== "production") {
    // Serve manifest.json, service-worker.js, and other static assets directly in development to avoid SPA fallbacks to index.html
    app.get("*", async (req, res, next) => {
      const urlPath = req.path;
      const ext = path.extname(urlPath);
      
      const isExcludedDir = urlPath.startsWith("/src/") || urlPath.startsWith("/node_modules/") || urlPath.startsWith("/@");
      
      if (ext && ext !== ".php" && ext !== ".html" && !isExcludedDir) {
        const rootFilePath = path.join(process.cwd(), urlPath);
        try {
          const stats = await fs.stat(rootFilePath);
          if (stats.isFile()) {
            return res.sendFile(rootFilePath);
          }
        } catch (e) {
          // Keep cascading
        }
      }
      next();
    });

    const vite = await createViteServer({
      server: { middlewareMode: true },
      appType: "spa",
    });
    app.use(vite.middlewares);
  } else {
    const distPath = path.join(process.cwd(), "dist");
    app.use(express.static(distPath));
    // Support serving static resources from the project root (like manifest.json, service worker, etc.)
    app.use(express.static(process.cwd(), { index: false }));
    app.get("*", (req, res) => {
      const ext = path.extname(req.path);
      if (ext && ext !== ".html" && ext !== ".php") {
        return res.status(404).send("Not Found");
      }
      res.sendFile(path.join(distPath, "index.html"));
    });
  }

  app.listen(PORT, "0.0.0.0", () => {
    console.log(`Server running on port ${PORT}`);
  });
}

startServer();

