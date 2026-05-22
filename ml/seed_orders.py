import mysql.connector
import random
from datetime import datetime, timedelta


# Konfigurasi database

DB_CONFIG = {
    'host': '127.0.0.1',
    'user': 'root',
    'password': '',
    'database': 'salsabila_delivery'
}

# Menu asli Warung Nasi Salsabilla Al Arief
MENU_MAKANAN = [
    'Nasgor Kebuli Sapi', 'Nasgor Kebuli Katsu', 'Nasgor Kebuli Ayam',
    'Nasi Mie Goreng Ayam Geprek', 'Nasi Mie Goreng Ayam Bakar',
    'Nasi Mie Goreng Ayam Goreng', 'Kentang/Nasi Katsu Salad',
    'Kentang/Nasi Katsu', 'Kentang/Nasi Cheese', 'Kentang/Nasi Cheese Salad',
    'Nasi Ayam Geprek Gobyos+Tempe', 'Nasi Ayam Penyet Bakar+Tempe',
    'Nasi Ayam Penyet Goreng+Tempe', 'Nasi Lele Penyet+Tempe',
    'Nasi SFC+Tempe', 'Kentang Goreng Saus', 'Nasi Soto Babat',
    'Nasi Soto Ayam', 'Kenplingju', 'Nasi Soto Sapi'
]

MENU_MINUMAN = [
    'Es Teh Manis', 'Teh Tawar', 'Jeruk Es', 'Jeruk Hangat',
    'Teh Susu Es', 'Kopi Hitam', 'Es Laguna Salsabilla',
    'Chocolatos Drink', 'Air Mineral', 'Lemon Tea Es'
]

MENU_TAMBAHAN = [
    'Telor Dadar', 'Kerupuk Udang', 'Tahu/Tempe Goreng',
    'Bacem Tahu/Tempe', 'Sambal'
]

CUSTOMER_NAMES = [
    'Budi', 'Siti', 'Andi', 'Dewi', 'Reza', 'Putri',
    'Fajar', 'Nita', 'Doni', 'Lina', 'Hendra', 'Maya',
    'Bagas', 'Rizki', 'Anggi', 'Dimas', 'Sari', 'Yogi',
    'Tania', 'Arif', 'Bella', 'Cahya', 'Daffa', 'Elsa'
]

def generate_order_items():
    items = []
    makanan = random.sample(MENU_MAKANAN, random.randint(1, 2))
    items.extend(makanan)
    if random.random() < 0.8:
        items.append(random.choice(MENU_MINUMAN))
    if random.random() < 0.4:
        items.append(random.choice(MENU_TAMBAHAN))
    return ', '.join(items)

def generate_duration(jam):
    """
    Durasi realistis berdasarkan kondisi nyata warung:
    - Jam 16:30-19:00 = sangat ramai, bisa 45-70 menit
    - Jam 11:00-13:00 = ramai, 35-60 menit
    - Jam lain = normal, 15-35 menit
    """
    if 16 <= jam <= 19:
        # Jam sibuk sore/malam - paling lama
        # 16:30-19:00 bisa sampai 1 jam lebih
        return random.randint(40, 70)
    elif 11 <= jam <= 13:
        # Jam sibuk siang
        return random.randint(30, 60)
    elif jam in [10, 14, 15]:
        # Agak ramai
        return random.randint(20, 40)
    else:
        # Sepi (pagi/malam)
        return random.randint(15, 30)

def generate_orders(n=30):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()

    cursor.execute("SELECT order_number FROM orders ORDER BY id DESC LIMIT 1")
    last = cursor.fetchone()
    last_num = int(last[0].split('-')[1]) if last else 0

    now = datetime.now()
    inserted = 0
    telat_count = 0
    tepat_count = 0

    # Distribusi jam lebih banyak di jam sibuk biar datanya realistis
    jam_pool = (
        [7, 8, 9, 10] * 2 +        # pagi - lebih sedikit
        [11, 12, 13] * 5 +          # siang - ramai
        [14, 15] * 2 +              # sore awal - normal
        [16, 17, 18, 19] * 6 +      # sore/malam - paling ramai
        [20] * 1                    # malam tutup
    )

    for i in range(n):
        num = last_num + i + 1
        order_number = f'ORD-{str(num).zfill(3)}'
        customer = random.choice(CUSTOMER_NAMES)
        items = generate_order_items()

        jam = random.choice(jam_pool)
        menit = random.randint(0, 59)
        if jam == 7:
            menit = random.randint(30, 59)
        if jam == 20:
            menit = 0
        order_time = f'{jam:02d}:{menit:02d}'

        duration = generate_duration(jam)
        jumlah_antrian = random.randint(1, 15)
        if 16 <= jam <= 19 or 11 <= jam <= 13:
            jumlah_antrian = random.randint(5, 15)  # antrian lebih banyak di jam sibuk

        created_at = now.replace(hour=jam, minute=menit, second=random.randint(0, 59), microsecond=0)
        started_at = created_at + timedelta(minutes=random.randint(1, 3))
        completed_at = started_at + timedelta(minutes=duration)

        estimated_duration = random.choice([20, 25, 30, 35])
        is_late = 1 if duration > 35 else 0  # telat kalau > 35 menit

        # Prediction
        skor = 0
        if 16 <= jam <= 19:
            skor += 3  # jam sore paling sibuk
        elif 11 <= jam <= 13:
            skor += 2  # jam siang sibuk
        if jumlah_antrian >= 8:
            skor += 1
        if duration > 35:
            skor += 1
        prediction = 'Telat' if skor >= 3 else 'Tepat'

        if prediction == 'Telat':
            telat_count += 1
        else:
            tepat_count += 1

        cursor.execute("""
            INSERT INTO orders
            (order_number, customer_name, order_items, status, order_time,
             started_at, completed_at, duration_minutes, estimated_duration,
             is_late, prediction, created_at, updated_at)
            VALUES (%s, %s, %s, 'completed', %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """, (
            order_number, customer, items, order_time,
            started_at, completed_at, duration,
            estimated_duration, is_late, prediction,
            created_at, completed_at
        ))
        inserted += 1

    conn.commit()
    cursor.close()
    conn.close()

    print(f"Berhasil insert {inserted} order testing!")
    print(f"Prediksi Tepat: {tepat_count} | Telat: {telat_count}")
    print(f"Order: ORD-{str(last_num+1).zfill(3)} s/d ORD-{str(last_num+n).zfill(3)}")

if __name__ == '__main__':
    generate_orders(30)