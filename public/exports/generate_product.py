import pandas as pd
import os

# Đường dẫn đến file Excel - điều chỉnh cho phù hợp
excel_file_path = 'D:/KyV_HocVienNganHang/PHP/Final_KT2/products_export_2025-11-06_083426.xlsx'


# Kiểm tra xem file có tồn tại không
if not os.path.exists(excel_file_path):
    print(f"File {excel_file_path} không tồn tại!")
    print("Vui lòng kiểm tra đường dẫn hoặc đặt file Excel trong cùng thư mục với script.")
    
    # Tạo dữ liệu mới từ đầu nếu file không tồn tại
    print("Tạo dữ liệu sản phẩm mới từ đầu...")
    existing_data = pd.DataFrame(columns=[
        'Product SKU', 'Product Name', 'Product Description', 
        'Variant Color', 'Variant Storage', 'Variant Price', 'Variant Stock'
    ])
else:
    # Đọc dữ liệu hiện có từ file Excel
    existing_data = pd.read_excel(excel_file_path)

# Tạo danh sách các sản phẩm mới
new_products = []

# Danh sách SKU đã tồn tại
existing_skus = set(existing_data['Product SKU'].str.upper()) if not existing_data.empty else set()

# Dữ liệu mẫu cho sản phẩm mới
new_products_data = [
    {
        'sku': 'IP15',
        'name': 'iPhone 15',
        'desc': 'iPhone 15 với thiết kế Dynamic Island và camera 48MP',
        'variants': [
            {'color': 'Đen', 'storage': '128GB', 'price': 21990000.0, 'stock': 45},
            {'color': 'Xanh dương', 'storage': '128GB', 'price': 21990000.0, 'stock': 38},
            {'color': 'Hồng', 'storage': '256GB', 'price': 23990000.0, 'stock': 32}
        ]
    },
    {
        'sku': 'IP15PLUS',
        'name': 'iPhone 15 Plus',
        'desc': 'iPhone 15 Plus màn hình lớn, pin trâu cả ngày',
        'variants': [
            {'color': 'Đen', 'storage': '128GB', 'price': 24990000.0, 'stock': 28},
            {'color': 'Xanh lá', 'storage': '256GB', 'price': 26990000.0, 'stock': 22}
        ]
    },
    {
        'sku': 'GALAXYA55',
        'name': 'Samsung Galaxy A55',
        'desc': 'Samsung Galaxy A55 hiệu năng ổn định, camera sắc nét',
        'variants': [
            {'color': 'Đen', 'storage': '128GB', 'price': 8990000.0, 'stock': 60},
            {'color': 'Xanh dương', 'storage': '128GB', 'price': 8990000.0, 'stock': 55},
            {'color': 'Tím', 'storage': '256GB', 'price': 9990000.0, 'stock': 40}
        ]
    },
    {
        'sku': 'XIAOMI14',
        'name': 'Xiaomi 14',
        'desc': 'Xiaomi 14 với camera Leica, hiệu năng flagship',
        'variants': [
            {'color': 'Đen', 'storage': '256GB', 'price': 16990000.0, 'stock': 35},
            {'color': 'Trắng', 'storage': '256GB', 'price': 16990000.0, 'stock': 30},
            {'color': 'Xanh lá', 'storage': '512GB', 'price': 18990000.0, 'stock': 25}
        ]
    },
    {
        'sku': 'OPPORENO10',
        'name': 'OPPO Reno10',
        'desc': 'OPPO Reno10 thiết kế thời trang, camera chân dung ấn tượng',
        'variants': [
            {'color': 'Đen', 'storage': '128GB', 'price': 10990000.0, 'stock': 42},
            {'color': 'Xanh dương', 'storage': '256GB', 'price': 11990000.0, 'stock': 36}
        ]
    },
    {
        'sku': 'VIVOV30',
        'name': 'vivo V30',
        'desc': 'vivo V30 camera selfie 50MP, thiết kế mỏng nhẹ',
        'variants': [
            {'color': 'Đen', 'storage': '128GB', 'price': 9990000.0, 'stock': 48},
            {'color': 'Hồng', 'storage': '256GB', 'price': 10990000.0, 'stock': 33}
        ]
    },
    {
        'sku': 'MACBOOKPRO14',
        'name': 'MacBook Pro 14 inch',
        'desc': 'MacBook Pro 14 inch với chip M3, màn hình Liquid Retina XDR',
        'variants': [
            {'color': 'Space Gray', 'storage': '512GB', 'price': 42990000.0, 'stock': 12},
            {'color': 'Bạc', 'storage': '1TB', 'price': 48990000.0, 'stock': 8}
        ]
    },
    {
        'sku': 'IPADAIR6',
        'name': 'iPad Air 6',
        'desc': 'iPad Air 6 với chip M1, màn hình Liquid Retina',
        'variants': [
            {'color': 'Xanh dương', 'storage': '64GB', 'price': 15990000.0, 'stock': 25},
            {'color': 'Xám', 'storage': '256GB', 'price': 19990000.0, 'stock': 18}
        ]
    },
    {
        'sku': 'GALAXYTABS9',
        'name': 'Samsung Galaxy Tab S9',
        'desc': 'Samsung Galaxy Tab S9 với S-Pen, màn hình AMOLED 120Hz',
        'variants': [
            {'color': 'Đen', 'storage': '128GB', 'price': 17990000.0, 'stock': 15},
            {'color': 'Beige', 'storage': '256GB', 'price': 19990000.0, 'stock': 10}
        ]
    },
    {
        'sku': 'SURFACELAPTOP5',
        'name': 'Microsoft Surface Laptop 5',
        'desc': 'Surface Laptop 5 thiết kế sang trọng, hiệu năng mượt mà',
        'variants': [
            {'color': 'Đen', 'storage': '256GB', 'price': 28990000.0, 'stock': 14},
            {'color': 'Bạch kim', 'storage': '512GB', 'price': 32990000.0, 'stock': 9}
        ]
    }
]

