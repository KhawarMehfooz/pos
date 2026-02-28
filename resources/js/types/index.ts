export * from './auth';
export * from './navigation';
export * from './ui';

export interface PaginationLink {
  url: string | null,
  label: string,
  active: boolean
}

export interface Paginated<T> {
  data: T[],
  current_page: number,
  last_page: number,
  per_page: number,
  total: number,
  links: PaginationLink[]
}

export interface Category {
  id: number
  category_name: string
  parent_id: number | null,
  children: Category[]
}

export interface Product {
  id: number
  product_name: string
  category_id: number | null
  purchase_price: number
  retail_price: number
  sale_price: number | null
  product_image?: string | null,
  product_image_url?: string,
  barcode?: string | null
  sku?: string | null
  track_stock: boolean
  stock_quantity: number
  min_stock_level: number
}

export interface Customer {
  id: number
  customer_name: string
  customer_phone?: string | null
  customer_email?: string | null
}

export interface CartItem {
  product: Product;
  quantity: number;
}
