export interface Category {
  id: number;
  name: string;
}

export interface Product {
  id: number;
  category_id: number;
  title: string;
  mrp: number;
  price: number;
  description: string;
  file: string;
  image: string;
  status: "active" | "inactive";
  created_at: string;
  preview_url?: string;
  preview_type?: string;
  preview_data?: string;
}

export interface User {
  id: number;
  name: string;
  phone: string;
  email: string;
  password?: string;
  status: "active" | "blocked";
  created_at: string;
}

export interface Order {
  id: number;
  user_id: number;
  product_id: number;
  amount: number;
  status: "pending" | "successful" | "failed";
  razorpay_order_id: string;
  created_at: string;
}

export interface Coupon {
  id: number;
  code: string;
  discount: number;
  expiry: string;
  usage_limit: number;
  used_count: number;
}

export interface Settings {
  id: number;
  app_name: string;
  razorpay_key: string;
  razorpay_secret: string;
  support_email: string;
  support_phone: string;
  theme_color: string;
  maintenance_mode: number;
}

export interface Review {
  id: number;
  user_id: number;
  product_id: number;
  rating: number;
  comment: string;
  user_name?: string;
  created_at: string;
}

export interface WishlistItem {
  id: number;
  user_id: number;
  product_id: number;
  created_at: string;
}

export interface NotificationItem {
  id: number;
  user_id: number;
  title: string;
  message: string;
  is_read: number;
  created_at: string;
}