# Kiểm tra và thêm sản phẩm mới (chỉ thêm nếu SKU chưa tồn tại)
for product in new_products_data:
    if product['sku'].upper() not in existing_skus:
        for variant in product['variants']:
            new_products.append({
                'Product SKU': product['sku'],
                'Product Name': product['name'],
                'Product Description': product['desc'],
                'Variant Color': variant['color'],
                'Variant Storage': variant['storage'],
                'Variant Price': variant['price'],
                'Variant Stock': variant['stock']
            })
        existing_skus.add(product['sku'].upper())

# Tạo DataFrame từ danh sách sản phẩm mới
new_products_df = pd.DataFrame(new_products)

# Kết hợp với dữ liệu hiện có
combined_df = pd.concat([existing_data, new_products_df], ignore_index=True)

# Lưu file Excel mới
output_filename = 'products_new.xlsx'
new_products_df.to_excel(output_filename, index=False)

print(f"Đã tạo thành công {len(new_products)} biến thể sản phẩm mới!")
print(f"Tổng số dòng dữ liệu sau khi thêm: {len(combined_df)}")
print(f"File đã được lưu thành: {output_filename}")

# Hiển thị thống kê
print("\n" + "="*50)
print("THỐNG KÊ CHI TIẾT")
print("="*50)
print(f"Số dòng dữ liệu ban đầu: {len(existing_data)}")
print(f"Số dòng dữ liệu mới thêm vào: {len(new_products)}")
print(f"Số dòng dữ liệu tổng cộng: {len(combined_df)}")

# Hiển thị các sản phẩm mới đã thêm
print("\n" + "="*50)
print("DANH SÁCH SẢN PHẨM MỚI ĐÃ THÊM")
print("="*50)

# Nhóm theo SKU để hiển thị đẹp hơn
if not new_products_df.empty:
    unique_new_skus = new_products_df['Product SKU'].unique()
    for sku in unique_new_skus:
        product_info = new_products_df[new_products_df['Product SKU'] == sku].iloc[0]
        variants = new_products_df[new_products_df['Product SKU'] == sku]
        
        print(f"\nSKU: {sku}")
        print(f"Tên: {product_info['Product Name']}")
        print(f"Mô tả: {product_info['Product Description']}")
        print("Biến thể:")
        for _, variant in variants.iterrows():
            print(f"  - Màu: {variant['Variant Color']}, Storage: {variant['Variant Storage']}, "
                  f"Giá: {variant['Variant Price']:,.0f}, Tồn kho: {variant['Variant Stock']}")
else:
    print("Không có sản phẩm mới nào được thêm vào.")

print("\n" + "="*50)
print("XUẤT FILE EXCEL THÀNH CÔNG!")
print("="*50)