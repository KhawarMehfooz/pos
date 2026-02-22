export * from './auth';
export * from './navigation';
export * from './ui';

export interface Category {
  id: number
  category_name: string
  parent_id: number | null
}

export interface Product {
  id: number
  product_name: string
  category_id: number | null
  purchase_price: number
  retail_price: number
  sale_price: number | null
  product_image?: string | null
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
